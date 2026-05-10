@extends('layouts.manager')

@section('content')
<div class="p-8 max-w-4xl">
    <header class="mb-12">
        <a href="{{ route('manager.products.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-all flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
            Back to All Items
        </a>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Edit Item</h1>
        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mt-1">Update details for: {{ $product->name }}</p>
    </header>

    <form action="{{ route('manager.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Section: Basic Information -->
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-8">General Information</h2>
            
            <!-- Image Upload Section -->
            <div class="pb-10 mb-10 border-b border-slate-50" x-data="{ photoPreview: '{{ $product->image_path ? asset('storage/' . $product->image_path) : null }}' }">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Product Display Image</label>
                <div class="flex items-center gap-6">
                    <div class="w-32 h-32 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                        <template x-if="!photoPreview">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </template>
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" 
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer"
                               @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                               ">
                        <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-widest">Leave empty to keep current image. JPG, PNG or GIF.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Item Name</label>
                    <input type="text" name="name" value="{{ $product->name }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Category</label>
                    <select name="category_id" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Item ID / Code</label>
                    <input type="text" name="sku" value="{{ $product->sku }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                </div>
            </div>
        </div>

        <!-- Section: Pricing -->
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-8">Prices (GH₵)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Buying Price (Cost)</label>
                    <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-green-600 transition-all font-black text-lg">
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Selling Price (Retail)</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ $product->selling_price }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-lg">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Wholesale Price</label>
                    <input type="number" step="0.01" name="wholesale_price" value="{{ $product->wholesale_price }}" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-amber-600 transition-all font-black text-lg">
                </div>
            </div>
        </div>

        <!-- Section: Stock Control -->
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-8">Stock Control</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Current Stock Amount</label>
                    <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-lg">
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Alert me when stock is below</label>
                    <input type="number" name="low_stock_threshold" value="{{ $product->low_stock_threshold }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-black text-lg">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="flex-1 bg-slate-900 text-white py-6 rounded-[24px] font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95">
                Save Changes
            </button>
            <a href="{{ route('manager.products.index') }}" class="px-10 py-6 bg-white border border-slate-100 rounded-[24px] text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition-all">Discard Changes</a>
        </div>
    </form>
</div>
@endsection
