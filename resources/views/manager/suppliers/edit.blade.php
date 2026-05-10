@extends('layouts.manager')

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <header class="flex items-center gap-6 mb-10">
        <a href="{{ route('manager.suppliers.index') }}" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Edit Supplier</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Update details for {{ $supplier->name }}</p>
        </div>
    </header>

    <form action="{{ route('manager.suppliers.update', $supplier) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Company Name</label>
                    <input type="text" name="name" value="{{ $supplier->name }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Email Address</label>
                    <input type="email" name="email" value="{{ $supplier->email }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Business Address</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">{{ $supplier->address }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('manager.suppliers.index') }}" class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all">Cancel</a>
            <button type="submit" class="bg-slate-900 text-white px-12 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-100 active:scale-95">Update Supplier</button>
        </div>
    </form>
</div>
@endsection
