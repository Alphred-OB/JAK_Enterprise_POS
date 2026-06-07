<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index()
    {
        return view('manager.backup.index');
    }

    public function download(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Backup download denied.']);
        }

        Log::info('Database backup downloaded', [
            'user_id'    => auth()->id(),
            'user_email' => auth()->user()->email,
            'ip'         => $request->ip(),
        ]);

        $shopName = str_replace(' ', '_', config('app.name', 'JAK_POS'));
        // Example output: JAK_POS_Database_Backup_-_07-May-2026_-_07-17-PM.sql
        $fileName = "{$shopName}_Database_Backup_-_" . now()->format('d-M-Y_-_h-i-A') . ".sql";
        
        $headers = [
            "Content-type" => "application/sql",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_" . $dbName;

        $callback = function() use ($tables, $tableKey) {
            $file = fopen('php://output', 'w');
            
            fwrite($file, "-- JAK POS Database Backup\n");
            fwrite($file, "-- Generated: " . now()->toDateTimeString() . "\n\n");
            fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get table structure
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0];
                fwrite($file, "DROP TABLE IF EXISTS `$tableName`;\n");
                fwrite($file, $createTable->{'Create Table'} . ";\n\n");

                // Stream rows in chunks to avoid loading entire table into memory
                $firstChunk = true;
                DB::table($tableName)->orderByRaw('1')->chunk(500, function ($rows) use ($file, $tableName, &$firstChunk) {
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        if ($firstChunk) {
                            $columns = array_keys($rowArray);
                            $firstChunk = false;
                        }
                        $values = array_map(function ($value) {
                            if (is_null($value)) return 'NULL';
                            return "'" . addslashes((string) $value) . "'";
                        }, array_values($rowArray));
                        fwrite($file, "INSERT INTO `$tableName` (`" . implode('`, `', array_keys($rowArray)) . "`) VALUES (" . implode(', ', $values) . ");\n");
                    }
                });
                fwrite($file, "\n");
            }

            fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
