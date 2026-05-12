@extends('layouts.app')

@section('title', 'Masuk CBT')

@section('content')
    <section class="-mx-3 -my-6 flex min-h-screen items-center justify-center bg-[#f6f8fa] px-4 py-8 sm:-mx-4 lg:-mx-8">
        <x-theme-toggle class="fixed right-4 top-4 z-50" />

        <form class="w-full max-w-sm rounded-xl border border-[#d0d7de] bg-white p-6 text-center shadow-sm" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="mx-auto flex size-20 items-center justify-center rounded-xl border border-[#d0d7de] bg-[#f6f8fa] p-2">
                <x-app-logo class="size-full object-contain" alt="Logo Sekolah" />
            </div>

            <h1 class="mt-4 text-xl font-semibold tracking-tight text-[#24292f]">CBT</h1>

            <label class="mt-5 block text-left">
                <span class="text-sm font-medium text-[#24292f]">Username</span>
                <input class="mt-2 w-full rounded-md border border-[#d0d7de] bg-white px-3 py-2 text-sm text-[#24292f] outline-none transition focus:border-[#0969da] focus:ring-4 focus:ring-[#0969da]/15" name="login" value="{{ old('login') }}" required autofocus>
            </label>

            <label class="mt-4 block text-left">
                <span class="text-sm font-medium text-[#24292f]">Password</span>
                <span class="mt-2 flex w-full items-center rounded-md border border-[#d0d7de] bg-white focus-within:border-[#0969da] focus-within:ring-4 focus-within:ring-[#0969da]/15">
                    <input id="password-input" class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm text-[#24292f] outline-none" name="password" type="password" required>
                    <button id="password-toggle" class="inline-flex size-10 shrink-0 items-center justify-center text-[#57606a] hover:text-[#24292f]" type="button" aria-label="Tampilkan password" title="Tampilkan password">
                        <x-icon name="eye" class="password-eye size-5" />
                        <x-icon name="eye-off" class="password-eye-off hidden size-5" />
                    </button>
                </span>
            </label>

            <button class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-md bg-[#0969da] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0757b8]" type="submit">
                <x-icon name="login" class="size-5" />
                Masuk
            </button>

        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const passwordInput = document.getElementById('password-input');
            const passwordToggle = document.getElementById('password-toggle');
            const eye = document.querySelector('.password-eye');
            const eyeOff = document.querySelector('.password-eye-off');

            passwordToggle?.addEventListener('click', () => {
                const visible = passwordInput.type === 'text';
                passwordInput.type = visible ? 'password' : 'text';
                passwordToggle.setAttribute('aria-label', visible ? 'Tampilkan password' : 'Sembunyikan password');
                passwordToggle.setAttribute('title', visible ? 'Tampilkan password' : 'Sembunyikan password');
                eye?.classList.toggle('hidden', !visible);
                eyeOff?.classList.toggle('hidden', visible);
            });
        })();
    </script>
@endpush
