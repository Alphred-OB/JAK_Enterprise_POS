<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config('app.name', 'JAK POS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04);
        }
        .bg-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.03) 0px, transparent 50%);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <div class="max-w-xl w-full text-center relative" x-data="{ ready: false }" x-init="setTimeout(() => ready = true, 50)">
        
        <!-- Decoration -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-64 h-64 bg-blue-100/50 rounded-full blur-3xl -z-10"></div>
        
        <div x-show="ready" 
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-12"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-cloak>
            
            <div class="mb-12 inline-flex items-center justify-center w-32 h-32 rounded-3xl bg-white shadow-2xl shadow-blue-100/50 rotate-3 transition-transform hover:rotate-0 duration-500">
                @yield('icon')
            </div>

            <h1 class="text-7xl font-black text-slate-900 tracking-tighter mb-4">@yield('code')</h1>
            <h2 class="text-2xl font-black text-slate-800 mb-6">@yield('message')</h2>
            
            <div class="glass p-8 rounded-[2rem] mb-10 max-w-sm mx-auto">
                <p class="text-sm font-bold text-slate-500 leading-relaxed">
                    @yield('description')
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all active:scale-95 shadow-xl shadow-blue-100">
                    Go to Dashboard
                </a>
                <button onclick="window.history.back()" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-500 px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all border border-slate-100 active:scale-95">
                    Go Back
                </button>
            </div>

            <p class="mt-16 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} {{ config('app.name', 'JAK POS') }} &bull; Enterprise Reliability
            </p>
        </div>
    </div>
</body>
</html>
