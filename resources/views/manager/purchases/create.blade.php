@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-8" x-data="stockInForm()">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <header class="flex items-center justify-between mb-10">
            <div>
                <a href="{{ route('manager.purchases.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Back to History
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Record Stock-In</h1>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Invoice Value</span>
                <p class="text-3xl font-black text-slate-900 tabular tracking-tighter">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} <span x-text="formatCurrency(grandTotal)"></span></p>
            </div>
        </header>

        <form action="{{ route('manager.purchases.store') }}" method="POST" id="purchaseForm" class="space-y-8">
            @csrf
            
            <!-- Details -->
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <h2 class="text-xl font-black text-slate-900 tracking-tight mb-8">Delivery Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Supplier</label>
                        <select name="supplier_id" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                            <option value="">Select Supplier...</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="text-xs text-red-500 font-bold mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Delivery Notes (Optional)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="Invoice #, driver name, etc.">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 p-10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">Products Received</h2>
                    <button type="button" @click="addItem()" class="bg-blue-50 text-blue-600 px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                        Add Row
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex flex-col md:flex-row items-end gap-4 p-4 bg-slate-50 rounded-2xl relative group">
                            
                            <!-- Remove Button -->
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="absolute -right-3 -top-3 w-8 h-8 bg-white text-slate-400 hover:text-red-600 hover:border-red-600 border border-slate-200 rounded-full flex items-center justify-center transition-all shadow-sm z-10 opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>

                            <!-- Product -->
                            <div class="w-full md:w-2/5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" x-show="index === 0">Product</label>
                                <select x-model="item.product_id" :name="'items['+index+'][product_id]'" required class="w-full bg-white border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:border-blue-600 transition-all font-bold text-sm shadow-sm" @change="updateUnitCost(index)">
                                    <option value="">Select...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}">{{ $product->name }} (In Stock: {{ $product->stock_quantity }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Qty -->
                            <div class="w-full md:w-1/5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" x-show="index === 0">Qty Received</label>
                                <input type="number" x-model.number="item.quantity" :name="'items['+index+'][quantity]'" min="1" required class="w-full bg-white border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:border-blue-600 transition-all font-black text-sm shadow-sm tabular text-center">
                            </div>

                            <!-- Unit Cost -->
                            <div class="w-full md:w-1/5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2" x-show="index === 0">Unit Cost</label>
                                <input type="number" step="0.01" x-model.number="item.unit_cost" :name="'items['+index+'][unit_cost]'" min="0" required class="w-full bg-white border-transparent rounded-xl py-3 px-4 focus:ring-0 focus:border-blue-600 transition-all font-black text-sm shadow-sm tabular text-right">
                            </div>

                            <!-- Subtotal -->
                            <div class="w-full md:w-1/5 bg-slate-200/50 rounded-xl py-3 px-4 flex items-center justify-end h-[46px]">
                                <span class="font-black text-slate-900 tabular">{{ \App\Models\Setting::first()->currency_symbol ?? 'GH₵' }} <span x-text="formatCurrency(item.quantity * item.unit_cost)"></span></span>
                            </div>
                        </div>
                    </template>
                </div>
                
                @error('items') <p class="text-xs text-red-500 font-bold mt-4">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4">
                <button type="submit" :disabled="isSubmitting || items.length === 0" class="bg-slate-900 text-white px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95 disabled:opacity-50">
                    <span x-text="isSubmitting ? 'Recording...' : 'Confirm Delivery & Update Stock'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function stockInForm() {
    return {
        items: [
            { product_id: '', quantity: 1, unit_cost: 0 }
        ],
        isSubmitting: false,
        
        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_cost), 0);
        },

        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        updateUnitCost(index) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.cost) {
                this.items[index].unit_cost = parseFloat(option.dataset.cost);
            }
        },

        formatCurrency(amount) {
            if(isNaN(amount)) return '0.00';
            return parseFloat(amount).toFixed(2);
        }
    }
}

document.getElementById('purchaseForm').addEventListener('submit', function() {
    // Add Alpine component check logic here if needed, but we can just let Alpine disable the button
});
</script>
@endsection
