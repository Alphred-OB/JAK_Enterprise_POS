@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="mb-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-red-600 tracking-tight uppercase leading-none">Flagged Actions</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">High-priority security alerts and suspicious activities</p>
        </div>
        <div class="flex items-center gap-2 px-6 py-3 bg-red-50 text-red-600 rounded-full animate-pulse border border-red-100">
            <div class="w-2 h-2 bg-red-600 rounded-full"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Active Monitoring</span>
        </div>
    </header>

    <!-- Filter Bar -->
    <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.activities.flagged') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-bold text-xs">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-bold text-xs">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Filter by Staff</label>
                <select name="user_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-bold text-xs">
                    <option value="">All Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all active:scale-95">
                    Investigate
                </button>
                <a href="{{ route('manager.activities.flagged') }}" class="px-4 py-3 bg-slate-100 text-slate-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-red-50 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50 bg-red-50/10">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Time & Date</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User Involved</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Alert Type</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-red-50/30 transition-all group">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-900 tabular tracking-tighter">{{ $activity->created_at->format('d M, Y') }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $activity->created_at->format('h:i:s A') }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center font-black text-[10px] text-red-600">
                                        {{ substr($activity->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <p class="text-xs font-black text-slate-600 uppercase">{{ $activity->user->name ?? 'System' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-red-600 text-white text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ str_replace('_', ' ', $activity->action) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-start gap-4">
                                    @if($activity->product)
                                        <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                            <img src="{{ asset('storage/' . $activity->product->image_path) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    <p class="text-sm font-bold text-red-600 leading-snug">{{ $activity->description }}</p>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center opacity-30">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                    <p class="text-sm font-black uppercase tracking-[0.2em]">No flagged actions match your search</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
