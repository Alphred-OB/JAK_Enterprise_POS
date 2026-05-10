@extends('layouts.manager')

@section('content')
<div class="p-8 max-w-2xl">
    <header class="mb-12">
        <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition-all flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
            Back to Team List
        </a>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Add New Staff</h1>
        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mt-1">Register a new team member and set their permissions</p>
    </header>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-[40px] p-10 border border-slate-100 shadow-sm">
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Full Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="e.g. Kwame Mensah">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Email Address (Login ID)</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm" placeholder="e.g. kwame@jakpos.com">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Access Role</label>
                    <select name="role" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                        <option value="">Select a Role</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Manager Security PIN (4 Digits)</label>
                    <input type="text" name="pin_code" maxlength="4" class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-sm" placeholder="e.g. 1234">
                    <p class="text-[8px] font-bold text-slate-400 mt-2">Required for Managers to approve discounts.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">System Password</label>
                        <input type="password" name="password" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="flex-1 bg-slate-900 text-white py-6 rounded-[24px] font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-600 transition-all shadow-2xl shadow-slate-200 active:scale-95">
                Onboard Staff Member
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-10 py-6 bg-white border border-slate-100 rounded-[24px] text-[10px] font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 transition-all">Cancel</a>
        </div>
    </form>
</div>
@endsection
