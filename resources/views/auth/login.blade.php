@extends('layouts.app')

@section('title', 'Masuk CBT')

@section('content')
    <section class="-mx-3 -my-6 flex min-h-screen items-center justify-center bg-[#eef2f6] px-4 py-8 sm:-mx-4 lg:-mx-8">
        <form class="w-full max-w-sm rounded-xl border border-[#d0d7de] bg-white p-6 text-center shadow-sm" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div class="mx-auto flex size-20 items-center justify-center rounded-xl border border-[#d0d7de] bg-[#eef2f6] p-2">
                <x-app-logo class="size-full object-contain" alt="Logo Sekolah" />
            </div>

            <h1 class="mt-4 text-xl font-semibold tracking-tight text-[#24292f]">{{ config('app.school_name') }}</h1>
            <p class="mt-1 text-sm font-medium text-[#57606a]">CBT Online</p>

            <label class="mt-5 block text-left">
                <span class="text-sm font-medium text-[#24292f]">Username</span>
                <input class="mt-2 w-full rounded-md border border-[#d0d7de] bg-white px-3 py-2 text-sm text-[#24292f] outline-none transition focus:border-[#0969da] focus:ring-4 focus:ring-[#0969da]/15" name="login" value="{{ old('login') }}" required autofocus>
            </label>

            <label class="mt-4 block text-left">
                <span class="text-sm font-medium text-[#24292f]">Password</span>
                <input class="mt-2 w-full rounded-md border border-[#d0d7de] bg-white px-3 py-2 text-sm text-[#24292f] outline-none transition focus:border-[#0969da] focus:ring-4 focus:ring-[#0969da]/15" name="password" type="password" required>
            </label>

            <button class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-md bg-[#0969da] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0757b8]" type="submit">
                <x-icon name="login" class="size-5" />
                Masuk
            </button>

        </form>
    </section>
@endsection
