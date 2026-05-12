@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8 max-w-4xl mx-auto">
    <header class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-10">
        <a href="{{ route('manager.suppliers.index') }}" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Add Supplier</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Register a new business partner</p>
        </div>
    </header>

    <form action="{{ route('manager.suppliers.store') }}" method="POST" class="space-y-8">
        @csrf
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Company Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900" placeholder="e.g. Tissue World Ltd">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900" placeholder="Full name of representative">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Phone Number</label>
                    <input type="text" name="phone" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900" placeholder="+233 ...">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Email Address</label>
                    <input type="email" name="email" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900" placeholder="supplier@email.com">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Business Address</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900" placeholder="Physical location details..."></textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-end gap-4">
            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-12 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 active:scale-95 order-1 md:order-2">Save Supplier</button>
            <a href="{{ route('manager.suppliers.index') }}" class="w-full md:w-auto text-center px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all order-2 md:order-1">Cancel</a>
        </div>
    </form>
</div>
@endsection
