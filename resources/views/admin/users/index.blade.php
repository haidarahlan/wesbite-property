@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Hak Akses')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ 
    resetModalOpen: false, 
    createModalOpen: false, 
    roleModalOpen: false,
    selectedUserId: null, 
    selectedUserName: '', 
    selectedUserEmail: '',
    selectedUserRole: 'admin'
}">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('admin.units.index') }}" class="hover:text-[#C5A038]">Dashboard</a>
                <span>/</span>
                <span class="text-gray-200">Manajemen User</span>
            </div>
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-users-gear text-[#C5A038]"></i>
                <span>Daftar Pengguna & Hak Akses</span>
            </h1>
            <p class="text-xs text-gray-400">
                @if(auth()->user()->isManagingDirector())
                    Sebagai <strong>Managing Director</strong>, Anda memiliki hak penuh untuk menambah akun dan mengubah role pengguna.
                @else
                    Sebagai <strong>Superadmin</strong>, Anda dapat melihat daftar pengguna, mengubah status aktif, dan mereset kata sandi.
                @endif
            </p>
        </div>

        @if(auth()->user()->isManagingDirector())
            <div>
                <button type="button" @click="createModalOpen = true"
                        class="px-4 py-2 bg-[#C5A038] hover:bg-[#b08d2e] text-white text-xs font-semibold rounded-xl shadow-lg shadow-[#C5A038]/20 flex items-center gap-2 transition-all cursor-pointer">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Tambah Pengguna Baru</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl p-4 shadow-sm flex flex-wrap items-center gap-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="w-full flex flex-wrap items-center gap-3">
            <div class="flex-grow min-w-[240px] relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau no WhatsApp..." 
                       class="w-full pl-9 pr-4 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-400 focus:outline-none focus:border-[#C5A038]">
            </div>

            <select name="role" onchange="this.form.submit()" class="px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-300 focus:outline-none">
                <option value="all">Semua Role</option>
                <option value="managing_director" {{ request('role') == 'managing_director' ? 'selected' : '' }}>Managing Director</option>
                <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin Marketing</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-[#28323E] hover:bg-[#34404F] text-gray-200 text-xs font-semibold rounded-xl">
                Filter
            </button>
        </form>
    </div>

    <!-- Table Users -->
    <div class="bg-[#1F2731] border border-[#2B3543] rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-[#161B22] border-b border-[#2B3543] text-gray-400 font-semibold uppercase tracking-wider">
                <tr>
                    <th class="py-3.5 px-4">Pengguna</th>
                    <th class="py-3.5 px-4">Kontak</th>
                    <th class="py-3.5 px-4">Role Akses</th>
                    <th class="py-3.5 px-4">Status Akun</th>
                    <th class="py-3.5 px-4">Terdaftar Sejak</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#2B3543] text-gray-300">
                @forelse($users as $user)
                    <tr class="hover:bg-[#28323E]/50 transition-colors">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#C5A038]/20 border border-[#C5A038]/40 flex items-center justify-center text-[#C5A038] font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-white block">{{ $user->name }}</span>
                                    <span class="text-[11px] text-gray-400 font-mono">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-gray-300 font-mono">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                @if($user->role === 'managing_director')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C5A038]/20 text-[#C5A038] border border-[#C5A038]/40 shadow-sm">
                                        <i class="fa-solid fa-crown mr-1"></i> MANAGING DIRECTOR
                                    </span>
                                @elseif($user->role === 'superadmin')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-950/80 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-shield-halved mr-1"></i> SUPERADMIN
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-950/80 text-blue-400 border border-blue-500/30">
                                        ADMIN MARKETING
                                    </span>
                                @endif

                                @if(auth()->user()->isManagingDirector() && $user->id !== auth()->id())
                                    <button type="button" 
                                            @click="roleModalOpen = true; selectedUserId = '{{ $user->id }}'; selectedUserName = '{{ $user->name }}'; selectedUserRole = '{{ $user->role }}'"
                                            class="text-gray-400 hover:text-[#C5A038] p-1 rounded hover:bg-[#161B22]" title="Ubah Role Akun">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($user->is_active)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-950/80 text-red-400 border border-red-500/30">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-[11px] text-gray-400">
                            {{ $user->created_at->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <!-- Tombol Reset Password (Trigger Modal) -->
                                <button type="button" 
                                        @click="resetModalOpen = true; selectedUserId = '{{ $user->id }}'; selectedUserName = '{{ $user->name }}'; selectedUserEmail = '{{ $user->email }}'"
                                        class="px-2.5 py-1.5 rounded-lg bg-amber-500/10 text-[#C5A038] hover:bg-[#C5A038] hover:text-white transition-all text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-key text-[10px]"></i>
                                    <span>Reset Password</span>
                                </button>

                                <!-- Toggle Status Aktif / Nonaktif -->
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" onsubmit="return confirm('Ubah status aktif akun ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg {{ $user->is_active ? 'bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white' : 'bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-white' }} transition-colors cursor-pointer"
                                                title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="fa-solid {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            Tidak ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $users->links() }}
    </div>

    <!-- Modal Reset Password Superadmin -->
    <div x-show="resetModalOpen" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
        
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl" @click.away="resetModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#2B3543] mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-key text-[#C5A038]"></i>
                    <span>Reset Password Pengguna</span>
                </h3>
                <button @click="resetModalOpen = false" class="text-gray-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <p class="text-xs text-gray-300 mb-4 leading-relaxed">
                Anda akan mereset kata sandi untuk pengguna <strong class="text-white" x-text="selectedUserName"></strong> (<span class="font-mono text-amber-400" x-text="selectedUserEmail"></span>).
            </p>

            <form :action="'{{ url('/admin/users') }}/' + selectedUserId + '/reset-password'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Password Baru</label>
                    <input type="password" name="new_password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required minlength="8" placeholder="Ulangi password baru"
                           class="w-full px-3.5 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="resetModalOpen = false" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Tambah Pengguna Baru (Khusus Managing Director) -->
    @if(auth()->user()->isManagingDirector())
        <div x-show="createModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-[#2B3543] mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-plus text-[#C5A038]"></i>
                        <span>Tambah Pengguna Baru</span>
                    </h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Nama Lengkap <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                               class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Alamat Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" required placeholder="nama@wastuproperty.co.id"
                               class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">No. WhatsApp</label>
                            <input type="text" name="phone" placeholder="08xxxxxxxxxx"
                                   class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">Role Akun <span class="text-red-400">*</span></label>
                            <select name="role" required class="w-full px-3 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                                <option value="admin">Admin Marketing</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="managing_director">Managing Director</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Password Awal <span class="text-red-400">*</span></label>
                        <input type="password" name="password" required minlength="6" value="123456"
                               class="w-full px-3.5 py-2 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 focus:outline-none focus:border-[#C5A038]">
                        <span class="text-[10px] text-gray-400 mt-1 block">Default: 123456 (dapat diubah nanti)</span>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-[#2B3543]">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 3: Ubah Role Pengguna (Khusus Managing Director) -->
        <div x-show="roleModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" style="display: none;">
            <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-6 max-w-sm w-full shadow-2xl" @click.away="roleModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-[#2B3543] mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-[#C5A038]"></i>
                        <span>Ubah Hak Akses Role</span>
                    </h3>
                    <button @click="roleModalOpen = false" class="text-gray-400 hover:text-white">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <p class="text-xs text-gray-300 mb-4 leading-relaxed">
                    Ubah peran akun untuk pengguna <strong class="text-white" x-text="selectedUserName"></strong>.
                </p>

                <form :action="'{{ url('/admin/users') }}/' + selectedUserId + '/role'" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Pilih Role Baru</label>
                        <div class="space-y-2">
                            <label class="border border-[#2B3543] rounded-xl p-3 flex items-center gap-2.5 cursor-pointer hover:border-[#C5A038] has-[:checked]:border-[#C5A038] has-[:checked]:bg-[#C5A038]/10 block">
                                <input type="radio" name="role" value="admin" x-model="selectedUserRole" class="text-[#C5A038]">
                                <div>
                                    <div class="font-bold text-white text-xs">Admin Marketing</div>
                                    <div class="text-[10px] text-gray-400">Unit, Leads & Survei lapangan mandiri</div>
                                </div>
                            </label>

                            <label class="border border-[#2B3543] rounded-xl p-3 flex items-center gap-2.5 cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-400 has-[:checked]:bg-amber-950/20 block">
                                <input type="radio" name="role" value="superadmin" x-model="selectedUserRole" class="text-amber-400">
                                <div>
                                    <div class="font-bold text-amber-400 text-xs">Superadmin</div>
                                    <div class="text-[10px] text-gray-400">Semua modul CRM & CMS kecuali Executive Dashboard</div>
                                </div>
                            </label>

                            <label class="border border-[#2B3543] rounded-xl p-3 flex items-center gap-2.5 cursor-pointer hover:border-[#C5A038] has-[:checked]:border-[#C5A038] has-[:checked]:bg-[#C5A038]/20 block">
                                <input type="radio" name="role" value="managing_director" x-model="selectedUserRole" class="text-[#C5A038]">
                                <div>
                                    <div class="font-bold text-[#C5A038] text-xs">Managing Director</div>
                                    <div class="text-[10px] text-gray-400">Akses penuh 100% termasuk Executive Dashboard</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-[#2B3543]">
                        <button type="button" @click="roleModalOpen = false" class="px-4 py-2 bg-[#232B35] hover:bg-[#2B3543] text-gray-300 text-xs font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold rounded-xl shadow-lg">
                            Simpan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
