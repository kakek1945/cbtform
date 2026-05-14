@extends('layouts.app')

@section('title', 'Ubah Password Admin')

@section('content')
    <form class="mx-auto max-w-xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                <x-icon name="shield" class="size-6" />
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Akun Admin</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Ubah Password</h1>
            </div>
        </div>

        <div class="mt-6 grid gap-4">
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Password Lama</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="current_password" type="password" required autofocus>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Password Baru</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="password" type="password" required>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Konfirmasi Password Baru</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="password_confirmation" type="password" required>
            </label>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button class="inline-flex items-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846]" type="submit">
                <x-icon name="save" class="size-5" />
                Simpan Password
            </button>
            <a class="rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50" href="{{ route('admin.dashboard') }}">Batal</a>
        </div>
    </form>
@endsection
