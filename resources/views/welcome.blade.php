<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Form CBT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="github-theme min-h-screen bg-[#eef2f6] text-[#24292f] antialiased">
    <main class="flex min-h-screen items-center justify-center px-4">
        <section class="w-full max-w-md rounded-xl border border-[#d0d7de] bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex size-20 items-center justify-center rounded-xl border border-[#d0d7de] bg-[#eef2f6] p-2">
                <x-app-logo class="size-full object-contain" alt="Logo CBT" />
            </div>
            <h1 class="mt-4 text-xl font-semibold tracking-tight text-[#24292f]">{{ config('app.school_name') }}</h1>
            <p class="mt-2 text-sm font-medium text-[#57606a]">CBT Online</p>
            <a class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-[#0969da] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0757b8]" href="{{ route('login') }}">
                Masuk ke Aplikasi
            </a>
        </section>
    </main>
</body>
</html>
