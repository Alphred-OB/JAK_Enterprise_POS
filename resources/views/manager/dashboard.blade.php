@extends('layouts.manager')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <!-- Header: Simple & Clear -->
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-12 gap-6">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Shop Overview</h1>
            <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mt-1">
                @if(request('start_date') && request('end_date'))
                    Performance from {{ \Carbon\Carbon::parse(request('start_date'))->format('d M') }} to {{ \Carbon\Carbon::parse(request('end_date'))->format('d M, Y') }}
                @else
                    Today's Performance • {{ now()->format('l, d F Y') }}
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- Date Filter Form -->
            <form action="{{ route('manager.dashboard') }}" method="GET" class="w-full md:w-auto bg-white p-1.5 rounded-[20px] border border-slate-100 shadow-sm flex flex-wrap items-center gap-2">
                <div class="flex flex-1 items-center gap-2 min-w-[200px]">
                    <input type="date" name="start_date" value="{{ request('start_date', now()->format('Y-m-d')) }}" class="flex-1 bg-slate-50 border-transparent rounded-xl py-2 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-[10px] uppercase">
                    <span class="text-slate-300 font-black text-[10px]">TO</span>
                    <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="flex-1 bg-slate-50 border-transparent rounded-xl py-2 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-[10px] uppercase">
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </button>
                    <div x-data="{ open: false }" class="relative flex-1 md:flex-none">
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full h-10 px-4 flex items-center justify-center gap-2 bg-slate-900 text-white rounded-xl hover:bg-blue-600 transition-all shadow-sm text-[10px] font-black uppercase tracking-widest">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            <span class="hidden sm:inline">Export Data</span>
                            <span class="sm:hidden">Export</span>
                        </button>
                        
                        <div x-show="open" x-transition.opacity class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl shadow-slate-200 border border-slate-100 overflow-hidden z-50 p-2" style="display: none;">
                            <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-3 py-2 mb-1">Downloads</div>
                            <a href="{{ route('manager.report', request()->all()) }}" target="_blank" class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all">
                                PDF Summary Report
                            </a>
                            <a href="{{ route('manager.export.sales', request()->all()) }}" class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all">
                                Sales (CSV)
                            </a>
                            <a href="{{ route('manager.export.expenses', request()->all()) }}" class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all">
                                Expenses (CSV)
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <a href="{{ route('pos.index') }}" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center justify-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Open Cashier
            </a>
        </div>
    </header>

    <!-- TOP ROW: MONEY SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <!-- Sales -->
        <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-[20px] flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Sales Today</span>
            <h3 class="text-3xl font-black text-slate-900 mt-2 tabular tracking-tighter">GH₵ {{ number_format($todaySales, 2) }}</h3>
        </div>

        <!-- Profit -->
        <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group">
            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-[20px] flex items-center justify-center mb-6 group-hover:bg-green-600 group-hover:text-white transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Profit Made Today</span>
            <h3 class="text-3xl font-black text-slate-900 mt-2 tabular tracking-tighter">GH₵ {{ number_format($todayProfit, 2) }}</h3>
        </div>

        <!-- Net Take-Home -->
        <div class="bg-slate-900 rounded-[40px] p-8 shadow-2xl shadow-slate-200 hover:-translate-y-1 transition-all relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="w-14 h-14 bg-blue-600 text-white rounded-[20px] flex items-center justify-center mb-6 relative z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] relative z-10">Actual Money (After Expenses)</span>
            <h3 class="text-3xl font-black text-white mt-2 tabular tracking-tighter relative z-10">GH₵ {{ number_format($netProfit, 2) }}</h3>
            <p class="text-[9px] font-black text-slate-500 uppercase mt-4 relative z-10">Subtracted GH₵ {{ number_format($todayExpenses, 2) }} in costs</p>
        </div>

        <!-- Stock Assets -->
        <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group">
            <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-[20px] flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Value of All Stock</span>
            <h3 class="text-3xl font-black text-slate-900 mt-2 tabular tracking-tighter">GH₵ {{ number_format($inventoryValue, 2) }}</h3>
        </div>
    </div>

    <!-- MAIN BODY: SHOP DETAILS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT COLUMN: CHARTS & TOP ITEMS -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Sales Chart -->
            <div class="bg-white rounded-[48px] p-10 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tight">Sales This Week</h4>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Graph showing money made over the last 14 days</p>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Payment Breakdown -->
                <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8">How Money Was Received</h4>
                    <div class="space-y-6">
                        @forelse($paymentBreakdown as $payment)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full {{ $payment->payment_method == 'cash' ? 'bg-green-500' : ($payment->payment_method == 'momo' ? 'bg-blue-500' : 'bg-amber-500') }}"></div>
                                    <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest">{{ $payment->payment_method }} Payments</span>
                                </div>
                                <span class="text-sm font-black text-slate-900 tabular">GH₵ {{ number_format($payment->total, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-xs font-bold text-slate-400 py-4 italic">No payments yet today.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Best Sellers -->
                <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-8">Fastest Selling Items</h4>
                    <div class="space-y-6">
                        @forelse($topProducts as $item)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-black text-slate-900 uppercase truncate leading-tight">{{ $item->product->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">{{ $item->product->category->name ?? 'Category' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-lg tabular">{{ $item->total_qty }} Sold</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs font-bold text-slate-400 py-4 italic">Waiting for sales...</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: ALERTS & STAFF -->
        <div class="space-y-8">
            <!-- Active Shift Details -->
            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Staff Currently Working</h4>
                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[9px] font-black rounded-full uppercase">{{ $activeShifts->count() }} ON DUTY</span>
                </div>
                <div class="space-y-6">
                    @forelse($activeShifts as $shift)
                        <div class="flex items-center gap-4 p-4 rounded-[24px] bg-slate-50 border border-slate-100">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 font-black text-slate-900 uppercase">
                                {{ substr($shift->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-black text-slate-900 truncate uppercase tracking-tight">{{ $shift->user->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">Logged in {{ $shift->opened_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 opacity-30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-[10px] font-black uppercase tracking-widest">No one is working now</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Staff Performance Leaderboard -->
            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Team Leaderboard</h4>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <div class="space-y-6">
                    @forelse($staffPerformance as $performance)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-center font-black text-[10px] text-slate-900 uppercase group-hover:bg-slate-900 group-hover:text-white transition-all">
                                    {{ substr($performance->user->name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-black text-slate-900 uppercase truncate leading-tight">{{ $performance->user->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">{{ $performance->transaction_count }} Sales Made</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-slate-900 tabular">GH₵ {{ number_format($performance->total_sales, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 opacity-30">
                            <p class="text-[10px] font-black uppercase tracking-widest">No sales records yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Safety Alerts: Suspicious Activity -->
            <div class="bg-white rounded-[40px] p-8 border border-red-50 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-2xl"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <h4 class="text-sm font-black text-red-600 uppercase tracking-[0.2em]">Safety Alerts</h4>
                    <div class="w-2 h-2 bg-red-600 rounded-full animate-ping"></div>
                </div>
                <div class="space-y-6 relative z-10">
                    @forelse($suspiciousActivities as $activity)
                        <div class="flex gap-4 group">
                            <div class="flex items-center gap-4 shrink-0">
                                @if($activity->product)
                                    <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                                        <img src="{{ asset('storage/' . $activity->product->image_path) }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-red-600 group-hover:text-white transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-black text-slate-900 leading-tight uppercase tracking-tight">
                                    {{ $activity->action == 'sale_cancelled' ? 'Sale Cancelled' : ($activity->action == 'discount_applied' ? 'Large Discount Given' : 'Stock Changed Manually') }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-400 truncate mt-0.5">{{ $activity->description }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-black text-slate-300 uppercase">{{ $activity->created_at->diffForHumans() }}</span>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[9px] font-black text-blue-600 uppercase tracking-tighter">{{ $activity->user->name ?? 'System' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 opacity-30 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-relaxed">No Security Issues<br>Found Today</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Items Running Low -->
            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Items Running Low</h4>
                    <span class="px-3 py-1 {{ $lowStockCount > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' }} text-[9px] font-black rounded-full uppercase">{{ $lowStockCount }} ALERT</span>
                </div>
                <div class="flex flex-col items-center justify-center py-8">
                    <h3 class="text-5xl font-black {{ $lowStockCount > 0 ? 'text-red-600' : 'text-slate-900' }} tabular tracking-tighter">{{ $lowStockCount }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase mt-4 tracking-widest">Products to Restock</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('D, d M'))) !!},
            datasets: [{
                label: 'Sales Amount',
                data: {!! json_encode($chartData->pluck('total')) !!},
                borderColor: '#2563eb',
                borderWidth: 4,
                backgroundColor: gradient,
                fill: true,
                tension: 0.45,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 9
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(241, 245, 249, 0.5)', drawBorder: false },
                    ticks: {
                        font: { size: 10, weight: '800' },
                        color: '#94a3b8',
                        callback: function(value) { return 'GH₵' + value.toLocaleString(); }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '800' }, color: '#94a3b8' }
                }
            }
        }
    });
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .tabular { font-variant-numeric: tabular-nums; }
</style>
@endsection
