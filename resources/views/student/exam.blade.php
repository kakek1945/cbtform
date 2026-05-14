@extends('layouts.app')

@section('title', 'Ujian Berlangsung')
@section('wideContent', 'true')

@section('content')
    <section class="fixed inset-0 z-40 bg-white">
        <div
            id="exam-timer"
            class="fixed right-3 top-3 z-50 rounded-full bg-[#0b2f57]/95 px-4 py-2 font-mono text-sm font-bold text-white shadow-lg ring-1 ring-white/30 backdrop-blur sm:right-5 sm:top-5"
            data-expires-at="{{ $session->expiresAt()->toIso8601String() }}"
            data-finish-url="{{ route('exam.session.finish', $session) }}"
            data-finished-url="{{ route('exam.session.finished', $session) }}"
            data-dashboard-url="{{ route('dashboard') }}"
            data-submission-status-url="{{ route('exam.session.submission-status', $session) }}"
            data-logout-url="{{ route('logout') }}"
            data-login-url="{{ route('login') }}"
        >--:--:--</div>

        <div id="exam-warning" class="fixed left-1/2 top-16 z-50 hidden w-[min(92vw,520px)] -translate-x-1/2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-900 shadow-lg sm:top-20"></div>

        <div id="time-warning-modal" class="fixed inset-0 z-[55] hidden items-center justify-center bg-red-950/75 px-4 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-3xl border border-red-200 bg-white p-6 text-center shadow-2xl">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-red-50 text-red-700 ring-1 ring-red-100">
                    <x-icon name="warning" class="size-8" />
                </div>
                <p class="mt-4 text-sm font-black uppercase tracking-[.22em] text-red-700">Sisa Waktu 3 Menit</p>
                <h2 class="mt-2 text-2xl font-black tracking-tight text-slate-950">Segera Kirim Google Form</h2>
                <p class="mt-3 text-base font-semibold leading-7 text-slate-700">
                    Tekan tombol <span class="font-black text-red-700">Kirim</span> di Google Form sekarang. Jika waktu habis sebelum jawaban dikirim, nilai tidak akan masuk ke aplikasi.
                </p>
                <button id="close-time-warning-button" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-red-700 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-red-800" type="button">
                    Saya Mengerti, Lanjut Kirim Jawaban
                </button>
            </div>
        </div>

        <div id="submission-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-3xl border border-white/20 bg-white p-6 text-center shadow-2xl">
                <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                    <x-icon name="check" class="size-7" />
                </div>
                <h2 class="mt-4 text-xl font-bold text-slate-950">Jawaban Terkirim</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Sistem sudah mendeteksi jawaban Google Form. Klik tombol di bawah untuk kembali ke dashboard siswa.</p>
                <button id="return-dashboard-button" class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#0969da] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0757b5]" type="button">
                    Kembali ke Dashboard Siswa
                </button>
            </div>
        </div>

        <div id="form-wrapper" class="h-screen w-screen overflow-hidden bg-white">
            <iframe id="google-form-frame" class="h-screen w-screen border-0" src="{{ $formUrl }}" title="Google Form Ujian" loading="eager">
                Memuat Google Form...
            </iframe>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.examSecurity = {
            tabSwitchUrl: @json(route('exam.session.tab-switch', $session)),
        };
    </script>
    <script src="{{ asset('js/exam-security.js') }}"></script>
@endpush
