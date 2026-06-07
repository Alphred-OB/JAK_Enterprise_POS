@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="mb-10 text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">History Log</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">Full chronological record of all system activities</p>
    </header>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-[32px] p-6 md:p-8 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.activities.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-xs appearance-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-xs appearance-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Filter by User</label>
                <select name="user_id" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-xs appearance-none">
                    <option value="">All Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Action Category</label>
                <select name="action" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-xs appearance-none">
                    <option value="">All Actions</option>
                    <option value="stock_adjusted" {{ request('action') == 'stock_adjusted' ? 'selected' : '' }}>Stock Adjustments</option>
                    <option value="sale_cancelled" {{ request('action') == 'sale_cancelled' ? 'selected' : '' }}>Cancelled Sales</option>
                    <option value="discount_applied" {{ request('action') == 'discount_applied' ? 'selected' : '' }}>Discounts Given</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Staff Logins</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all active:scale-95 shadow-xl shadow-slate-100">Filter Log</button>
                @if(request()->anyFilled(['start_date', 'end_date', 'user_id', 'action']))
                    <a href="{{ route('manager.activities.index') }}" class="p-4 bg-slate-50 text-slate-400 hover:text-red-600 rounded-2xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/10">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Timestamp</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Staff Member</th>
                        <th class="hidden md:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Action Type</th>
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
                                <td class="hidden sm:table-cell px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center font-black text-[10px] text-slate-500">
                                            {{ substr($activity->user->name ?? 'S', 0, 1) }}
                                        </div>
                                        <p class="text-xs font-black text-slate-600 uppercase">{{ $activity->user->name ?? 'System' }}</p>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-8 py-6">
                                    <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-widest border border-slate-100">
                                        {{ str_replace('_', ' ', $activity->action) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-start gap-4">
                                        @if($activity->product)
                                            <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                                <img src="{{ asset('storage/' . $activity->product->image_path) }}" class="w-full h-full object-cover" loading="lazy">
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
