<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Admin - CRM PT Wastu Pratama Wijaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #14181E; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-gray-200">
    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/10 border border-[#C5A038]/30 text-[#C5A038] mb-3 shadow-lg">
                <i class="fa-solid fa-user-plus text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold text-[#C5A038] uppercase tracking-wider font-serif">REGISTRASI ADMIN</h1>
            <p class="text-xs text-gray-400 mt-1">Akun yang didaftarkan otomatis berstatus sebagai <strong>Admin</strong></p>
        </div>

        <!-- Form Card -->
        <div class="bg-[#1F2731] border border-[#2B3543] rounded-3xl p-7 sm:p-8 shadow-2xl">
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                               placeholder="Nama Pegawai / Admin"
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="admin@wastuproperty.co.id"
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nomor WhatsApp</label>
                    <div class="relative">
                        <i class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="081234567890"
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="password" name="password" required
                               placeholder="Minimal 8 karakter"
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <i class="fa-solid fa-shield-check absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="password" name="password_confirmation" required
                               placeholder="Ulangi kata sandi"
                               class="w-full pl-9 pr-4 py-2.5 bg-[#14181E] border border-[#2B3543] rounded-xl text-xs text-gray-200 placeholder-gray-500 focus:outline-none focus:border-[#C5A038]">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-[#C5A038] hover:bg-[#B08C2B] text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-amber-900/30 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                        Daftar Sebagai Admin
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-[#2B3543] text-center text-xs text-gray-400">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-[#C5A038] hover:underline font-semibold">Masuk di Sini</a>
            </div>
        </div>
    </div>
</body>
</html>
