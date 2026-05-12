@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8 max-w-5xl mx-auto">
    <header class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-10">
        <a href="{{ route('manager.returns.index') }}" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Process Return</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Locate a sale and authorize an item return</p>
        </div>
    </header>

    <!-- STEP 1: Search Sale -->
    <div class="bg-white rounded-[32px] p-6 md:p-10 border border-slate-100 shadow-sm mb-10">
        <form action="{{ route('manager.returns.create') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
            <div class="w-full md:flex-1 space-y-3">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Receipt Number</label>
                <div class="relative group">
                    <input type="text" name="receipt" value="{{ request('receipt') }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm uppercase tracking-widest" placeholder="RCPT-XXXXXX">
                    <div class="absolute left-4 top-5 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto bg-slate-900 text-white px-10 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-100 active:scale-95">Find Sale</button>
        </form>
    </div>

    @if(request('receipt') && !$sale)
        <div class="bg-red-50 border border-red-100 text-red-600 p-8 rounded-[32px] text-center">
            <p class="text-sm font-black uppercase tracking-widest">No sale found with receipt number #{{ request('receipt') }}</p>
        </div>
    @endif

    @if($sale)
        <!-- STEP 2: Sale Items & Return Details -->
        <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Original Sale Details</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">Date: {{ $sale->created_at->format('d M, Y h:i A') }} • Cashier: {{ $sale->user->name ?? 'System' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Amount</p>
                        <p class="text-xl font-black text-slate-900 tabular tracking-tighter">GH₵ {{ number_format($sale->total, 2) }}</p>
                    </div>
                </div>
                
                <div class="p-8">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Select Item to Return</h4>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($sale->items as $item)
                            <div x-data="{ open: false }" class="border border-slate-100 rounded-3xl p-6 hover:border-blue-200 transition-all">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center font-black text-slate-400 uppercase">
                                            {{ substr($item->product->name ?? 'P', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $item->product->name ?? 'DELETED' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">{{ $item->qty }} units @ GH₵ {{ number_format($item->unit_price, 2) }}</p>
                                        </div>
                                    </div>
                                    <button @click="open = !open" class="w-full sm:w-auto bg-blue-50 text-blue-600 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                                        Return
                                    </button>
                                </div>

                                <!-- Return Form (Expandable) -->
                                <div x-show="open" x-collapse x-cloak class="mt-8 pt-8 border-t border-slate-50">
                                    <form action="{{ route('manager.returns.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        @csrf
                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Quantity to Return</label>
                                            <input type="number" name="quantity" max="{{ $item->qty }}" min="1" value="1" required class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm">
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Refund Amount (GH₵)</label>
                                            <input type="number" step="0.01" name="refund_amount" value="{{ $item->unit_price }}" required class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-black text-sm text-red-600 tabular">
                                        </div>

                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Reason for Return</label>
                                            <select name="reason" required class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-[10px] uppercase tracking-widest text-slate-500">
                                                <option value="Damaged Item">Damaged Item</option>
                                                <option value="Expired Product">Expired Product</option>
                                                <option value="Customer Exchange">Customer Exchange</option>
                                                <option value="Wrong Item Sold">Wrong Item Sold</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-3 flex justify-end pt-4">
                                            <button type="submit" class="bg-red-600 text-white px-10 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all shadow-xl shadow-red-100 active:scale-95">
                                                Confirm Return & Restock
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
