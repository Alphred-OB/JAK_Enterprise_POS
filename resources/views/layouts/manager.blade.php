<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAK POS - Manager Intelligence</title>
    @if(isset($settings) && $settings->shop_logo)
        <link rel="icon" href="{{ asset('storage/' . $settings->shop_logo) }}">
    @endif
    
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .mono { font-family: 'JetBrains+Mono', monospace; }
        [x-cloak] { display: none !important; }
        
        /* Sidebar Active State */
        .nav-link.active {
            background: #0f172a;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
        }
        .nav-link.active svg { color: #3b82f6; }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-[#fafbfc] text-slate-900 overflow-x-hidden">
    <div class="flex min-h-screen">
        <!-- SIDEBAR: The Command Nav -->
        <aside class="w-80 bg-white border-r border-slate-100 flex flex-col fixed inset-y-0 left-0 z-50 overflow-y-auto scrollbar-hide">
            <!-- Brand Logo -->
            <div class="p-8 mb-4">
                <div class="flex items-center gap-3">
                    @if($settings->shop_logo)
                        <img src="{{ asset('storage/' . $settings->shop_logo) }}" class="w-10 h-10 object-contain rounded-xl shadow-2xl shadow-slate-200">
                    @else
                        <div class="w-10 h-10 bg-slate-900 rounded-2xl flex items-center justify-center shadow-2xl shadow-slate-200">
                            <span class="text-white font-black text-xl">{{ substr($settings->shop_name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none">{{ $settings->shop_name }}</h2>
                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">Enterprise Intelligence</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-6 space-y-2">
                @if(auth()->user()->role !== 'inventory_officer')
                <div class="pb-4">
                    <span class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">General</span>
                </div>
                
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">System Control Center</span>
                </a>
                @endif

                <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Store Dashboard</span>
                </a>
                @endif

                <div class="pt-8 pb-4">
                    <span class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Shop Management</span>
                </div>

                <a href="{{ route('manager.products.index') }}" class="nav-link {{ request()->routeIs('manager.products.*') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Products Catalog</span>
                    </div>
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                        <div class="flex items-center justify-center">
                            <span class="relative flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-[9px] font-black text-white tabular-nums">{{ $lowStockCount }}</span>
                            </span>
                        </div>
                    @endif
                </a>

                @if(auth()->user()->role !== 'inventory_officer')
                <a href="{{ route('manager.categories.index') }}" class="nav-link {{ request()->routeIs('manager.categories.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Categories</span>
                </a>
                @endif

                <a href="{{ route('manager.stock.audit') }}" class="nav-link {{ request()->routeIs('manager.stock.*') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Stock Control</span>
                    </div>
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                        <div class="flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        </div>
                    @endif
                </a>

                <a href="{{ route('manager.inventory.conflicts') }}" class="nav-link {{ request()->routeIs('manager.inventory.conflicts') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Stock Conflicts</span>
                    </div>
                    @php
                        $unresolvedConflicts = \App\Models\SaleItem::where('status', 'conflict')->count();
                    @endphp
                    @if($unresolvedConflicts > 0)
                        <div class="flex items-center justify-center">
                            <span class="relative flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-600 items-center justify-center text-[9px] font-black text-white tabular-nums">{{ $unresolvedConflicts }}</span>
                            </span>
                        </div>
                    @endif
                </a>

                @if(auth()->user()->role !== 'inventory_officer')
                <a href="{{ route('manager.returns.index') }}" class="nav-link {{ request()->routeIs('manager.returns.*') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Returns & Refunds</span>
                    </div>
                    @if(isset($returnCount) && $returnCount > 0)
                        <div class="flex items-center justify-center">
                            <span class="relative inline-flex rounded-full h-5 w-5 bg-blue-100 items-center justify-center text-[9px] font-black text-blue-600 tabular-nums">{{ $returnCount }}</span>
                        </div>
                    @endif
                </a>

                <a href="{{ route('manager.shifts.index') }}" class="nav-link {{ request()->routeIs('manager.shifts.*') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Cash Reconciliations</span>
                    </div>
                    @if(isset($activeShiftCount) && $activeShiftCount > 0)
                        <div class="flex items-center justify-center">
                            <span class="relative flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-green-500 items-center justify-center text-[9px] font-black text-white tabular-nums">{{ $activeShiftCount }}</span>
                            </span>
                        </div>
                    @endif
                </a>

                <a href="{{ route('manager.expenses.index') }}" class="nav-link {{ request()->routeIs('manager.expenses.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Record Expenses</span>
                </a>

                @endif

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Staff & Roles</span>
                </a>
                @endif

                @if(auth()->user()->role !== 'inventory_officer')
                <a href="{{ route('manager.customers.index') }}" class="nav-link {{ request()->routeIs('manager.customers.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Customers</span>
                </a>
                @endif

                <a href="{{ route('manager.suppliers.index') }}" class="nav-link {{ request()->routeIs('manager.suppliers.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Suppliers</span>
                </a>

                <a href="{{ route('manager.purchases.index') }}" class="nav-link {{ request()->routeIs('manager.purchases.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Stock-In & Deliveries</span>
                </a>

                @if(auth()->user()->role !== 'inventory_officer')
                <div class="pt-8 pb-4">
                    <span class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Safety & Records</span>
                </div>

                <a href="{{ route('manager.activities.index') }}" class="nav-link {{ request()->routeIs('manager.activities.index') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">History Log</span>
                </a>

                <a href="{{ route('manager.activities.flagged') }}" class="nav-link {{ request()->routeIs('manager.activities.flagged') ? 'active' : '' }} flex items-center justify-between px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <div class="flex items-center gap-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span class="text-sm font-black uppercase tracking-tight">Safety Alerts</span>
                    </div>
                    @if(isset($flaggedCount) && $flaggedCount > 0)
                        <div class="flex items-center justify-center">
                            <span class="relative flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-amber-500 items-center justify-center text-[9px] font-black text-white tabular-nums">{{ $flaggedCount }}</span>
                            </span>
                        </div>
                    @endif
                </a>

                @endif

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.backup.index') }}" class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Database Backup</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }} flex items-center gap-4 px-4 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 hover:text-slate-900 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="text-sm font-black uppercase tracking-tight">Shop Configuration</span>
                </a>
                @endif
            </nav>

            <!-- Bottom Profile / Logout & Issue Reporting -->
            <div x-data="{ reportIssueOpen: false }" class="p-6 border-t border-slate-50 bg-slate-50/50">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center font-black text-slate-900">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-slate-900 truncate uppercase">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ auth()->user()->role }}</p>
                    </div>
                </div>

                <button @click="reportIssueOpen = true" class="w-full py-4 mb-3 bg-white border border-slate-100 rounded-2xl text-[10px] font-black text-amber-500 uppercase tracking-widest hover:bg-amber-50 hover:border-amber-100 transition-all flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Report Issue
                </button>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-white border border-slate-100 rounded-2xl text-[10px] font-black text-red-500 uppercase tracking-widest hover:bg-red-50 hover:border-red-100 transition-all flex items-center justify-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout Securely
                    </button>
                </form>

                <!-- Report Issue Modal -->
                <div x-show="reportIssueOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center">
                    <div x-show="reportIssueOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="reportIssueOpen = false"></div>
                    
                    <div x-show="reportIssueOpen" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                         class="relative bg-white rounded-[40px] shadow-2xl p-10 max-w-lg w-full mx-4 overflow-hidden border border-slate-100">
                        
                        <div class="mb-8">
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Report a Problem</h2>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">This goes directly to the Manager's Safety Alerts.</p>
                        </div>

                        <form action="{{ route('manager.issues.report') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Urgency Level</label>
                                <select name="urgency" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-amber-500 transition-all font-black text-sm text-slate-900 appearance-none">
                                    <option value="low">Low (Non-urgent)</option>
                                    <option value="medium">Medium (Needs attention)</option>
                                    <option value="high">High (Urgent)</option>
                                    <option value="critical">Critical (Stop operations)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Describe the Issue</label>
                                <textarea name="description" required rows="4" placeholder="e.g. The shelf in aisle 4 is broken, or I found a missing box..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-amber-500 transition-all font-bold text-sm text-slate-900"></textarea>
                            </div>

                            <div class="flex gap-4 pt-4 border-t border-slate-50">
                                <button type="button" @click="reportIssueOpen = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-slate-500 bg-slate-50 uppercase tracking-widest hover:bg-slate-100 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-white bg-amber-500 uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                                    Submit Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 ml-80 min-h-screen">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    
    @stack('scripts')
</body>
</html>
