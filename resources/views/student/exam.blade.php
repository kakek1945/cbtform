@extends('layouts.app')

@section('title', 'Ujian Berlangsung')
@section('wideContent', 'true')

@section('content')
    <section class="-mx-2 rounded-3xl border border-slate-200 bg-white p-2 shadow-sm sm:-mx-3 lg:-mx-4">
        <div class="sticky top-2 z-20 flex flex-col justify-between gap-2 rounded-2xl border border-slate-200 bg-white/95 px-3 py-2 shadow-sm backdrop-blur lg:flex-row lg:items-center">
            <div class="min-w-0">
                <p class="truncate text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                    {{ auth()->user()->name }} / {{ auth()->user()->nis }} / {{ auth()->user()->getAttribute('class') }}
                </p>
                <h1 class="truncate text-sm font-bold tracking-tight text-[#0b2f57] sm:text-base">
                    {{ $exam->title }}
                    <span class="font-semibold text-slate-400">/ {{ $exam->subject }}</span>
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <div
                    id="exam-timer"
                    class="rounded-xl bg-[#0b2f57] px-3 py-2 font-mono text-sm font-bold text-white sm:text-base"
                    data-expires-at="{{ $session->expiresAt()->toIso8601String() }}"
                    data-finish-url="{{ route('exam.session.finish', $session) }}"
                    data-finished-url="{{ route('exam.session.finished', $session) }}"
                >--:--:--</div>

                <button id="fullscreen-button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50" type="button">
                    <x-icon name="monitor" class="size-4 text-[#0b2f57]" />
                    Fullscreen
                </button>

                <form method="POST" action="{{ route('exam.session.finish', $session) }}">
                    @csrf
                    <button class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700" onclick="return confirm('Selesaikan ujian sekarang? Pastikan jawaban Google Form sudah dikirim.')" type="submit">
                        <x-icon name="logout" class="size-4" />
                        Selesai
                    </button>
                </form>
            </div>
        </div>

        <div id="exam-warning" class="mt-2 hidden rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900"></div>

        <div id="form-wrapper" class="mt-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
            <iframe class="h-[calc(100vh-7.25rem)] min-h-[820px] w-full" src="{{ $formUrl }}" title="Google Form Ujian" loading="eager">
                Memuat Google Form...
            </iframe>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.examSecurity = {
            tabSwitchUrl: @json(route('exam.session.tab-switch', $session)),
            fullscreenExitUrl: @json(route('exam.session.fullscreen-exit', $session)),
        };
    </script>
    <script src="{{ asset('js/exam-security.js') }}"></script>
@endpush
