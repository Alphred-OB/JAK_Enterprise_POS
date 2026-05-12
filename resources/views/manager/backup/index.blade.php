@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8 max-w-4xl mx-auto">
    <header class="mb-12 text-left">
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Backup & Recovery</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Protect your business data with secure system snapshots</p>
    </header>

    <div class="grid grid-cols-1 gap-8">
        <!-- Backup Action Card -->
        <div class="bg-white rounded-[40px] p-12 border border-slate-100 shadow-sm text-center relative overflow-hidden group">
            <!-- Decorative Background -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-20 h-20 md:w-24 md:h-24 bg-blue-600 text-white rounded-[24px] md:rounded-[32px] flex items-center justify-center mb-8 shadow-2xl shadow-blue-200 animate-bounce-slow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </div>
                
                <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight mb-4">Generate Snapshot</h2>
                <p class="text-slate-500 font-bold text-xs md:text-sm max-w-md mx-auto mb-10 leading-relaxed px-4">
                    This will create a full SQL backup of your entire POS database, including sales history, inventory, staff records, and settings.
                </p>

                <a href="{{ route('admin.backup.download') }}" class="w-full sm:w-auto bg-slate-900 text-white px-8 md:px-12 py-5 rounded-[20px] md:rounded-[24px] font-black text-[10px] md:text-xs uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95 text-center">
                    Download SQL Backup
                </a>
                
                <div class="mt-8 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Status: Secure & Ready</span>
                </div>
            </div>
        </div>

        <!-- Information/Caution Card -->
        <div class="bg-amber-50 rounded-[32px] p-8 border border-amber-100 flex gap-6">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-sm font-black text-amber-900 uppercase tracking-tight">Best Practices</h3>
                <p class="text-xs font-bold text-amber-700/70 mt-2 leading-relaxed">
                    It is recommended to download a backup at the end of every business day. Store these files on a secure external drive or cloud storage. In case of server failure, these files can be used to restore your entire business state instantly.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
</style>
@endsection
