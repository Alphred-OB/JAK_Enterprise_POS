@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8" x-data="{ 
    showAddModal: false, 
    showEditModal: false,
    editingCategory: { id: '', name: '', description: '' }
}">
    <!-- Header -->
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Categories</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Organize your shop inventory</p>
        </div>
        <button @click="showAddModal = true" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center justify-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            Add Category
        </button>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-50 rounded-full blur-2xl group-hover:bg-blue-50 transition-colors"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 bg-slate-50 text-slate-900 rounded-[20px] flex items-center justify-center font-black text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                            {{ substr($category->name, 0, 1) }}
                        </div>
                        <div class="flex gap-2">
                            <button @click="editingCategory = { id: '{{ $category->id }}', name: '{{ $category->name }}', description: '{{ $category->description }}' }; showEditModal = true" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <form action="{{ route('manager.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2 uppercase">{{ $category->name }}</h3>
                    <p class="text-xs font-bold text-slate-400 line-clamp-2 mb-6 uppercase tracking-widest leading-relaxed">
                        {{ $category->description ?? 'No description provided for this category.' }}
                    </p>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Products</span>
                            <span class="text-xl font-black text-slate-900 tabular tracking-tighter">{{ $category->products_count }}</span>
                        </div>
                        <a href="{{ route('manager.products.index', ['category' => $category->id]) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">View All &rarr;</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Category Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div x-show="showAddModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative bg-white rounded-[40px] shadow-2xl p-10 max-w-lg w-full mx-4 overflow-hidden border border-slate-100">
            <div class="mb-8">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">New Category</h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Add a new grouping for your products.</p>
            </div>

            <form action="{{ route('manager.categories.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Category Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm text-slate-900" placeholder="e.g. Beverages, Snacks...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Description (Optional)</label>
                    <textarea name="description" rows="4" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm text-slate-900" placeholder="Briefly describe what goes in here..."></textarea>
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-50">
                    <button type="button" @click="showAddModal = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-slate-500 bg-slate-50 uppercase tracking-widest hover:bg-slate-100 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-white bg-slate-900 uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div x-show="showEditModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative bg-white rounded-[40px] shadow-2xl p-10 max-w-lg w-full mx-4 overflow-hidden border border-slate-100">
            <div class="mb-8">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Edit Category</h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Modify category details.</p>
            </div>

            <form :action="'/manager/categories/' + editingCategory.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Category Name</label>
                    <input type="text" name="name" x-model="editingCategory.name" required class="w-full bg-slate-50 border-transparent rounded-2xl py-5 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm text-slate-900">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Description (Optional)</label>
                    <textarea name="description" x-model="editingCategory.description" rows="4" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm text-slate-900"></textarea>
                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-50">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-slate-500 bg-slate-50 uppercase tracking-widest hover:bg-slate-100 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-5 rounded-2xl text-[10px] font-black text-white bg-slate-900 uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
