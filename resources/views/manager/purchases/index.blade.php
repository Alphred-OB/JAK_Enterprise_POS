@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Stock-In History</h1>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Record and track inventory deliveries</p>
            </div>
            <a href="{{ route('manager.purchases.create') }}" class="w-full md:w-auto justify-center bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                Record Delivery
            </a>
        </header>

        <!-- List -->
        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            @if($purchases->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reference</th>
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Supplier</th>
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Cost</th>
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="py-5 px-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($purchases as $purchase)
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="py-5 px-6 text-sm font-black text-slate-900 mono">{{ $purchase->reference_number }}</td>
                                    <td class="py-5 px-6 text-xs font-bold text-slate-500 uppercase">{{ $purchase->created_at->format('d M, Y') }}</td>
                                    <td class="py-5 px-6 text-sm font-black text-slate-700">{{ $purchase->supplier->name }}</td>
                                    <td class="py-5 px-6 text-sm font-black text-slate-900 tabular tracking-tighter text-right">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($purchase->total_cost, 2) }}</td>
                                    <td class="py-5 px-6 text-center">
                                        <span class="inline-block px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-black uppercase tracking-widest">Received</span>
                                    </td>
                                    <td class="py-5 px-6 text-right">
                                        <a href="{{ route('manager.purchases.show', $purchase) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    {{ $purchases->links() }}
                </div>
            @else
                <div class="p-20 text-center">
                    <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-2">No Deliveries Recorded</h3>
                    <p class="text-sm font-bold text-slate-500 max-w-md mx-auto mb-8">You haven't recorded any supplier deliveries yet. Click "Record Delivery" to log new stock arrivals.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
