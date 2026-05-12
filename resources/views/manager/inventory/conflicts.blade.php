@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="mb-10 text-left">
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Inventory Conflicts</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Resolve stock discrepancies detected during offline syncs</p>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Product / SKU</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sale Info</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Conflict Details</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($conflicts as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-900">{{ $item->product->name }}</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $item->product->sku }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[11px] font-black text-slate-900 uppercase">{{ $item->sale->receipt_number }}</p>
                                <p class="text-[10px] font-bold text-slate-400">Sold by {{ $item->sale->user->name }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-2xl text-[11px] font-bold leading-relaxed border border-red-100">
                                    {{ $item->conflict_note }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right" x-data="{ showResolve: false }">
                                <button @click="showResolve = true" class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">
                                    Resolve
                                </button>

                                <!-- Resolution Modal -->
                                <template x-if="showResolve">
                                    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-sm">
                                        <div class="bg-white w-full max-w-md rounded-[40px] shadow-2xl p-10 text-left" @click.away="showResolve = false">
                                            <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Inventory Resolution</h2>
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-8">Set physical stock for {{ $item->product->name }}</p>

                                            <form action="{{ route('manager.inventory.resolve', $item->id) }}" method="POST" class="space-y-6">
                                                @csrf
                                                <div class="space-y-2">
                                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Actual Physical Stock (On Shelf)</label>
                                                    <input type="number" name="actual_stock" required min="0" 
                                                           class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold"
                                                           placeholder="Count the items on the shelf now...">
                                                </div>

                                                <div class="space-y-2">
                                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Resolution Note</label>
                                                    <textarea name="resolution_note" required rows="3"
                                                              class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold"
                                                              placeholder="e.g. Physical count confirmed 2 items. System stock was incorrect."></textarea>
                                                </div>

                                                <div class="flex gap-4 pt-4">
                                                    <button type="button" @click="showResolve = false" class="flex-1 px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">Cancel</button>
                                                    <button type="submit" class="flex-1 bg-blue-600 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all">Update Reality</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-lg font-black text-slate-900">No Conflicts</h3>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Inventory is perfectly reconciled across all terminals.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($conflicts->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $conflicts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
