@extends('layouts.manager')

@section('content')
<div class="p-8">
    <header class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Receive Stock</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Log bulk deliveries from suppliers</p>
        </div>
        <a href="{{ route('manager.stock.audit') }}" class="bg-slate-100 text-slate-500 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all active:scale-95 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Audit
        </a>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-3xl bg-white rounded-[40px] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('manager.stock.storeStockIn') }}" method="POST" class="space-y-8">
            @csrf
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Select Product</label>
                <div class="relative">
                    <select name="product_id" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 pl-14 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm appearance-none">
                        <option value="">-- Choose product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Current Stock: {{ $product->stock_quantity }})</option>
                        @endforeach
                    </select>
                    <div class="absolute left-5 top-5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Supplier</label>
                    <select name="supplier_id" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm appearance-none">
                        <option value="">-- Choose supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Quantity Received</label>
                    <input type="number" name="quantity" required min="1" class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-2xl" placeholder="0">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Invoice / Reference Number (Optional)</label>
                    <input type="text" name="reference_number" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="e.g. INV-2024-001">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Delivery Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="Any details about the delivery condition..."></textarea>
                </div>
            </div>

            <hr class="border-slate-100">

            <div class="flex justify-end">
                <button type="submit" class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95">
                    Log Delivery & Update Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
