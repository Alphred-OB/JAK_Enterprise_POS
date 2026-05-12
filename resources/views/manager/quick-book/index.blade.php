@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <!-- Header -->
    <header class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tighter uppercase italic">Quick Book</h1>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mt-1">Real-Time Financial Sales Ledger</p>
        </div>
        
        <div class="flex items-center gap-3 w-full lg:w-auto">
            <div class="flex-1 lg:flex-none bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Period Sales</span>
                <span class="text-lg md:text-xl font-black text-slate-900 tabular">GH₵ {{ number_format($totalAmount, 2) }}</span>
            </div>
            <div class="flex-1 lg:flex-none bg-slate-900 px-6 py-3 rounded-2xl shadow-xl shadow-slate-200">
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block">Transactions</span>
                <span class="text-lg md:text-xl font-black text-white tabular">{{ $totalTransactions }}</span>
            </div>
        </div>
    </header>

    <!-- Advanced Filters -->
    <section class="bg-white rounded-[32px] p-6 mb-8 border border-slate-100 shadow-sm">
        <form action="{{ route('manager.quick-book.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Date Range -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                       class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs uppercase">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                       class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs uppercase">
            </div>

            <!-- Cashier -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Cashier</label>
                <select name="user_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs uppercase appearance-none">
                    <option value="">All Staff</option>
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}" {{ request('user_id') == $cashier->id ? 'selected' : '' }}>{{ $cashier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method -->
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Payment</label>
                <select name="payment_method" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs uppercase appearance-none">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="debt" {{ request('payment_method') == 'debt' ? 'selected' : '' }}>Debt</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white h-[46px] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Filter Ledger
                </button>
                <a href="{{ route('manager.export.sales', request()->all()) }}" class="w-[46px] h-[46px] flex items-center justify-center bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-all group" title="Export CSV">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </a>
            </div>
        </form>
    </section>

    <!-- Ledger Table -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Transaction ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Date & Time</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Method</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Staff</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Amount</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-tighter">#{{ $sale->invoice_no }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-xs font-black text-slate-700">{{ $sale->created_at->format('d M, Y') }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5 tabular">{{ $sale->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-8 py-5">
                            @if($sale->customer)
                                <p class="text-xs font-black text-slate-900 uppercase">{{ $sale->customer->name }}</p>
                                <p class="text-[9px] font-bold text-slate-400 tabular">{{ $sale->customer->phone }}</p>
                            @else
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Walk-in</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest 
                                {{ $sale->payment_method == 'cash' ? 'bg-green-50 text-green-600' : ($sale->payment_method == 'momo' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600') }}">
                                {{ $sale->payment_method }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-[9px] font-black text-slate-500 uppercase">
                                    {{ substr($sale->user->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-black text-slate-700 uppercase">{{ $sale->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-slate-900 tabular tracking-tight">GH₵ {{ number_format($sale->total, 2) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="p-2 bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white rounded-xl transition-all shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="opacity-20 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">No Transactions Found in this Period</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($sales->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .tabular { font-variant-numeric: tabular-nums; }
</style>
@endsection
