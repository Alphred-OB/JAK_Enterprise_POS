<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Summary Report - {{ $settings->shop_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
            .report-card { border: none !important; shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-12">
    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-[32px] md:rounded-[40px] p-6 md:p-12 report-card border border-slate-100">
        <!-- Report Header -->
        <header class="flex flex-col md:flex-row justify-between items-start mb-12 md:mb-16 border-b border-slate-100 pb-8 md:pb-12 gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">Business Summary</h1>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Generated on {{ now()->format('d M, Y • h:i A') }}</p>
            </div>
            <div class="md:text-right">
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">{{ $settings->shop_name }}</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase mt-1">{{ $settings->shop_address ?? 'Official Performance Report' }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $settings->shop_phone ?? '' }}</p>
            </div>
        </header>

        <!-- Time Range -->
        <div class="bg-slate-900 text-white rounded-3xl p-8 mb-12 flex flex-col md:flex-row justify-between items-center shadow-2xl shadow-slate-200 gap-6">
            <div class="text-center md:text-left">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Reporting Period</span>
                <p class="text-xl font-black uppercase">{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</p>
            </div>
            <button onclick="window.print()" class="no-print w-full md:w-auto bg-white text-slate-900 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-500 hover:text-white transition-all shadow-lg">Print / Save PDF</button>
        </div>

        <!-- Financial Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-16">
            <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3 text-center">Total Revenue</span>
                <p class="text-2xl font-black text-slate-900 text-center tabular">{{ $settings->currency_symbol }} {{ number_format($todaySales, 2) }}</p>
            </div>
            <div class="p-8 bg-slate-50 rounded-3xl border border-slate-100">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-3 text-center">Operational Costs</span>
                <p class="text-2xl font-black text-red-600 text-center tabular">{{ $settings->currency_symbol }} {{ number_format($todayExpenses, 2) }}</p>
            </div>
            <div class="p-8 bg-blue-600 rounded-3xl shadow-xl shadow-blue-100 sm:col-span-2 lg:col-span-1">
                <span class="text-[9px] font-black text-white/60 uppercase tracking-widest block mb-3 text-center">Estimated Profit</span>
                <p class="text-2xl font-black text-white text-center tabular">{{ $settings->currency_symbol }} {{ number_format($todayProfit - $todayExpenses, 2) }}</p>
            </div>
        </div>

        <!-- Details Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Top Selling Items -->
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                    <span class="w-2 h-2 bg-slate-900 rounded-full"></span>
                    Best Selling Items
                </h3>
                <div class="space-y-4">
                    @foreach($topProducts as $item)
                        <div class="flex justify-between items-center py-4 border-b border-slate-50">
                            <span class="text-[11px] font-black text-slate-700 uppercase">{{ $item->product->name }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item->total_qty }} units sold</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Staff Performance -->
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                    <span class="w-2 h-2 bg-slate-900 rounded-full"></span>
                    Staff Contributions
                </h3>
                <div class="space-y-4">
                    @foreach($staffPerformance as $staff)
                        <div class="flex justify-between items-center py-4 border-b border-slate-50">
                            <span class="text-[11px] font-black text-slate-700 uppercase">{{ $staff->user->name }}</span>
                            <span class="text-[11px] font-black text-slate-900 tabular">{{ $settings->currency_symbol }} {{ number_format($staff->total_sales, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <footer class="mt-20 pt-12 border-t border-slate-100 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">EndOfReport • Valid for Administrative Use Only</p>
        </footer>
    </div>
    
    <div class="max-w-4xl mx-auto mt-8 flex justify-center no-print">
        <a href="{{ route('manager.dashboard') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Dashboard
        </a>
    </div>
</body>
</html>
