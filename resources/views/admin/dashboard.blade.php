@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Control Center</h1>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mt-1">Super Admin Overview</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
                <a href="{{ route('admin.settings.index') }}" class="w-full sm:flex-1 md:flex-none justify-center bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Settings
                </a>
                <a href="{{ route('admin.backup.index') }}" class="w-full sm:flex-1 md:flex-none justify-center bg-slate-900 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Backup
                </a>
            </div>
        </header>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Lifetime Revenue -->
            <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-colors">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Lifetime Revenue</span>
                <p class="text-2xl md:text-3xl font-black text-slate-900 tabular tracking-tighter">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($totalRevenue, 2) }}</p>
            </div>

            <!-- Net Profit -->
            <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-blue-200 transition-colors">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Net Profit</span>
                <p class="text-2xl md:text-3xl font-black text-slate-900 tabular tracking-tighter">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} {{ number_format($netProfit, 2) }}</p>
            </div>

            <!-- System Users -->
            <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm relative overflow-hidden group hover:border-purple-200 transition-colors">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Total Staff</span>
                <p class="text-2xl md:text-3xl font-black text-slate-900 tabular tracking-tighter">{{ $totalUsers }}</p>
            </div>

            <!-- Shop Settings -->
            <a href="{{ route('admin.settings.index') }}" class="bg-slate-900 rounded-[32px] p-8 shadow-xl shadow-slate-200 relative overflow-hidden group hover:bg-blue-600 transition-colors block">
                <div class="absolute top-0 right-0 p-8 opacity-20 transition-opacity">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Configure</span>
                <p class="text-xl md:text-2xl font-black text-white tracking-tight mt-1">Shop Details</p>
            </a>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Staff Overview -->
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Staff Overview</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">Manage All &rarr;</a>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-black">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Managers</h3>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Full dashboard access</p>
                            </div>
                        </div>
                        <span class="text-xl font-black text-slate-900 tabular tracking-tighter">{{ $managers }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-black">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Cashiers</h3>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">POS access only</p>
                            </div>
                        </div>
                        <span class="text-xl font-black text-slate-900 tabular tracking-tighter">{{ $cashiers }}</span>
                    </div>
                </div>
            </div>

            <!-- Database Security -->
            <div class="bg-slate-900 rounded-[40px] shadow-2xl shadow-slate-200 border border-slate-800 p-10 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                </div>
                <div class="relative z-10">
                    <h2 class="text-2xl font-black tracking-tight mb-2">Database Security</h2>
                    <p class="text-sm font-bold text-slate-400 mb-8 max-w-sm leading-relaxed">Protect your business data. Download encrypted backups of your entire inventory, sales history, and customer records.</p>
                    
                    <a href="{{ route('admin.backup.index') }}" class="inline-flex items-center gap-3 bg-white text-slate-900 px-8 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all shadow-xl active:scale-95 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Export Database
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
