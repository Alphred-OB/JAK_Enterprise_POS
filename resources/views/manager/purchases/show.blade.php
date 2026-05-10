@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-10">
            <div>
                <a href="{{ route('manager.purchases.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to History
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Delivery #{{ $purchase->reference_number }}</h1>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 bg-green-50 text-green-600 rounded-xl text-xs font-black uppercase tracking-widest mb-2">Received</span>
                <p class="text-xs font-bold text-slate-500 uppercase">{{ $purchase->created_at->format('d M, Y • H:i A') }}</p>
            </div>
        </header>

        <div class="bg-white rounded-[40px] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
            <!-- Details -->
            <div class="p-10 border-b border-slate-100 flex justify-between items-end bg-slate-50/50">
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Supplier</span>
                    <h3 class="text-xl font-black text-slate-900">{{ $purchase->supplier->name }}</h3>
                    @if($purchase->notes)
                        <p class="text-sm font-bold text-slate-500 mt-2">Notes: {{ $purchase->notes }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Recorded By</span>
                    <h3 class="text-sm font-black text-slate-900">{{ $purchase->user->name }}</h3>
                </div>
            </div>

            <!-- Items -->
            <div class="p-10">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Items Received</h3>
                <table class="w-full text-left border-collapse mb-8">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Product</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Unit Cost</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchase->items as $item)
                            <tr>
                                <td class="py-4 text-sm font-black text-slate-900">{{ $item->product->name }}</td>
                                <td class="py-4 text-sm font-black text-slate-700 text-center">{{ $item->quantity }}</td>
                                <td class="py-4 text-sm font-black text-slate-700 tabular text-right">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($item->unit_cost, 2) }}</td>
                                <td class="py-4 text-sm font-black text-slate-900 tabular text-right">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end pt-6 border-t border-slate-100">
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Delivery Value</span>
                        <p class="text-3xl font-black text-slate-900 tabular tracking-tighter">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($purchase->total_cost, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
