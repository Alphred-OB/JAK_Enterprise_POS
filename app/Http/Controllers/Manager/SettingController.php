<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();
        return view('manager.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_address' => 'nullable|string|max:255',
            'shop_phone' => 'nullable|string|max:20',
            'currency_symbol' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = Setting::first();
        $data = $request->only(['shop_name', 'shop_address', 'shop_phone', 'currency_symbol']);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->shop_logo) {
                Storage::disk('public')->delete($settings->shop_logo);
            }
            $path = $request->file('logo')->store('shop', 'public');
            $data['shop_logo'] = $path;
        }

        $settings->update($data);

        return redirect()->route('admin.settings.index')->with('success', 'Shop settings updated successfully.');
    }
}
