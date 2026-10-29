<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Safety Talk') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="relative min-h-screen flex items-center justify-center overflow-hidden px-6 py-12
                    bg-gradient-to-br from-indigo-100 via-violet-100 to-rose-100">

            {{-- Mesh gradient halus: ubah warna blob di sini untuk ganti palet --}}
            <div class="pointer-events-none absolute -top-32 -left-28 h-[32rem] w-[32rem] rounded-full bg-indigo-400/40 blur-3xl"></div>
            <div class="pointer-events-none absolute top-1/4 -right-32 h-[28rem] w-[28rem] rounded-full bg-fuchsia-300/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-16 h-[30rem] w-[30rem] rounded-full bg-amber-200/60 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 right-1/4 h-[26rem] w-[26rem] rounded-full bg-sky-300/50 blur-3xl"></div>

            {{-- Konten tanpa kartu: langsung di atas latar --}}
            <div class="relative z-10 w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>