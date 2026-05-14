<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Form CBT'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="github-theme min-h-screen bg-[#eef2f6] text-[#24292f] antialiased">
    @php
        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        $isWideContent = trim($__env->yieldContent('wideContent'));
        $navItems = auth()->check()
            ? ($isAdmin
                ? [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                    ['label' => 'Ujian', 'route' => 'admin.exams.index', 'icon' => 'exam'],
                    ['label' => 'Siswa', 'route' => 'admin.students.index', 'icon' => 'student'],
                    ['label' => 'Monitoring', 'route' => 'admin.monitoring.index', 'icon' => 'monitor'],
                    ['label' => 'Hasil Ujian', 'route' => 'admin.results.index', 'icon' => 'report'],
                    ['label' => 'Log', 'route' => 'admin.activity-logs.index', 'icon' => 'report'],
                ]
                : [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
                ])
            : [];
    @endphp

    <div class="min-h-screen bg-[#eef2f6]">
        @auth
            <header class="topbar sticky top-0 z-30 bg-[#24292f] text-white">
                <div class="topbar-inner mx-auto flex min-h-16 w-[min(1180px,calc(100%-1.5rem))] items-center justify-between gap-4 py-3">
                    <a class="flex min-w-0 items-center gap-3" href="{{ route($isAdmin ? 'admin.dashboard' : 'dashboard') }}">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-md border border-white/15 bg-white p-1.5">
                            <x-app-logo class="size-full object-contain" alt="Logo CBT" />
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-xs font-semibold uppercase tracking-[.14em] text-[#d0d7de]">{{ config('app.school_name') }}</span>
                            <span class="block truncate text-base font-semibold text-white">@yield('title', 'Form CBT')</span>
                        </span>
                    </a>

                    <div class="flex items-center gap-3">
                        <span class="hidden rounded-full border border-white/15 px-3 py-1 text-sm font-medium text-[#d0d7de] sm:inline-flex">
                            {{ auth()->user()->name }} / {{ $isAdmin ? 'Administrator' : 'Peserta' }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="inline-flex min-h-8 items-center justify-center gap-2 rounded-md border border-white/15 bg-transparent px-3 py-1.5 text-sm font-semibold text-white hover:bg-white/10" type="submit">
                                <x-icon name="logout" class="size-4" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <nav class="mx-auto flex w-[min(1180px,calc(100%-1.5rem))] gap-1 overflow-x-auto pb-3">
                    @foreach ($navItems as $item)
                        @php($sectionRoute = str($item['route'])->endsWith('.index') ? str($item['route'])->beforeLast('.')->append('.*')->toString() : null)
                        @php($active = request()->routeIs($item['route']) || ($sectionRoute && request()->routeIs($sectionRoute)))
                        <a class="inline-flex shrink-0 items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition {{ $active ? 'bg-white text-[#24292f]' : 'text-[#d0d7de] hover:bg-white/10 hover:text-white' }}" href="{{ route($item['route']) }}">
                            <x-icon :name="$item['icon']" class="size-4" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </header>
        @endauth

        <div class="min-w-0 flex-1">

            @php($mainWidth = $isWideContent ? 'w-[min(1600px,calc(100%-1rem))]' : 'w-[min(1180px,calc(100%-1.5rem))]')
            <main class="mx-auto {{ $mainWidth }} py-8">
                @if (session('status'))
                    <div class="flash mb-5 text-sm font-medium text-[#24292f]">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="errors mb-5 text-sm font-medium">
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
</body>
</html>
