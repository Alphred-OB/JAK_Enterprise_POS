@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="mb-10 text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">History Log</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Full chronological record of all system activities</p>
    </header>

    <!-- Filter Bar -->
    <div class="bg-white rounded-[32px] p-6 md:p-8 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.activities.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs shadow-sm">
            </div>
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs shadow-sm">
            </div>
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Staff Member</label>
                <div class="relative">
                    <select name="user_id" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-6 pr-10 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs shadow-sm appearance-none">
                        <option value="">All Staff</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Action Type</label>
                <div class="relative">
                    <select name="action" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-6 pr-10 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-xs shadow-sm appearance-none">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 top-4 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-slate-900 text-white h-[60px] rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all active:scale-95 shadow-xl shadow-slate-200">
                    Apply Filters
                </button>
                <a href="{{ route('manager.activities.index') }}" class="w-[60px] h-[60px] bg-slate-100 text-slate-400 rounded-2xl hover:bg-slate-200 transition-all flex items-center justify-center shadow-inner" title="Reset Filters">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Time & Date</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Action</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Description</th>
                    </tr>
                </thead>
                    @forelse($activities as $activity)
                        <tbody x-data="{ open: false }" class="divide-y divide-slate-50 border-b border-slate-50 last:border-b-0">
                            <tr class="hover:bg-slate-50/50 transition-all group cursor-pointer" @click="open = !open">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-black text-slate-900 tabular tracking-tighter">{{ $activity->created_at->format('d M, Y') }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $activity->created_at->format('h:i:s A') }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center font-black text-[10px] text-slate-400">
                                            {{ substr($activity->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        <p class="text-xs font-black text-slate-600 uppercase">{{ $activity->user->name ?? 'System' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-widest">
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
                                        <p class="text-sm font-bold text-slate-600 leading-snug">{{ $activity->description }}</p>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="open" x-collapse x-cloak>
                                <td colspan="4" class="px-8 py-6 bg-slate-50/50 border-t border-slate-100/50">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div>
                                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Technical Details</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between items-center py-2 border-b border-slate-100/50">
                                                    <span class="text-xs font-bold text-slate-500">IP Address</span>
                                                    <span class="text-xs font-black text-slate-900 font-mono">{{ $activity->ip_address ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex flex-col gap-1 py-2 border-b border-slate-100/50">
                                                    <span class="text-xs font-bold text-slate-500">User Agent</span>
                                                    <span class="text-[10px] font-black text-slate-700 leading-relaxed">{{ $activity->user_agent ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if(!empty($activity->metadata) && is_array($activity->metadata))
                                        <div>
                                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Action Details</h4>
                                            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-3">
                                                @foreach($activity->metadata as $key => $value)
                                                    <div class="flex justify-between items-center pb-2 last:pb-0 border-b border-slate-50 last:border-0">
                                                        <span class="text-xs font-bold text-slate-500 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                                        <span class="text-xs font-black text-slate-900">
                                                            @if(str_contains(strtolower($key), 'amount') || str_contains(strtolower($key), 'price'))
                                                                GH₵ {{ number_format((float)$value, 2) }}
                                                            @else
                                                                {{ is_array($value) ? json_encode($value) : $value }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center opacity-30">
                                    <div class="flex flex-col items-center">
                                        <p class="text-sm font-black uppercase tracking-[0.2em]">No activities match your filters</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforelse
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
