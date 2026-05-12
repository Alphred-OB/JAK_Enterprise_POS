@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<div x-data="posSystem()" 
     @keydown.window.f1.prevent="$refs.searchInput.focus()"
     @keydown.window.ctrl.p.prevent="if(showSuccess) printReceipt()"
     @keydown.window.escape="if(showSupport) showSupport = false; else cart = []"
     @keydown.window.enter="if(showSuccess) showSuccess = false; else if(cart.length > 0) checkout()"
     class="flex flex-col h-screen overflow-hidden bg-white relative" 
     x-init="init()"
     x-cloak>
    
    <!-- Header: Strategic Hierarchy -->
    <header class="bg-white border-b border-slate-100 px-4 lg:px-8 py-3 lg:py-4 flex flex-col lg:flex-row lg:items-center justify-between z-20 gap-4">
        <div class="flex items-center justify-between lg:justify-start gap-4">
            <div class="flex items-center gap-3">
                @if($settings->shop_logo)
                    <img src="{{ asset('storage/' . $settings->shop_logo) }}" class="w-10 h-10 lg:w-12 lg:h-12 object-contain rounded-xl shadow-lg shadow-slate-100">
                @else
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg lg:text-xl font-black tracking-tight text-slate-900 leading-none">{{ $settings->shop_name ?? 'JAK POS' }}</h1>
                    <template x-if="currentShift">
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[8px] lg:text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-full" x-text="'SHIFT: ' + currentShift.opened_at_formatted"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="flex lg:hidden items-center gap-2">
                <button @click="showSupport = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex-1 lg:max-w-2xl lg:mx-12 order-3 lg:order-2">
            <div class="relative group">
                <input 
                    type="text" 
                    x-model="search" 
                    x-ref="searchInput"
                    placeholder="Search products or SKU..." 
                    class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-3 lg:py-3.5 pl-11 lg:pl-12 pr-4 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold"
                >
                <div class="absolute left-4 top-3 lg:top-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-6 order-2 lg:order-3">
            <div class="flex flex-col items-end">
                <span class="text-sm font-black text-slate-900">{{ auth()->user()->name ?? 'Cashier' }}</span>
                <div class="flex items-center gap-1.5" :class="isOffline ? 'text-amber-500' : 'text-slate-400'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isOffline ? 'bg-amber-500' : 'bg-green-500 animate-pulse'"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-text="isOffline ? 'Offline' : 'Online'"></span>
                </div>
            </div>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="w-11 h-11 flex items-center justify-center rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all group shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500 group-hover:text-slate-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <!-- ... existing dropdown ... -->

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 mt-3 w-56 bg-white rounded-3xl shadow-2xl shadow-slate-200 border border-slate-100 py-2 z-50 overflow-hidden"
                     style="display: none;">
                    
                    <button @click="showSupport = true; open = false" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors text-left group">
                        <div class="w-8 h-8 rounded-xl bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900 tracking-wide uppercase">Support</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Report an issue</p>
                        </div>
                    </button>
                    
                    <button @click="openHistory(); open = false" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors text-left group">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900 tracking-wide uppercase">Sales History</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">View past receipts</p>
                        </div>
                    </button>

                    @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                    <div class="h-px bg-slate-100 my-1 mx-4"></div>
                    <a href="{{ route('manager.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors text-left group">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900 tracking-wide uppercase">Dashboard</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Manager Area</p>
                        </div>
                    </a>
                    @endif

                    <div class="h-px bg-slate-100 my-1 mx-4"></div>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors text-left group">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-900 tracking-wide uppercase">Sign Out</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Lock terminal</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Content Area (Unchanged) -->
    <main class="flex-1 flex overflow-hidden">
        <!-- ... existing content ... -->
        <!-- Main Exploration Area -->
        <section class="flex-1 overflow-y-auto p-4 lg:p-8 custom-scrollbar bg-white">
            <!-- View Controls & Title -->
            <div class="flex items-center justify-between mb-4 lg:mb-6">
                <div>
                    <h2 class="text-lg lg:text-3xl font-black text-slate-900 tracking-tight" x-text="selectedCategoryName"></h2>
                    <p class="text-[9px] lg:text-sm text-slate-500 font-bold uppercase tracking-widest" x-text="filteredProducts.length + ' items'"></p>
                </div>
                <div class="flex items-center bg-slate-50 p-1 rounded-xl lg:rounded-2xl border border-slate-200">
                    <button @click="viewMode = 'list'" 
                            :class="viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm border border-slate-200' : 'text-slate-400 hover:text-slate-600'"
                            class="p-1.5 lg:p-2 rounded-lg lg:rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'grid'" 
                            :class="viewMode === 'grid' ? 'bg-white text-blue-600 shadow-sm border border-slate-200' : 'text-slate-400 hover:text-slate-600'"
                            class="p-1.5 lg:p-2 rounded-lg lg:rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Horizontal Category Chips -->
            <div class="flex items-center gap-3 overflow-x-auto pb-6 mb-8 no-scrollbar">
                <button @click="selectedCategory = null" 
                        :class="selectedCategory === null ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                        class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap">
                    All Categories
                </button>
                
                @foreach($categories as $category)
                <button @click="selectedCategory = '{{ $category->id }}'" 
                        :class="selectedCategory === '{{ $category->id }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                        class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- GRID VIEW -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 lg:gap-6">
                <template x-for="(product, index) in filteredProducts" :key="product.id">
                    <div 
                        @click="addToCart(product)"
                        class="bg-white rounded-[24px] border border-slate-200 hover:border-blue-600 hover:shadow-[0_20px_50px_rgba(37,99,235,0.1)] transition-all cursor-pointer group flex flex-col relative overflow-hidden h-full"
                    >
                        <div class="aspect-[5/4] bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-blue-50/50 transition-colors relative overflow-hidden">
                            <template x-if="product.image_path">
                                <img :src="'/storage/' + product.image_path" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </template>
                            <template x-if="!product.image_path">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 lg:h-20 w-10 lg:w-20 opacity-30 group-hover:scale-110 transition-transform duration-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </template>
                            <div class="absolute top-2 lg:top-3 left-2 lg:left-3 bg-white/80 backdrop-blur-md px-2 py-0.5 lg:px-2.5 lg:py-1 rounded-lg lg:rounded-xl border border-slate-100 shadow-sm">
                                <span class="text-[8px] lg:text-[9px] font-black text-slate-500 uppercase tracking-widest" x-text="categories.find(c => c.id === product.category_id)?.name || 'General'"></span>
                            </div>
                            <div class="absolute bottom-2 lg:bottom-3 right-2 lg:right-3 bg-blue-600 text-white px-2 py-1 lg:px-3 lg:py-1.5 rounded-lg lg:rounded-xl shadow-lg">
                                <span class="text-xs lg:text-sm font-black tabular" x-text="formatCurrency(product.selling_price)"></span>
                            </div>
                        </div>
                        <div class="p-3 lg:p-5 flex flex-col flex-1">
                            <div class="mb-2 lg:mb-3">
                                <h3 class="font-black text-slate-900 text-sm lg:text-base leading-tight line-clamp-2 group-hover:text-blue-700 transition-colors" x-text="product.name"></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[8px] lg:text-[10px] font-mono font-bold text-slate-400" x-text="'SKU: ' + product.sku"></span>
                                </div>
                            </div>
                            <div class="mt-auto pt-2 lg:pt-3 border-t border-slate-50 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[8px] lg:text-[9px] font-bold text-slate-400 uppercase tracking-widest">Stock</span>
                                    <span :class="product.stock_quantity <= product.low_stock_threshold ? 'text-red-500' : 'text-slate-900'" class="text-xs lg:text-sm font-black tabular" x-text="product.stock_quantity"></span>
                                </div>
                                <template x-if="product.stock_quantity <= product.low_stock_threshold">
                                    <div class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded-md border border-red-100 animate-pulse">
                                        <span class="text-[8px] font-black uppercase">Low</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- LIST VIEW -->
            <div x-show="viewMode === 'list'" class="space-y-3">
                <template x-for="(product, index) in filteredProducts" :key="product.id">
                    <div 
                        @click="addToCart(product)"
                        class="bg-white rounded-2xl border border-slate-100 hover:border-blue-600 hover:shadow-xl transition-all cursor-pointer group flex items-center p-4 gap-6"
                    >
                        <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 group-hover:bg-blue-50 transition-colors overflow-hidden shrink-0">
                            <template x-if="product.image_path">
                                <img :src="'/storage/' + product.image_path" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!product.image_path">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-black text-slate-900" x-text="product.name"></h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1" x-text="'SKU: ' + product.sku"></p>
                        </div>
                        <div class="text-right px-8 border-x border-slate-50">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stock</p>
                            <p :class="product.stock_quantity <= product.low_stock_threshold ? 'text-red-500' : 'text-slate-900'" class="text-sm font-black tabular" x-text="product.stock_quantity"></p>
                        </div>
                        <div class="text-right min-w-[120px]">
                            <p class="text-lg font-black text-blue-600 tabular" x-text="formatCurrency(product.selling_price)"></p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State for Search -->
            <template x-if="filteredProducts.length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">No matching items</h3>
                    <p class="text-slate-500 max-w-xs mx-auto">Try another keyword or scan a different barcode.</p>
                </div>
            </template>
        </section>

        <!-- Sidebar: Cart & Checkout -->
        <aside 
            :class="showCartMobile ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'"
            class="fixed inset-0 lg:static lg:inset-auto lg:flex z-[100] lg:z-10 w-full lg:w-[420px] bg-white border-l border-slate-100 flex flex-col shadow-[-20px_0_50px_rgba(0,0,0,0.02)] transition-transform duration-500 ease-out"
        >
            <!-- Mobile Close Handle -->
            <div @click="showCartMobile = false" class="lg:hidden h-2 w-12 bg-slate-200 rounded-full mx-auto my-4 shrink-0"></div>
            
            <!-- Header -->
            <div class="p-4 lg:p-6 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h2 class="text-lg lg:text-xl font-black text-slate-900 tracking-tight">Active Order</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] lg:text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="'#' + (lastReceipt || '260507-001')"></span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-[9px] lg:text-[10px] font-bold text-blue-600 uppercase tracking-widest" x-text="cart.length + ' ITEMS'"></span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="showCartMobile = false" class="lg:hidden p-3 text-slate-400 bg-slate-50 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <button @click="cart = []" class="p-3 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 lg:h-6 lg:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Cart Body -->
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar relative flex flex-col">
                <!-- Zen Empty State -->
                <div x-show="cart.length === 0" 
                     x-transition:enter="transition ease-out duration-500"
                     class="absolute inset-0 flex flex-col items-center justify-center text-center p-12 pointer-events-none">
                    <div class="w-20 h-20 bg-slate-50 rounded-[32px] mb-6 flex items-center justify-center border border-slate-50 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-2 text-center">Ready</h3>
                    <div class="flex items-center justify-center gap-2 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-xl">
                        <kbd class="text-[9px] font-black text-blue-600 bg-white px-1.5 py-0.5 rounded border border-blue-100">F1</kbd>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Search</span>
                    </div>
                </div>

                <div class="space-y-1" x-show="cart.length > 0">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="group bg-white hover:bg-slate-50 rounded-2xl p-3 transition-all flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-slate-900 text-sm truncate" x-text="item.name"></h4>
                                    <button @click="removeFromCart(index)" class="opacity-0 group-hover:opacity-100 text-slate-300 hover:text-red-500 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <div class="flex items-center bg-slate-100 rounded-lg p-0.5">
                                        <button @click="updateQty(index, -1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-blue-600 rounded-md transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4" /></svg>
                                        </button>
                                        <span class="w-8 text-center text-xs font-black text-slate-900 tabular" x-text="item.qty"></span>
                                        <button @click="updateQty(index, 1)" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-white hover:text-blue-600 rounded-md transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                    </div>
                                    <span class="font-black text-slate-900 text-sm tabular" x-text="formatCurrency(item.selling_price * item.qty)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 bg-slate-50/50 space-y-6">
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-widest">
                        <span>Subtotal</span>
                        <span class="text-slate-900 tabular" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-widest group cursor-pointer hover:text-blue-600 transition-all" @click="openDiscountModal()">
                        <div class="flex items-center gap-2">
                            <span>Discount</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </div>
                        <span class="text-red-500 tabular" x-text="formatCurrency(discount)"></span>
                    </div>
                    <div class="pt-4 mt-2 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">Total Payable</span>
                        <span class="text-3xl font-black text-slate-900 tabular tracking-tighter" x-text="formatCurrency(total)"></span>
                    </div>
                </div>

                <!-- Discount Modal -->
                <div x-show="showDiscountModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[110] flex items-center justify-center p-6" x-cloak>
                    <div class="bg-white rounded-[40px] p-10 max-w-md w-full shadow-2xl">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Apply Discount</h3>
                            <button @click="showDiscountModal = false" class="text-slate-400 hover:text-slate-600 transition-all">×</button>
                        </div>

                        <div class="space-y-6">
                            <template x-if="!isPinRequired">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Discount Amount (GHS)</label>
                                    <input type="number" x-model="tempDiscount" class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-3xl" placeholder="0.00">
                                    <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Limit: GH₵ <span x-text="formatCurrency(subtotal * 0.05)"></span> without manager approval</p>
                                </div>
                            </template>

                            <template x-if="isPinRequired">
                                <div class="bg-red-50 p-6 rounded-3xl border border-red-100">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-red-900 uppercase">Manager Approval Needed</p>
                                            <p class="text-[9px] font-bold text-red-600 uppercase tracking-widest">Large discount requested</p>
                                        </div>
                                    </div>
                                    <input type="password" maxlength="4" x-model="managerPin" class="w-full bg-white border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:border-red-600 transition-all font-black text-4xl tracking-[1em] text-center" placeholder="****">
                                </div>
                            </template>

                            <button @click="isPinRequired ? verifyManagerPin() : applyDiscount()" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-slate-100 hover:bg-blue-600 transition-all active:scale-[0.98]">
                                <span x-text="isPinRequired ? 'Verify & Apply' : 'Apply Discount'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Selection -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button @click="paymentMethod = 'cash'" 
                            :class="paymentMethod === 'cash' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-white text-slate-500 border border-slate-200'"
                            class="py-3 rounded-xl font-black uppercase tracking-widest text-[9px] transition-all flex flex-col items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Cash
                    </button>
                    <button @click="paymentMethod = 'momo'" 
                            :class="paymentMethod === 'momo' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-white text-slate-500 border border-slate-200'"
                            class="py-3 rounded-xl font-black uppercase tracking-widest text-[9px] transition-all flex flex-col items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        MoMo
                    </button>
                    <button @click="paymentMethod = 'card'" 
                            :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-white text-slate-500 border border-slate-200'"
                            class="py-3 rounded-xl font-black uppercase tracking-widest text-[9px] transition-all flex flex-col items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                        Card
                    </button>
                    <button @click="paymentMethod = 'debt'" 
                            :class="paymentMethod === 'debt' ? 'bg-red-600 text-white shadow-lg shadow-red-100' : 'bg-white text-slate-500 border border-slate-200'"
                            class="py-3 rounded-xl font-black uppercase tracking-widest text-[9px] transition-all flex flex-col items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Debt
                    </button>
                </div>

                <!-- Customer Selection for Debt -->
                <div x-show="paymentMethod === 'debt'" x-transition 
                     class="mb-4 bg-red-50 rounded-[20px] p-4 border border-red-100">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[10px] font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Select Customer for Debt
                        </label>
                        <button @click="showNewCustomerModal = true" class="text-[9px] font-black bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 transition-all">+ New</button>
                    </div>
                    <select x-model="selectedCustomer" 
                            class="w-full bg-white border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:border-red-500 text-sm font-bold text-slate-700 shadow-sm">
                        <option value="">-- Choose Customer --</option>
                        <template x-for="customer in customers" :key="customer.id">
                            <option :value="customer.id" x-text="customer.name"></option>
                        </template>
                    </select>
                    <p x-show="!selectedCustomer" class="text-[9px] font-bold text-red-500 mt-2">* Required for credit sales</p>
                </div>

                <button @click="checkout()" :disabled="cart.length === 0 || isProcessing || !currentShift || (paymentMethod === 'debt' && !selectedCustomer)"
                        class="w-full h-16 rounded-[24px] bg-slate-900 hover:bg-slate-800 text-white flex items-center justify-center gap-3 transition-all active:scale-[0.98] disabled:opacity-50 shadow-xl shadow-slate-100">
                    <span x-show="!isProcessing" class="text-sm font-black uppercase tracking-[0.2em]" x-text="!currentShift ? 'Open Shift First' : 'Process Payment'"></span>
                    <span x-show="isProcessing" class="text-sm font-black uppercase tracking-[0.2em]">Saving...</span>
                </button>
            </div>
        </aside>

        <!-- Floating Cart Bar (Mobile Only) -->
        <div 
            x-show="cart.length > 0 && !showCartMobile"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            class="lg:hidden fixed bottom-6 left-6 right-6 z-[80]"
        >
            <button 
                @click="showCartMobile = true"
                class="w-full bg-slate-900 text-white rounded-3xl p-4 shadow-2xl flex items-center justify-between group active:scale-[0.98] transition-all"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <div class="absolute -top-1 -right-1 bg-white text-blue-600 w-5 h-5 rounded-full text-[10px] font-black flex items-center justify-center shadow-lg" x-text="cart.length"></div>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Order</p>
                        <p class="text-lg font-black tracking-tight" x-text="formatCurrency(total)"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-xl group-hover:bg-white/20 transition-all">
                    <span class="text-xs font-black uppercase tracking-widest">Checkout</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>
        </div>
    </main>

    <!-- Success Overlay -->
    <div x-show="showSuccess" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-6" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-white rounded-[40px] p-12 max-w-md w-full text-center shadow-2xl relative overflow-hidden">
            <div class="w-24 h-24 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-2">Sale Success!</h2>
            <p class="text-slate-500 mb-6">Receipt <span class="font-mono font-bold text-slate-900" x-text="lastReceipt"></span> has been recorded.</p>
            
            <!-- Receipt Preview -->
            <template x-if="!String(lastReceiptId).startsWith('offline_')">
                <div class="mb-8 border-2 border-slate-100 rounded-3xl overflow-hidden bg-white shadow-inner">
                    <div class="bg-slate-50 px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Preview</span>
                        <div class="flex gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                        </div>
                    </div>
                    <iframe :src="'/pos/receipt/' + lastReceiptId + '?preview=1'" class="w-full h-64 border-none scale-90 origin-top"></iframe>
                </div>
            </template>
            <template x-if="String(lastReceiptId).startsWith('offline_')">
                <div class="mb-8 border-2 border-amber-100 bg-amber-50 rounded-3xl p-6 text-center">
                    <div class="w-12 h-12 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-xs font-black text-amber-700 uppercase tracking-widest mb-1">Receipt Pending Sync</p>
                    <p class="text-[10px] font-bold text-amber-600/70">The receipt will be available to print once the system reconnects to the server.</p>
                </div>
            </template>

            <div class="space-y-3">
                <button @click="printReceipt(lastReceiptId)" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-blue-100">Print Receipt (Ctrl+P)</button>
                <button @click="showSuccess = false" class="w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase tracking-widest">Next Sale (Enter)</button>
            </div>
        </div>
    </div>

    <!-- History Hub Slide-over -->
    <div x-show="showHistory" 
         class="fixed inset-0 z-50 overflow-hidden" 
         style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="showHistory = false"
             x-show="showHistory"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="w-screen max-w-lg"
                 x-show="showHistory"
                 x-transition:enter="transform transition ease-out duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="h-full flex flex-col bg-white shadow-2xl">
                    <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Sales History</h2>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">Audit Archive</p>
                        </div>
                        <button @click="showHistory = false" class="p-2 text-slate-400 hover:text-slate-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar space-y-3 bg-slate-50">
                        <template x-for="sale in sales" :key="sale.id">
                            <div class="bg-white rounded-[24px] p-5 shadow-sm border border-slate-100 hover:border-blue-200 transition-all cursor-pointer group" @click="selectedSale = sale">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-sm font-black text-slate-900 group-hover:text-blue-600 transition-colors" x-text="sale.receipt_number"></h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5" x-text="moment(sale.created_at).format('DD MMM YYYY, hh:mm A')"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-base font-black text-slate-900 tabular" x-text="formatCurrency(sale.total)"></p>
                                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-full" x-text="sale.payment_method"></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-500" x-text="sale.user?.name"></span>
                                    </div>
                                    <button @click.stop="printReceipt(sale.id)" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">Reprint</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Sale Detail View (Sliding Over History) -->
                    <div x-show="selectedSale" 
                         class="absolute inset-0 z-[60] bg-white flex flex-col"
                         x-transition:enter="transform transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in duration-300"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">
                        
                        <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <button @click="selectedSale = null" class="flex items-center gap-2 text-slate-400 hover:text-slate-900 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Back to List</span>
                            </button>
                            <button @click="printReceipt(selectedSale.id)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            </button>
                        </div>

                        <div class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar">
                            <div class="text-center">
                                <h3 class="text-3xl font-black text-slate-900 tracking-tighter" x-text="formatCurrency(selectedSale?.total)"></h3>
                                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mt-2" x-text="selectedSale?.receipt_number"></p>
                            </div>

                            <div class="bg-slate-50 rounded-[32px] p-6 space-y-4">
                                <template x-for="item in selectedSale?.items" :key="item.id">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h5 class="text-xs font-black text-slate-900" x-text="item.product?.name"></h5>
                                            <p class="text-[10px] font-bold text-slate-400" x-text="item.quantity + ' x ' + formatCurrency(item.unit_price)"></p>
                                        </div>
                                        <p class="text-xs font-black text-slate-900" x-text="formatCurrency(item.total)"></p>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-4 px-2">
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-400">Date & Time</span>
                                    <span class="text-slate-900" x-text="moment(selectedSale?.created_at).format('DD MMM YYYY, hh:mm A')"></span>
                                </div>
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-400">Cashier</span>
                                    <span class="text-slate-900" x-text="selectedSale?.user?.name"></span>
                                </div>
                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-400">Payment Method</span>
                                    <span class="text-slate-900" x-text="selectedSale?.payment_method"></span>
                                </div>
                            </div>

                            <!-- Live Receipt Preview in Details -->
                            <div class="border-2 border-dashed border-slate-200 rounded-[32px] p-2 bg-slate-50">
                                <iframe :src="'/pos/receipt/' + selectedSale?.id + '?preview=1'" class="w-full h-80 rounded-[24px] bg-white shadow-sm border-none scale-90 origin-top"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Center Slide-over -->
    <div x-show="showSupport" 
         class="fixed inset-0 z-50 overflow-hidden" 
         style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="showSupport = false"
             x-show="showSupport"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
            <div class="w-screen max-w-md"
                 x-show="showSupport"
                 x-transition:enter="transform transition ease-out duration-500"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in duration-500"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="h-full flex flex-col bg-white shadow-2xl">
                    <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Support Center</h2>
                            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mt-1">Operational Reporting</p>
                        </div>
                        <button @click="showSupport = false" class="p-2 text-slate-400 hover:text-slate-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar space-y-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Issue Category</label>
                                <select x-model="supportForm.category" class="w-full bg-slate-50 border-transparent rounded-xl py-4 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-bold text-sm">
                                    <option value="software">System Software Issue</option>
                                    <option value="hardware">Hardware / Printer Issue</option>
                                    <option value="inventory">Inventory Discrepancy</option>
                                    <option value="customer">Customer Dispute</option>
                                    <option value="other">Other Operational Issue</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Describe what happened</label>
                                <textarea 
                                    x-model="supportForm.description"
                                    rows="6" 
                                    placeholder="Provide as much detail as possible..."
                                    class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-bold text-sm"
                                ></textarea>
                            </div>

                            <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                                <div class="flex gap-4">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-red-900 leading-tight">Emergency Support</p>
                                        <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest mt-1">Direct line to admin</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 border-t border-slate-100 bg-slate-50/50">
                        <button 
                            @click="submitSupport()"
                            :disabled="!supportForm.description"
                            class="w-full py-5 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-red-700 disabled:bg-slate-300 transition-all shadow-xl shadow-red-100 active:scale-[0.98]"
                        >
                            Submit Incident Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Customer Modal -->
    <div x-show="showNewCustomerModal" 
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl z-[100] flex items-center justify-center p-6" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="bg-white rounded-[40px] p-10 max-w-sm w-full shadow-2xl relative" x-on:click.away="showNewCustomerModal = false">
            <button @click="showNewCustomerModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 p-2 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            
            <h2 class="text-2xl font-black text-slate-900 mb-2">Quick Add Customer</h2>
            <p class="text-slate-500 text-xs font-bold mb-8">Register a new customer for credit sales.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" x-model="newCustomerName" class="w-full bg-slate-50 border-transparent rounded-2xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Phone Number (Optional)</label>
                    <input type="text" x-model="newCustomerPhone" class="w-full bg-slate-50 border-transparent rounded-2xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="024 XXX XXXX">
                </div>
                
                <button @click="saveNewCustomer()" :disabled="!newCustomerName || isSavingCustomer" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all disabled:opacity-50 mt-4">
                    <span x-text="isSavingCustomer ? 'Saving...' : 'Save Customer'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Start Shift Modal -->
    <div x-show="showShiftModal" 
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl z-[100] flex items-center justify-center p-6" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-[40px] p-10 max-w-md w-full shadow-2xl">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-2">Open Shift</h2>
            <p class="text-slate-500 mb-8 font-bold">Please record your opening cash drawer balance to start selling.</p>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Opening Cash (GHS)</label>
                    <input type="number" x-model="openingCash" class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-2xl" placeholder="0.00">
                </div>
                
                <div class="space-y-3">
                    <button @click="openShift()" class="w-full py-5 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all active:scale-[0.98]">
                        Start Selling Now
                    </button>

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-4 bg-slate-50 text-slate-500 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-100 transition-all active:scale-[0.98]">
                            Or Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Shift Modal -->
    <div x-show="showCloseShiftModal" 
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl z-[100] flex items-center justify-center p-6" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-[40px] p-10 max-w-lg w-full shadow-2xl max-h-[90vh] overflow-y-auto custom-scrollbar">
            <h2 class="text-3xl font-black text-slate-900 mb-2">End of Shift</h2>
            <p class="text-slate-500 mb-8 font-bold">Reconcile your drawer and finalize the day.</p>
            
            <div class="space-y-8">
                <!-- Summary Section -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Expected Cash</span>
                        <p class="text-lg font-black text-slate-900 tabular" x-text="formatCurrency(shiftSummary?.expected_cash || 0)"></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Momo Total</span>
                        <p class="text-lg font-black text-slate-900 tabular" x-text="formatCurrency(shiftSummary?.momo || 0)"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Actual Cash Counted (GHS)</label>
                        <input type="number" x-model="closingCash" class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-2xl" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Shift Notes</label>
                        <textarea x-model="shiftNotes" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="Any discrepancies or remarks..."></textarea>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="showCloseShiftModal = false" class="flex-1 py-5 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                    <button @click="closeShift()" class="flex-1 py-5 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-red-100 hover:bg-red-700 transition-all">Finalize Shift</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    [x-cloak] { display: none !important; }
</style>

<script>
function posSystem() {
    return {
        products: @json($products),
        categories: @json($categories),
        customers: @json($customers ?? []),
        selectedCustomer: '',
        viewMode: 'grid',
        search: '',
        selectedCategory: null,
        cart: [],
        discount: 0,
        tempDiscount: 0,
        showDiscountModal: false,
        isPinRequired: false,
        managerPin: '',
        paymentMethod: 'cash',
        showSuccess: false,
        showSupport: false,
        showHistory: false,
        showNewCustomerModal: false,
        managerPin: '',
        isVerifyingPin: false,
        isPinRequired: false,
        newCustomerName: '',
        newCustomerPhone: '',
        isSavingCustomer: false,
        selectedSale: null,
        lastReceipt: '',
        lastReceiptId: null,
        isProcessing: false,
        isOffline: !navigator.onLine,
        offlineSalesQueue: JSON.parse(localStorage.getItem('jakpos_offline_sales') || '[]'),
        isSyncing: false,
        currentShift: null,
        showShiftModal: false,
        showCloseShiftModal: false,
        showCartMobile: false,
        shiftSummary: null,
        openingCash: '',
        closingCash: '',
        shiftNotes: '',

        async init() {
            try {
                // Ensure CSRF is fresh for API calls
                await axios.get('/sanctum/csrf-cookie');
                await this.checkShiftStatus();
            } catch (e) {
                console.error("Initialization failed", e);
                this.showShiftModal = true; // Fallback: allow them to try opening a shift
            }
            
            this.$nextTick(() => {
                if(this.$refs.searchInput) this.$refs.searchInput.focus();
            });

            // Network Listeners
            window.addEventListener('online', () => {
                this.isOffline = false;
                this.syncOfflineSales();
            });
            window.addEventListener('offline', () => {
                this.isOffline = true;
            });
        },

        async checkShiftStatus() {
            try {
                const response = await axios.get('/api/shifts/current');
                if (response.data.has_open_shift) {
                    this.currentShift = response.data.shift;
                    this.currentShift.opened_at_formatted = moment(this.currentShift.opened_at).format('hh:mm A');
                    this.showShiftModal = false;
                } else {
                    this.showShiftModal = true;
                }
            } catch (error) {
                console.error('Failed to check shift status:', error);
                // Fallback: If we can't reach the server, show the modal so they can try to open/reconnect
                this.showShiftModal = true;
                
                if (error.response?.status === 401) {
                    // Session might have expired or Sanctum config is wrong
                    console.warn("Unauthorized API call. Check SANCTUM_STATEFUL_DOMAINS.");
                }
            }
        },

        async openShift() {
            if (this.openingCash < 0) return;
            try {
                const response = await axios.post('/api/shifts/open', { opening_cash: this.openingCash });
                if (response.data.success) {
                    await this.checkShiftStatus();
                }
            } catch (error) {
                const msg = error.response?.data?.message || 'Connection failed. Check your internet or session.';
                alert('Shift Error: ' + msg);
                console.error('Open shift failed:', error);
            }
        },

        async openCloseShiftModal() {
            if (this.isOffline) {
                alert("Cannot close shift while offline. Please connect to the internet first.");
                return;
            }
            try {
                // Fetch preview summary before closing
                const response = await axios.post('/api/shifts/close', { 
                    closing_cash: 0, // Temp dummy
                    preview: true 
                });
                if (response.data.success) {
                    this.shiftSummary = response.data.summary;
                }
                this.showCloseShiftModal = true;
            } catch (error) {
                this.showCloseShiftModal = true;
            }
        },

        async closeShift() {
            try {
                const response = await axios.post('/api/shifts/close', { 
                    closing_cash: this.closingCash,
                    notes: this.shiftNotes
                });
                if (response.data.success) {
                    this.currentShift = null;
                    this.showCloseShiftModal = false;
                    await this.checkShiftStatus();
                }
            } catch (error) {
                alert('Failed to close shift');
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(amount || 0);
        },

        get selectedCategoryName() {
            if (this.selectedCategory === null) return 'All Products';
            const cat = this.categories.find(c => c.id === this.selectedCategory);
            return cat ? cat.name : 'All Products';
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                     (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase()));
                const matchesCategory = this.selectedCategory === null || p.category_id === this.selectedCategory;
                return matchesSearch && matchesCategory;
            });
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.selling_price * item.qty), 0);
        },

        get total() {
            return Math.max(0, this.subtotal - this.discount);
        },

        addToCart(product) {
            const index = this.cart.findIndex(i => i.id === product.id);
            if (index > -1) {
                this.cart[index].qty++;
            } else {
                this.cart.push({ ...product, qty: 1 });
            }
        },

        openDiscountModal() {
            if (this.cart.length === 0) return;
            this.tempDiscount = this.discount;
            this.isPinRequired = false;
            this.managerPin = '';
            this.showDiscountModal = true;
        },

        applyDiscount() {
            const threshold = this.subtotal * 0.05; // 5% Limit
            if (this.tempDiscount > threshold) {
                this.isPinRequired = true;
                return;
            }
            
            this.discount = parseFloat(this.tempDiscount) || 0;
            this.showDiscountModal = false;
        },

        async verifyManagerPin() {
            if(this.managerPin.length !== 4) return;
            this.isVerifyingPin = true;
            try {
                const response = await axios.post('/api/verify-pin', {
                    pin: this.managerPin,
                    action: 'apply_discount'
                });
                if(response.data.success) {
                    this.discount = parseFloat(this.tempDiscount) || 0;
                    this.showDiscountModal = false;
                    this.isPinRequired = false;
                    this.managerPin = '';
                }
            } catch (error) {
                alert(error.response?.data?.message || 'Invalid PIN or unauthorized.');
            } finally {
                this.isVerifyingPin = false;
                this.managerPin = '';
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        updateQty(index, delta) {
            this.cart[index].qty += delta;
            if (this.cart[index].qty < 1) this.removeFromCart(index);
        },



        printReceipt(id) {
            if (!id || String(id).startsWith('offline_')) {
                alert("Receipt will be available for printing after syncing with the server.");
                return;
            }
            const printUrl = `/pos/receipt/${id}`;
            const printWindow = window.open(printUrl, '_blank', 'width=400,height=600');
            printWindow.focus();
        },

        async openHistory() {
            this.showHistory = true;
            try {
                const response = await axios.get('/api/sales');
                this.sales = response.data.data;
            } catch (error) {
                console.error('Failed to fetch sales history:', error);
            }
        },

        async submitSupport() {
            try {
                await axios.post('/api/support-reports', this.supportForm);
                
                // High-fidelity feedback
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold shadow-2xl z-[100] animate-bounce';
                toast.innerText = 'Incident Report Submitted to Admin';
                document.body.appendChild(toast);
                
                this.showSupport = false;
                this.supportForm.description = '';
                
                setTimeout(() => toast.remove(), 3000);
            } catch (error) {
                alert('Failed to send report. Check connection.');
            }
        },

        async saveNewCustomer() {
            if(!this.newCustomerName) return;
            this.isSavingCustomer = true;
            try {
                const response = await axios.post('/api/customers', {
                    name: this.newCustomerName,
                    phone: this.newCustomerPhone
                });
                if(response.data.success) {
                    this.customers.push(response.data.customer);
                    // Sort alphabetically
                    this.customers.sort((a, b) => a.name.localeCompare(b.name));
                    this.selectedCustomer = response.data.customer.id;
                    this.showNewCustomerModal = false;
                    this.newCustomerName = '';
                    this.newCustomerPhone = '';
                }
            } catch (error) {
                alert('Failed to save customer');
            } finally {
                this.isSavingCustomer = false;
            }
        },

        async checkout() {
            if (this.cart.length === 0) return;
            this.isProcessing = true;

            const salePayload = {
                items: this.cart,
                subtotal: this.subtotal,
                discount: this.discount,
                total: this.total,
                payment_method: this.paymentMethod,
                customer_id: this.paymentMethod === 'debt' ? this.selectedCustomer : null,
                timestamp: new Date().toISOString()
            };

            // Process Offline
            if (this.isOffline) {
                this.saveOfflineSale(salePayload);
                return;
            }

            try {
                const response = await axios.post('/api/sales', salePayload);

                if (response.data.success) {
                    this.lastReceipt = response.data.receipt_number;
                    this.lastReceiptId = response.data.sale.id;
                    this.showSuccess = true;
                    this.cart = [];
                    this.discount = 0;
                    this.isProcessing = false;
                    
                    // Removed the buggy this.config.autoPrint check here
                    
                    setTimeout(() => this.$refs.searchInput.focus(), 100);
                }
            } catch (error) {
                console.error('Checkout failed, attempting to save offline:', error);
                // If network error, fallback to offline
                if (!error.response) {
                    this.saveOfflineSale(salePayload);
                } else {
                    alert('Payment Failed: ' + (error.response?.data?.message || 'Server error'));
                    this.isProcessing = false;
                }
            }
        },

        saveOfflineSale(salePayload) {
            salePayload.offline_id = 'offline_' + Date.now();
            this.offlineSalesQueue.push(salePayload);
            localStorage.setItem('jakpos_offline_sales', JSON.stringify(this.offlineSalesQueue));
            
            this.lastReceipt = "OFFLINE-PENDING";
            this.lastReceiptId = salePayload.offline_id;
            
            // Adjust local stock visually
            this.cart.forEach(item => {
                const productIndex = this.products.findIndex(p => p.id === item.id);
                if (productIndex > -1) {
                    this.products[productIndex].stock_quantity -= item.qty;
                }
            });

            this.showSuccess = true;
            this.cart = [];
            this.discount = 0;
            this.isProcessing = false;
            
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-amber-500 text-white px-8 py-4 rounded-2xl font-bold shadow-2xl z-[100] animate-bounce';
            toast.innerText = 'Sale Saved Offline. Will sync when connected.';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        },

        async syncOfflineSales() {
            if (this.offlineSalesQueue.length === 0 || this.isSyncing) return;
            
            this.isSyncing = true;
            const toast = document.createElement('div');
            toast.id = 'sync-toast';
            toast.className = 'fixed top-8 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-8 py-3 rounded-full font-bold shadow-2xl z-[100] flex items-center gap-3';
            toast.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Syncing ${this.offlineSalesQueue.length} offline sales...`;
            document.body.appendChild(toast);

            const queueCopy = [...this.offlineSalesQueue];
            let successfulSyncs = 0;

            for (const sale of queueCopy) {
                try {
                    await axios.post('/api/sales', sale);
                    // Remove from queue on success
                    this.offlineSalesQueue = this.offlineSalesQueue.filter(s => s.offline_id !== sale.offline_id);
                    localStorage.setItem('jakpos_offline_sales', JSON.stringify(this.offlineSalesQueue));
                    successfulSyncs++;
                } catch (error) {
                    console.error('Failed to sync offline sale:', error);
                    // Stop syncing process if one fails due to server error, try again later
                    break;
                }
            }

            this.isSyncing = false;
            document.getElementById('sync-toast')?.remove();

            if (successfulSyncs > 0) {
                const successToast = document.createElement('div');
                successToast.className = 'fixed top-8 left-1/2 -translate-x-1/2 bg-green-500 text-white px-8 py-3 rounded-full font-bold shadow-2xl z-[100]';
                successToast.innerText = `Successfully synced ${successfulSyncs} sales!`;
                document.body.appendChild(successToast);
                setTimeout(() => successToast.remove(), 3000);
            }
        }
    }
}
</script>
@endsection
