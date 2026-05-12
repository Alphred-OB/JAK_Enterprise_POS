<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login | {{ $settings->shop_name ?? 'JAK POS' }}</title>
    @if(isset($settings) && $settings->shop_logo)
        <link rel="icon" href="{{ asset('storage/' . $settings->shop_logo) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }
        .bg-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            50% { transform: translateX(8px); }
            75% { transform: translateX(-8px); }
        }
        .animate-shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full relative" 
         x-data="{ showHelp: false, showPassword: false, loading: false, booted: false }"
         x-init="setTimeout(() => booted = true, 50)"
         x-cloak>
        
        <div x-show="booted" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            
            <!-- Logo Area -->
            <div class="text-center mb-10">
                <div class="relative inline-block mb-6 group">
                    @if($settings->shop_logo)
                        <img src="{{ asset('storage/' . $settings->shop_logo) }}" 
                             class="w-24 h-24 object-contain rounded-2xl shadow-xl shadow-slate-200 mx-auto transition-all duration-700"
                             :class="loading ? 'scale-110 rotate-[360deg]' : 'hover:scale-105'">
                    @else
                        <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-100 mx-auto rotate-3 transition-all duration-700"
                             :class="loading ? 'scale-110 rotate-[360deg]' : 'hover:rotate-0'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">{{ $settings->shop_name ?? 'JAK POS' }}</h1>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em]">Secure Terminal Entry</p>
            </div>

            <!-- Login Card -->
            <div class="glass rounded-[2.5rem] p-10 shadow-[0_32px_120px_rgba(0,0,0,0.06)] relative overflow-hidden transition-all duration-500"
                 :class="{ 'opacity-80 scale-[0.99] blur-[2px]': loading, 'animate-shake': {{ $errors->any() ? 'true' : 'false' }} }">
                
                <!-- Help Overlay -->
                <div x-show="showHelp" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute inset-0 bg-white/95 backdrop-blur-sm z-30 flex flex-col items-center justify-center p-8 text-center"
                     style="display: none;">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">Need Access?</h3>
                    <p class="text-sm font-bold text-slate-500 mb-8 leading-relaxed">For security reasons, password resets are handled manually. Please contact your manager or administrator.</p>
                    <button type="button" @click="showHelp = false" class="w-full bg-slate-900 text-white px-6 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-colors">Return to Login</button>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6" @submit="loading = true">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-100 p-4 rounded-2xl flex items-center gap-4">
                            <div class="bg-red-500 p-1.5 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">Login Failed</p>
                                <p class="text-[11px] text-red-500 font-bold mt-0.5">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">ID / Email Address</label>
                        <div class="relative group">
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 pl-12 pr-4 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold text-slate-900 disabled:opacity-50"
                                   placeholder="cashier@jakpos.com" :disabled="loading">
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
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                   class="w-full bg-slate-50 border-2 border-transparent rounded-2xl py-4 pl-12 pr-12 focus:ring-0 focus:border-blue-600 focus:bg-white transition-all text-sm font-bold text-slate-900 disabled:opacity-50"
                                   placeholder="••••••••" :disabled="loading">
                            <div class="absolute left-4 top-4 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute right-4 top-4 text-slate-400 hover:text-blue-600 transition-colors">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.888 9.888L5.123 5.123m13.754 13.754l-4.242-4.242M21 12c-1.274 4.057-5.064 7-9.542 7-1.35 0-2.622-.268-3.784-.755m1.34-11.454A10.01 10.01 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            <label for="remember" class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Remember Me</label>
                        </div>
                        <button type="button" @click="showHelp = true" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-700 transition-colors disabled:opacity-50" :disabled="loading">Forgot Password?</button>
                    </div>

                    <button type="submit" :disabled="loading"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-[1.25rem] py-5 font-black uppercase tracking-[0.2em] text-xs shadow-2xl shadow-blue-100 transition-all active:scale-[0.98] flex items-center justify-center gap-3 disabled:bg-blue-400 disabled:shadow-none">
                        <template x-if="!loading">
                            <span class="flex items-center gap-3">
                                Unlock Terminal
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="flex items-center gap-3">
                                Verifying...
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </template>
                    </button>
                </form>
            </div>

            <!-- PWA Install Button -->
            <div class="mt-10 text-center" x-data="{ ready: false }" x-init="if(window.deferredPrompt) ready = true" @pwa-ready.window="ready = true">
                <button x-show="ready" @click="window.installPWA()" 
                        class="inline-flex items-center gap-3 bg-white hover:bg-slate-50 text-slate-500 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all shadow-sm active:scale-95 border border-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Download POS App
                </button>
            </div>
        </div>
    </div>

    <script>
        // PWA Installation Logic for Login Page (Strict Install Only)
        window.deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            window.deferredPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-ready'));
        });

        window.installPWA = async function() {
            if (window.deferredPrompt) {
                window.deferredPrompt.prompt();
                const { outcome } = await window.deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('User accepted the PWA prompt');
                }
                window.deferredPrompt = null;
                window.location.reload(); 
            }
        };

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW registration failed:', err));
            });
        }
    </script>
</body>
</html>
