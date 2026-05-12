@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8 max-w-2xl">
    <header class="mb-12">
        <a href="{{ route('manager.expenses.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-all flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
            Back to All Expenses
        </a>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-none">Log Expense</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Record outgoing money for your shop</p>
    </header>

    <form action="{{ route('manager.expenses.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Amount Spent (GH₵)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-slate-50 border-transparent rounded-2xl py-6 px-8 focus:ring-0 focus:bg-white focus:border-red-600 transition-all font-black text-3xl" placeholder="0.00">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Date</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Category</label>
                        <select name="category" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Description / Reason</label>
                    <textarea name="description" required rows="3" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="e.g. Electricity bill for the month of May"></textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-4">
            <button type="submit" class="w-full md:flex-1 bg-slate-900 text-white py-6 rounded-[24px] font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95">
                Save Expense Record
            </button>
            <a href="{{ route('manager.expenses.index') }}" class="w-full md:w-auto text-center px-10 py-6 bg-white border border-slate-100 rounded-[24px] text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition-all">Cancel</a>
        </div>
    </form>
</div>
@endsection
