<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Form CBT'))</title>
    <script>
        (() => {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.classList.toggle('dark', theme === 'dark' || (!theme && prefersDark));
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="github-theme min-h-screen bg-[#f6f8fa] text-[#24292f] antialiased">
    @php
        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        $isWideContent = trim($__env->yieldContent('wideContent'));
        $navItems = auth()->check()
            ? ($isAdmin
                ? [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                    ['label' => 'Ujian', 'route' => 'admin.exams.index', 'icon' => 'exam'],
                    ['label' => 'Monitoring', 'route' => 'admin.monitoring.index', 'icon' => 'monitor'],
                    ['label' => 'Hasil Ujian', 'route' => 'admin.results.index', 'icon' => 'report'],
                ]
                : [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
                ])
            : [];
    @endphp

    <div class="min-h-screen bg-[#f6f8fa] lg:flex">
        @if ($isAdmin)
            <aside class="hidden w-72 shrink-0 border-r border-[#d0d7de] bg-white text-[#24292f] lg:flex lg:min-h-screen lg:flex-col">
                <div class="flex h-20 items-center gap-3 border-b border-[#d0d7de] px-6">
                    <div class="flex size-11 items-center justify-center rounded-xl border border-[#d0d7de] bg-[#f6f8fa] p-1.5">
                        <x-app-logo class="size-full object-contain" alt="Logo CBT" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[.16em] text-[#57606a]">Aplikasi CBT</p>
                        <p class="text-base font-semibold">Form CBT Sekolah</p>
                    </div>
                </div>

                <nav class="space-y-1 px-3 py-4">
                    @foreach ($navItems as $item)
                        @php($sectionRoute = str($item['route'])->contains('.') ? str($item['route'])->beforeLast('.')->append('.*')->toString() : $item['route'])
                        @php($active = request()->routeIs($item['route']) || request()->routeIs($sectionRoute))
                        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-[#ddf4ff] text-[#0969da]' : 'text-[#24292f] hover:bg-[#f6f8fa]' }}" href="{{ route($item['route']) }}">
                            <x-icon :name="$item['icon']" class="size-5" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-auto border-t border-[#d0d7de] p-3">
                    <div class="rounded-lg bg-[#f6f8fa] p-3 text-sm text-[#57606a]">
                        <p class="font-semibold text-[#24292f]">{{ auth()->user()->name }}</p>
                        <p class="mt-1">{{ $isAdmin ? 'Administrator' : 'Peserta Ujian' }}</p>
                    </div>
                    <form class="mt-3" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-[#d0d7de] bg-white px-3 py-2 text-sm font-semibold text-[#24292f] hover:bg-[#f6f8fa]" type="submit">
                            <x-icon name="logout" class="size-4" />
                            Logout
                        </button>
                    </form>
                </div>
            </aside>
        @endif

        <div class="min-w-0 flex-1">
            @if ($isAdmin)
            <header class="sticky top-0 z-30 border-b border-[#d0d7de] bg-white/95 text-[#24292f] backdrop-blur">
                <div class="mx-auto flex h-16 w-[min(1280px,calc(100%-1.5rem))] items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-[#d0d7de] bg-[#f6f8fa] p-1.5 lg:hidden">
                            <x-app-logo class="size-full object-contain" alt="Logo CBT" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[.16em] text-[#57606a]">Sistem Ujian</p>
                            <h1 class="truncate text-lg font-semibold text-[#24292f]">@yield('title', 'Form CBT')</h1>
                        </div>
                    </div>

                    <x-theme-toggle class="shrink-0" />
                </div>

                @auth
                    <nav class="flex gap-2 overflow-x-auto border-t border-[#d0d7de] bg-white px-3 py-2 lg:hidden">
                        @foreach ($navItems as $item)
                            @php($sectionRoute = str($item['route'])->contains('.') ? str($item['route'])->beforeLast('.')->append('.*')->toString() : $item['route'])
                            @php($active = request()->routeIs($item['route']) || request()->routeIs($sectionRoute))
                            <a class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium {{ $active ? 'bg-[#ddf4ff] text-[#0969da]' : 'text-[#24292f] hover:bg-[#f6f8fa]' }}" href="{{ route($item['route']) }}">
                                <x-icon :name="$item['icon']" class="size-4" />
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                @endauth
            </header>
            @endif

            @if (auth()->check() && ! $isAdmin && ! $isWideContent)
                <x-theme-toggle class="fixed right-4 top-4 z-50" />
            @endif

            @php($mainWidth = $isWideContent ? 'w-[min(1600px,calc(100%-1rem))]' : 'w-[min(1280px,calc(100%-1.5rem))]')
            <main class="mx-auto {{ $mainWidth }} {{ $isAdmin || ! auth()->check() ? 'py-6' : 'py-4 sm:py-6' }}">
                @if (session('status'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-900">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        (() => {
            const buttons = document.querySelectorAll('.theme-toggle');
            const moonIcons = document.querySelectorAll('.theme-toggle-icon-moon');
            const sunIcons = document.querySelectorAll('.theme-toggle-icon-sun');

            const refreshTheme = () => {
                const dark = document.documentElement.classList.contains('dark');

                moonIcons.forEach((icon) => icon.classList.toggle('hidden', dark));
                sunIcons.forEach((icon) => icon.classList.toggle('hidden', !dark));
                buttons.forEach((button) => {
                    button.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
                    button.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
                });
            };

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const dark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                    refreshTheme();
                });
            });

            refreshTheme();
        })();
    </script>
</body>
</html>
