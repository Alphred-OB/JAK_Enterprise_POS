@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8" x-data="{ viewMode: 'table' }">
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Products & Stock</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Manage your items, prices, and stock levels</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- View Switcher -->
            <div class="bg-white p-1 rounded-2xl border border-slate-100 shadow-sm flex items-center shrink-0">
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-400 hover:text-slate-600'" class="p-3 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                </button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-400 hover:text-slate-600'" class="p-3 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                </button>
            </div>

            <div class="hidden sm:flex bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Items</span>
                <span class="text-xs font-black text-slate-900">{{ $products->total() }}</span>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('manager.export.products') }}" class="flex-1 sm:flex-none bg-white text-slate-600 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all border border-slate-100 flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export
                </a>
                <a href="{{ route('manager.products.create') }}" class="flex-[2] sm:flex-none bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 active:scale-95 flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Add Item
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" x-cloak class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Item Details</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Stock</th>
                        <th class="hidden lg:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pricing (GH₵)</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Settings</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center overflow-hidden border border-slate-100 group-hover:bg-white transition-all shadow-sm shrink-0">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-300 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $product->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-0.5 mono">{{ $product->sku ?? 'NO-CODE' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-6">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $product->category->name ?? 'None' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-black {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-red-600' : 'text-slate-900' }} tabular">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase">{{ $product->stock_unit ?? 'pcs' }}</span>
                                    </div>
                                    @if($product->stock_quantity <= $product->low_stock_threshold)
                                        <span class="text-[8px] font-black text-red-500 uppercase tracking-widest animate-pulse">Needs Restocking</span>
                                    @else
                                        <span class="text-[8px] font-black text-green-500 uppercase tracking-widest">Stock is OK</span>
                                    @endif
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-8 py-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[8px] font-black text-slate-400 uppercase">Cost</p>
                                        <p class="text-xs font-black text-slate-600 tabular">{{ number_format($product->cost_price, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-black text-slate-400 uppercase">Selling</p>
                                        <p class="text-xs font-black text-blue-600 tabular">{{ number_format($product->selling_price, 2) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.products.edit', $product) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center opacity-30">
                                    <p class="text-sm font-black uppercase tracking-[0.2em]">No items added yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- GRID VIEW -->
    <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($products as $product)
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all group flex flex-col">
                <div class="aspect-square bg-slate-50 relative overflow-hidden shrink-0">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-md px-3 py-1 rounded-xl border border-slate-100 shadow-sm">
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $product->category->name ?? 'General' }}</span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="mb-4">
                        <h3 class="text-base font-black text-slate-900 uppercase leading-tight line-clamp-1">{{ $product->name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-1">{{ $product->sku ?? 'NO-CODE' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Retail Price</p>
                            <p class="text-sm font-black text-blue-600 tabular">GH₵ {{ number_format($product->selling_price, 2) }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase mb-1">Stock Level</p>
                            <p class="text-sm font-black {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-red-600 animate-pulse' : 'text-slate-900' }} tabular">{{ $product->stock_quantity }} {{ $product->stock_unit ?? 'pcs' }}</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                        <a href="{{ route('manager.products.edit', $product) }}" class="flex-1 bg-slate-900 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all text-center">Edit Item</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($products->hasPages())
        <div class="mt-12">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
