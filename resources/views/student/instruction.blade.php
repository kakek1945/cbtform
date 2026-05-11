@extends('layouts.app')

@section('title', 'Instruksi Ujian')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1fr_.7fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex items-center gap-3">
                <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                    <x-icon name="exam" class="size-6" />
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Instruksi Ujian</p>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57] md:text-3xl">{{ $exam->title }}</h1>
                </div>
            </div>

            <p class="mt-6 leading-7 text-slate-600">{{ $exam->instructions ?: 'Kerjakan ujian dengan jujur. Pastikan koneksi stabil dan jangan meninggalkan halaman ujian.' }}</p>

            <div class="mt-6 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-sm text-slate-500">Mata Pelajaran</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->subject }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-sm text-slate-500">Kelas</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->getAttribute('class') }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-sm text-slate-500">Durasi</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->duration_minutes }} menit</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <p class="text-sm text-slate-500">Jadwal</p>
                    <p class="mt-1 font-bold text-slate-900">{{ $exam->start_time->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <form class="rounded-3xl border border-[#0b2f57]/20 bg-[#0b2f57] p-6 text-white shadow-sm md:p-8" method="POST" action="{{ route('exam.start', $exam) }}">
            @csrf
            <div class="flex size-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20">
                <x-icon name="shield" class="size-6 text-sky-100" />
            </div>
            <h2 class="mt-5 text-2xl font-bold">Mulai dengan tertib</h2>
            <p class="mt-3 text-sm leading-6 text-sky-50/85">Timer berjalan saat tombol ditekan. Pindah tab dan keluar fullscreen akan dicatat oleh sistem.</p>

            <label class="mt-6 flex gap-3 rounded-2xl bg-white/10 p-4 text-sm ring-1 ring-white/10">
                <input id="agree" class="mt-1 size-5 rounded border-white/30 text-[#0b2f57]" type="checkbox">
                <span>Saya telah membaca dan memahami aturan ujian.</span>
            </label>

            <button id="start-button" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 font-bold text-[#0b2f57] opacity-50 transition" type="submit" disabled>
                <x-icon name="exam" class="size-5" />
                Mulai Ujian
            </button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const agree = document.getElementById('agree');
        const button = document.getElementById('start-button');
        agree.addEventListener('change', () => {
            button.disabled = !agree.checked;
            button.classList.toggle('opacity-50', !agree.checked);
        });
    </script>
@endpush
