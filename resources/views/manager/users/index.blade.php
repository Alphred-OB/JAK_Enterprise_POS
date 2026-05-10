@extends('layouts.manager')

@section('content')
<div class="p-8">
    <header class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Staff & Roles</h1>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Manage your team and their system permissions</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('manager.export.staff') }}" class="bg-white text-slate-600 px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all border border-slate-100 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export CSV
            </a>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 active:scale-95 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Add New Staff
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
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="w-full bg-slate-50 border-transparent rounded-2xl py-4 pl-12 pr-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-bold text-sm">
                <div class="absolute left-4 top-4 text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <select name="role" class="bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-[10px] uppercase tracking-widest text-slate-500">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrators</option>
                <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Managers</option>
                <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashiers</option>
            </select>

            <select name="status" class="bg-slate-50 border-transparent rounded-2xl py-4 px-6 focus:ring-0 focus:bg-white focus:border-blue-600 transition-all font-black text-[10px] uppercase tracking-widest text-slate-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Staff</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Staff</option>
            </select>

            <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all">Apply</button>
            
            @if(request()->anyFilled(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-red-500 transition-all">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Name & Email</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Role</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Joined</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center font-black text-slate-400 group-hover:bg-white transition-all shadow-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $user->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 {{ $user->role == 'admin' ? 'bg-red-50 text-red-600' : ($user->role == 'manager' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600') }} text-[9px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    @if($user->trashed())
                                        <div class="w-2 h-2 rounded-full bg-red-500 shadow-lg shadow-red-200"></div>
                                        <span class="text-[10px] font-black text-red-400 uppercase tracking-widest">Fired (Archived)</span>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-lg shadow-green-200 animate-pulse"></div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight">{{ $user->created_at->format('d M, Y') }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->trashed())
                                        <form action="{{ route('admin.users.restore', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to restore this staff member?');">
                                            @csrf
                                            <button type="submit" class="p-2 text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all inline-block" title="Restore Staff">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-300 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all inline-block" title="Edit Profile">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        
                                        @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to fire this staff member? They will lose access to the system immediately, but their historical data will be archived.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all inline-block" title="Fire Staff">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center opacity-30">
                                <p class="text-sm font-black uppercase tracking-[0.2em]">No staff members found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
