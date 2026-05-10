<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | JAK POS Enterprise</title>
    @if(isset($settings) && $settings->shop_logo)
        <link rel="icon" href="{{ asset('storage/' . $settings->shop_logo) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .bg-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full" x-data="{ showHelp: false }">
        <!-- Logo Area -->
        <div class="text-center mb-10">
            @if($settings->shop_logo)
                <img src="{{ asset('storage/' . $settings->shop_logo) }}" class="w-24 h-24 object-contain rounded-2xl shadow-xl shadow-slate-200 mx-auto mb-6 hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-200 mx-auto mb-6 rotate-3 hover:rotate-0 transition-transform duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            @endif
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">{{ $settings->shop_name ?? 'JAK POS' }}</h1>
            <p class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Secure Terminal Entry</p>
        </div>

        <!-- Login Card -->
        <div class="glass rounded-3xl p-10 shadow-[0_32px_120px_rgba(0,0,0,0.08)] relative overflow-hidden">
            <!-- Forgot Password Help Message -->
            <div x-show="showHelp" style="display: none;" 
                 x-transition.opacity
                 class="absolute inset-0 bg-white/95 backdrop-blur-sm z-10 flex flex-col items-center justify-center p-8 text-center rounded-3xl">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Need Access?</h3>
                <p class="text-sm font-bold text-slate-500 mb-6 leading-relaxed">For security reasons, password resets are handled manually. Please contact your Manager or Administrator to reset your access code.</p>
                <button type="button" @click="showHelp = false" class="bg-slate-900 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-colors">Return to Login</button>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-100 p-4 rounded-2xl">
                        <p class="text-xs font-bold text-red-600 uppercase tracking-widest text-center">Login Failed</p>
                        <p class="text-[10px] text-red-500 font-bold text-center mt-1">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">ID / Email Address</label>
                    <div class="relative group">
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 pl-12 pr-4 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold"
                               placeholder="cashier@jakpos.com">
                        <div class="absolute left-4 top-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Secure Password</label>
                    <div class="relative group">
                        <input type="password" name="password" required
                               class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 pl-12 pr-4 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold"
                               placeholder="••••••••">
                        <div class="absolute left-4 top-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center px-2">
                    <button type="button" @click="showHelp = true" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-700 transition-colors">Forgot Password?</button>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-2xl py-5 font-black uppercase tracking-[0.2em] text-sm shadow-2xl shadow-blue-100 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    Unlock Terminal
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>


    </div>
</body>
</html>
