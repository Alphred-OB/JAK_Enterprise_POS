@extends('layouts.manager')

@section('content')
<div class="p-8">
    <header class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Stock Audit</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Physical count vs Expected inventory</p>
        </div>
        <a href="{{ route('manager.stock.in') }}" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            Receive Stock Delivery
        </a>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.stock.audit') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product name, sku, or barcode..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                <div class="absolute left-4 top-4 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">Search</button>
            @if(request('search'))
                <a href="{{ route('manager.stock.audit') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Product Info</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Expected Stock</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                        <tbody x-data="{ open: false }" class="divide-y divide-slate-50 border-b border-slate-50 last:border-b-0">
                            <tr class="hover:bg-slate-50/50 transition-all group cursor-pointer" @click="open = !open">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100 overflow-hidden shrink-0">
                                            @if($product->image_path)
                                                <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900">{{ $product->name }}</p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">SKU: {{ $product->sku ?? 'N/A' }} | BC: {{ $product->barcode ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="text-xl font-black tabular-nums tracking-tighter {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-red-500' : 'text-slate-900' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button class="text-xs font-black text-slate-500 hover:text-blue-600 uppercase tracking-widest transition-colors py-2 px-4 rounded-xl hover:bg-blue-50">
                                        <span x-show="!open">Audit Count</span>
                                        <span x-show="open">Cancel</span>
                                    </button>
                                </td>
                            </tr>
                            <tr x-show="open" x-collapse x-cloak>
                                <td colspan="4" class="px-8 py-6 bg-slate-50 border-t border-slate-100 shadow-inner">
                                    <div class="max-w-2xl mx-auto">
                                        <form action="{{ route('manager.stock.storeAudit') }}" method="POST" class="space-y-6">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            
                                            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                                                <h3 class="text-slate-900 font-black text-lg">Physical Stock Count</h3>
                                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Expected: {{ $product->stock_quantity }}</span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Actual Count On Shelf</label>
                                                    <input type="number" name="physical_count" required min="0" class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl py-4 px-4 focus:ring-0 focus:border-blue-500 font-black text-2xl shadow-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Reason For Count</label>
                                                    <select name="reason" required class="w-full bg-white border border-slate-200 text-slate-700 rounded-xl py-4 px-4 focus:ring-0 focus:border-blue-500 font-bold text-xs h-[64px] shadow-sm">
                                                        <option value="Routine Check">Routine Check</option>
                                                        <option value="Damage Recorded">Damage Recorded</option>
                                                        <option value="Theft Suspected">Theft Suspected</option>
                                                        <option value="Expiration/Spoilage">Expiration/Spoilage</option>
                                                        <option value="System Correction">System Correction</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-500 transition-all active:scale-95 shadow-md shadow-blue-500/20">
                                                Submit Count & Log Adjustment
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center opacity-30">
                                <div class="flex flex-col items-center">
                                    <p class="text-sm font-black uppercase tracking-[0.2em]">No products found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
