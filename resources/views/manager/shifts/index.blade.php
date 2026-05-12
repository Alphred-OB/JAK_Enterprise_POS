@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8 max-w-7xl mx-auto">
    <!-- Header Section -->
    <header class="flex flex-col xl:flex-row items-start xl:items-center justify-between mb-10 gap-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Shift Reconciliations</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Audit daily cashier shifts, verify cash drawers, and detect missing funds.</p>
        </div>
        
        <form action="" method="GET" class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
            <input type="date" name="date" value="{{ request('date') }}" class="flex-1 min-w-[140px] bg-white border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-500 shadow-sm">
            <select name="status" class="flex-1 min-w-[140px] bg-white border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-0 focus:border-blue-500 shadow-sm uppercase text-[10px] tracking-widest">
                <option value="">All Statuses</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none bg-slate-900 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                    Filter
                </button>
                @if(request()->hasAny(['date', 'status']))
                    <a href="{{ route('manager.shifts.index') }}" class="flex-1 sm:flex-none text-center bg-slate-100 text-slate-500 px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Clear</a>
                @endif
            </div>
        </form>
    </header>

    <!-- Shifts Table -->
    <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Shift / Date</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Cashier</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Opening Cash</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Expected (Sales)</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Actual Counted</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Discrepancy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($shifts as $shift)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900 tracking-wide">{{ $shift->opened_at->format('M d, Y') }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold text-slate-400">{{ $shift->opened_at->format('h:i A') }}</span>
                                <span class="text-[10px] font-bold text-slate-300">-</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $shift->closed_at ? $shift->closed_at->format('h:i A') : 'Ongoing' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 font-black text-xs flex items-center justify-center uppercase">
                                    {{ substr($shift->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $shift->user->name }}</p>
                                    @if($shift->notes)
                                        <span title="{{ $shift->notes }}" class="cursor-help text-[9px] font-bold text-amber-500 uppercase tracking-widest">Has Notes</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($shift->status === 'open')
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest">Active</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest">Closed</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <p class="text-sm font-black text-slate-600 tabular-nums">GH₵ {{ number_format($shift->opening_cash, 2) }}</p>
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($shift->status === 'closed')
                                <p class="text-sm font-black text-slate-900 tabular-nums">GH₵ {{ number_format($shift->expected_cash, 2) }}</p>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($shift->status === 'closed')
                                <p class="text-sm font-black text-slate-900 tabular-nums">GH₵ {{ number_format($shift->closing_cash, 2) }}</p>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            @if($shift->status === 'closed')
                                @php
                                    $diff = $shift->closing_cash - $shift->expected_cash;
                                @endphp
                                @if($diff < 0)
                                    <span class="px-3 py-1 bg-red-50 text-red-600 border border-red-100 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                                        Short: GH₵ {{ number_format(abs($diff), 2) }}
                                    </span>
                                @elseif($diff > 0)
                                    <span class="px-3 py-1 bg-amber-50 text-amber-600 border border-amber-100 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                                        Over: GH₵ {{ number_format($diff, 2) }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                                        Perfect Match
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-3xl flex items-center justify-center mx-auto mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <p class="text-sm font-black text-slate-900 uppercase tracking-widest mb-1">No Shifts Found</p>
                            <p class="text-xs text-slate-500 font-bold">No cashier shifts match the selected filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($shifts->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
