@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Suppliers</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Manage your business partners and product sources</p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('manager.export.products') }}" class="flex-1 md:flex-none justify-center bg-white text-slate-600 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all border border-slate-100 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export
            </a>
            <a href="{{ route('manager.suppliers.create') }}" class="flex-[1.5] md:flex-none justify-center bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 active:scale-95 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                Add Supplier
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('manager.suppliers.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="w-full md:flex-1 relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search company, contact person or phone..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                <div class="absolute left-4 top-4 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">Search</button>
            @if(request('search'))
                <a href="{{ route('manager.suppliers.index') }}" class="w-full md:w-auto text-center text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Company / Name</th>
                        <th class="hidden md:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Contact Person</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Phone</th>
                        <th class="hidden sm:table-cell px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Email</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center font-black text-slate-400 group-hover:bg-white transition-all shadow-sm">
                                        {{ substr($supplier->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $supplier->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 line-clamp-1 max-w-[200px]">{{ $supplier->address }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-8 py-6">
                                <p class="text-[11px] font-black text-slate-700 uppercase">{{ $supplier->contact_person ?? 'N/A' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[11px] font-black text-slate-900 tabular">{{ $supplier->phone ?? 'N/A' }}</p>
                            </td>
                            <td class="hidden sm:table-cell px-8 py-6">
                                <p class="text-[11px] font-bold text-slate-400">{{ $supplier->email ?? 'N/A' }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manager.suppliers.edit', $supplier) }}" class="p-2 text-slate-300 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('manager.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Remove this supplier?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center opacity-30">
                                <p class="text-sm font-black uppercase tracking-[0.2em]">No suppliers found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
