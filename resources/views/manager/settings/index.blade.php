@extends('layouts.manager')

@section('content')
<div class="min-h-screen bg-[#fafbfc] p-4 md:p-8 pt-20 lg:pt-8">
    <header class="mb-10 text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Shop Settings</h1>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Customize your brand identity and system defaults</p>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 p-6 rounded-3xl mb-8 flex items-center justify-between">
            <span class="text-sm font-black uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-4xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left: Branding -->
                <div class="lg:col-span-1">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-6">Brand Identity</h3>
                    
                    <div x-data="{ photoPreview: '{{ $settings->shop_logo ? asset('storage/' . $settings->shop_logo) : null }}' }" class="space-y-6">
                        <div class="w-full aspect-square bg-white rounded-[40px] border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden group relative">
                            <template x-if="!photoPreview">
                                <div class="text-center p-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Logo Uploaded</p>
                                </div>
                            </template>
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-contain p-8">
                            </template>

                            <label class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center cursor-pointer">
                                <span class="text-[10px] font-black text-white uppercase tracking-widest bg-blue-600 px-6 py-3 rounded-xl shadow-xl">Change Logo</span>
                                <input type="file" name="logo" class="hidden" @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                ">
                            </label>
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase text-center tracking-widest">Square PNG or JPG. Max 2MB.</p>
                    </div>
                </div>

                <!-- Right: Configuration -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm space-y-8">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Official Shop Name</label>
                            <input type="text" name="shop_name" value="{{ $settings->shop_name }}" required class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-slate-900">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ $settings->currency_symbol }}" required class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-slate-900 tabular">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Shop Phone Number</label>
                                <input type="text" name="shop_phone" value="{{ $settings->shop_phone }}" class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Business Address</label>
                            <textarea name="shop_address" rows="2" class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900">{{ $settings->shop_address }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Receipt Footer Note</label>
                            <textarea name="receipt_footer" rows="3" placeholder="e.g. Items sold are not returnable. Thank you!" class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-slate-900 text-sm leading-relaxed">{{ $settings->receipt_footer }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-12 py-5 rounded-[20px] md:rounded-[24px] font-black text-[10px] md:text-xs uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95 text-center">
                            Save Configuration
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
