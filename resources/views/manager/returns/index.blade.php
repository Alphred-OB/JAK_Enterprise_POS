@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Returns & Refunds</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Audit and manage product returns and stock adjustments</p>
        </div>
        <a href="{{ route('manager.returns.create') }}" class="w-full md:w-auto justify-center bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3" /></svg>
            Process Return
        </a>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.returns.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by receipt # or product name..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                <div class="absolute left-4 top-4 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">Search</button>
            @if(request('search'))
                <a href="{{ route('manager.returns.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Return Date</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Receipt / Product</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Qty / Refund</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Reason</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Authorized By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($returns as $return)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight">{{ $return->created_at->format('d M, Y') }}</p>
                                <p class="text-[9px] font-bold text-slate-300 mt-0.5">{{ $return->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div>
                                    <p class="text-xs font-black text-blue-600 uppercase tracking-tight">#{{ $return->sale->receipt_number ?? 'DELETED' }}</p>
                                    <p class="text-sm font-black text-slate-900 mt-0.5">{{ $return->product->name ?? 'DELETED PRODUCT' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 tabular tracking-tight">
                                        {{ $return->quantity }} units
                                    </span>
                                    <span class="text-[10px] font-black text-red-500 mt-0.5">
                                        Refund: GH₵ {{ number_format($return->refund_amount, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $return->reason }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-black text-slate-400">
                                        {{ substr($return->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $return->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center opacity-30">
                                <p class="text-sm font-black uppercase tracking-[0.2em]">No returns recorded</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($returns->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
