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
        >--:--:--</div>

        <div id="exam-warning" class="fixed left-1/2 top-16 z-50 hidden w-[min(92vw,520px)] -translate-x-1/2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-900 shadow-lg sm:top-20"></div>

        <div id="form-wrapper" class="h-screen w-screen overflow-hidden bg-white">
            <iframe class="h-screen w-screen border-0" src="{{ $formUrl }}" title="Google Form Ujian" loading="eager">
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
