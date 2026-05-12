@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-6 md:pt-8">
    <header class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Customers</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Track your customers and manage outstanding credit/debts</p>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto">
            <a href="{{ route('manager.customers.create') }}" class="w-full md:w-auto justify-center bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-95 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Register Customer
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
        <form action="{{ route('manager.customers.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone or email..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                <div class="absolute left-4 top-4 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <label class="flex items-center gap-3 bg-slate-50 px-6 py-4 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all">
                <input type="checkbox" name="has_debt" value="1" {{ request('has_debt') ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-slate-200 text-red-600 focus:ring-red-600">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Show Debt Only</span>
            </label>

            <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">Filter</button>
            
            @if(request('search') || request('has_debt'))
                <a href="{{ route('manager.customers.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer Name</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Contact Info</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Outstanding Debt</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 {{ $customer->total_debt > 0 ? 'bg-red-50 text-red-400' : 'bg-slate-50 text-slate-400' }} rounded-xl flex items-center justify-center font-black group-hover:bg-white transition-all shadow-sm">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $customer->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 line-clamp-1 max-w-[200px]">{{ $customer->address ?? 'No Address' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[11px] font-black text-slate-700 tabular">{{ $customer->phone ?? 'N/A' }}</p>
                                <p class="text-[10px] font-bold text-slate-400 lowercase mt-0.5">{{ $customer->email ?? 'N/A' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black {{ $customer->total_debt > 0 ? 'text-red-600' : 'text-slate-900' }} tabular tracking-tight">
                                        GH₵ {{ number_format($customer->total_debt, 2) }}
                                    </span>
                                    @if($customer->total_debt > 0)
                                        <span class="text-[9px] font-black text-red-400 uppercase tracking-widest mt-0.5">Payment Pending</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 {{ $customer->total_debt > 0 ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600' }} text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $customer->total_debt > 0 ? 'Overdue' : 'Good' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($customer->total_debt > 0)
                                    <button onclick="openRepaymentModal('{{ $customer->id }}', '{{ $customer->name }}', {{ $customer->total_debt }})" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-sm">
                                        Pay Debt
                                    </button>
                                    @endif

                                    <a href="{{ route('manager.customers.edit', $customer) }}" class="p-2 text-slate-300 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('manager.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer record?')">
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
                                <p class="text-sm font-black uppercase tracking-[0.2em]">No customers registered</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Repayment Modal -->
<div x-data="{
        show: false,
        customerId: '',
        customerName: '',
        maxAmount: 0,
        amount: ''
     }" 
     @open-repayment.window="
        show = true; 
        customerId = $event.detail.id;
        customerName = $event.detail.name;
        maxAmount = $event.detail.debt;
        amount = maxAmount;
     "
     x-show="show" 
     style="display: none;"
     class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
     x-transition.opacity>
    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl relative" x-on:click.away="show = false">
        <button @click="show = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 p-2 rounded-xl transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Record Repayment</h3>
        <p class="text-xs font-bold text-slate-500 mb-6">Receiving payment from <span x-text="customerName" class="text-slate-900 uppercase"></span></p>

        <form :action="'{{ url('manager/customers') }}/' + customerId + '/repayment'" method="POST">
            @csrf
            
            <div class="bg-slate-50 rounded-2xl p-4 mb-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Outstanding Debt</p>
                <p class="text-2xl font-black text-red-600">GH₵ <span x-text="maxAmount.toFixed(2)"></span></p>
            </div>

            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Amount Paid (GH₵)</label>
                <input type="number" name="amount" x-model="amount" step="0.01" min="0.01" :max="maxAmount" required
                       class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-5 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-xl text-slate-900 placeholder:text-slate-300">
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="show = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Save Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRepaymentModal(id, name, debt) {
        window.dispatchEvent(new CustomEvent('open-repayment', {
            detail: { id, name, debt }
        }));
    }
</script>
@endsection
