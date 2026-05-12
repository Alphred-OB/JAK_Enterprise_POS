<!-- Cart Body Shared Template -->
<div class="flex-1 overflow-y-auto p-4 lg:p-6 custom-scrollbar relative flex flex-col">
    <!-- Empty State -->
    <template x-if="cart.length === 0">
        <div class="h-full flex flex-col items-center justify-center text-center p-8">
            <div class="w-20 h-20 bg-slate-50 rounded-[32px] flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-900 mb-2">READY</h3>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest leading-relaxed">Add products to start a new sale</p>
            
            <div class="hidden lg:flex mt-8 items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-xl border border-blue-100">
                <span class="text-[9px] font-black uppercase tracking-widest">F1 Search</span>
            </div>
        </div>
    </template>

    <!-- Cart Items -->
    <div class="space-y-3">
        <template x-for="(item, index) in cart" :key="index">
            <div class="group bg-white border border-slate-100 rounded-3xl p-3 lg:p-4 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50/50 transition-all duration-300">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 lg:w-14 lg:h-14 bg-slate-50 rounded-2xl flex items-center justify-center overflow-hidden shrink-0">
                        <template x-if="item.image_path">
                            <img :src="'/storage/' + item.image_path" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!item.image_path">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-black text-slate-900 leading-tight mb-1 truncate" x-text="item.name"></h4>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs font-black text-blue-600 tabular" x-text="formatCurrency(item.selling_price)"></span>
                            <div class="flex items-center bg-slate-50 rounded-xl p-0.5 border border-slate-100">
                                <button @click="removeFromCart(index)" class="w-6 h-6 lg:w-7 lg:h-7 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-white rounded-lg transition-all">-</button>
                                <span class="w-6 lg:w-8 text-center text-[10px] lg:text-xs font-black text-slate-900" x-text="item.quantity"></span>
                                <button @click="addToCart(item)" class="w-6 h-6 lg:w-7 lg:h-7 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition-all">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Footer: Totals & Actions -->
<div class="p-6 lg:p-8 border-t border-slate-50 space-y-4 lg:space-y-6 bg-white">
    <div class="space-y-3">
        <div class="flex items-center justify-between text-[10px] lg:text-xs font-black text-slate-400 uppercase tracking-widest">
            <span>Subtotal</span>
            <span class="text-slate-900 tabular" x-text="formatCurrency(subtotal)"></span>
        </div>
        <div class="flex items-center justify-between text-[10px] lg:text-xs font-black">
            <div class="flex items-center gap-2 text-slate-400 uppercase tracking-widest">
                <span>Discount</span>
                <button @click="showDiscountModal = true" class="text-blue-600 hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </button>
            </div>
            <span class="text-red-500 tabular" x-text="'- ' + formatCurrency(discount)"></span>
        </div>
        <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[10px] lg:text-xs font-black text-blue-600 uppercase tracking-widest">Total Payable</span>
            <span class="text-2xl lg:text-4xl font-black text-slate-900 tracking-tighter tabular" x-text="formatCurrency(total)"></span>
        </div>
    </div>

    <!-- Payment Methods Grid -->
    <div class="grid grid-cols-2 gap-2 lg:gap-3">
        <button @click="paymentMethod = 'cash'" 
                :class="paymentMethod === 'cash' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 ring-2 ring-blue-600 ring-offset-2' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'"
                class="py-3 lg:py-4 px-4 lg:px-6 rounded-2xl flex flex-col items-center gap-2 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <span class="text-[8px] lg:text-[9px] font-black uppercase tracking-widest">Cash</span>
        </button>
        <button @click="paymentMethod = 'momo'" 
                :class="paymentMethod === 'momo' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 ring-2 ring-blue-600 ring-offset-2' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'"
                class="py-3 lg:py-4 px-4 lg:px-6 rounded-2xl flex flex-col items-center gap-2 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
            <span class="text-[8px] lg:text-[9px] font-black uppercase tracking-widest">Momo</span>
        </button>
        <button @click="paymentMethod = 'card'" 
                :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 ring-2 ring-blue-600 ring-offset-2' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'"
                class="py-3 lg:py-4 px-4 lg:px-6 rounded-2xl flex flex-col items-center gap-2 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
            <span class="text-[8px] lg:text-[9px] font-black uppercase tracking-widest">Card</span>
        </button>
        <button @click="paymentMethod = 'debt'" 
                :class="paymentMethod === 'debt' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 ring-2 ring-blue-600 ring-offset-2' : 'bg-slate-50 text-slate-400 hover:bg-slate-100'"
                class="py-3 lg:py-4 px-4 lg:px-6 rounded-2xl flex flex-col items-center gap-2 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[8px] lg:text-[9px] font-black uppercase tracking-widest">Debt</span>
        </button>
    </div>

    <button @click="checkout()" 
            :disabled="cart.length === 0 || isProcessing"
            class="w-full py-4 lg:py-6 bg-slate-400 text-white rounded-[24px] lg:rounded-[32px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-slate-900 transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
        <span x-show="!isProcessing">Process Payment</span>
        <span x-show="isProcessing" class="flex items-center justify-center gap-3">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Processing...
        </span>
    </button>
</div>
