@extends('layouts.app')

@section('title', 'Ujian Selesai')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
        <div class="rounded-3xl border border-[#0b2f57]/20 bg-[#0b2f57] p-6 text-white shadow-sm md:p-8">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20">
                <x-icon name="shield" class="size-6 text-sky-100" />
            </div>
            <p class="mt-6 text-sm font-semibold uppercase tracking-[.18em] text-sky-100">Ujian Selesai</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Terima kasih, {{ $session->user->name }}.</h1>
            <p class="mt-4 leading-7 text-sky-50/85">Sesi ujian sudah tercatat. Pastikan jawaban Google Form sudah terkirim sebelum meninggalkan ruangan.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 font-bold text-[#0b2f57]" href="{{ route('dashboard') }}">
                    <x-icon name="dashboard" class="size-5" />
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="inline-flex items-center gap-2 rounded-2xl bg-sky-100 px-5 py-3 font-bold text-[#0b2f57] hover:bg-sky-200" type="submit">
                        <x-icon name="login" class="size-5" />
                        Ke Halaman Login
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Ringkasan</p>
                    <h2 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Detail Sesi</h2>
                </div>
                <x-icon name="report" class="size-8 text-[#0b2f57]" />
            </div>

            <dl class="mt-6 grid gap-3">
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Nama Siswa</dt><dd class="font-bold">{{ $session->user->name }}</dd></div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Ujian</dt><dd class="font-bold">{{ $session->exam->title }}</dd></div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Mulai</dt><dd class="font-bold">{{ $session->started_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Selesai</dt><dd class="font-bold">{{ $session->finished_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Status</dt><dd class="font-bold">{{ str_replace('_', ' ', $session->status) }}</dd></div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100"><dt class="text-slate-500">Pindah Tab</dt><dd class="font-bold">{{ $session->tab_switch_count }}</dd></div>
            </dl>
        </div>
    </section>
@endsection
