@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[.78fr_1.22fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                    <x-icon name="student" class="size-6" />
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Peserta Ujian</p>
                    <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">{{ $student->name }}</h1>
                </div>
            </div>

            <dl class="mt-6 grid gap-3 text-sm">
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <dt class="text-slate-500">NIS</dt>
                    <dd class="font-bold text-slate-800">{{ $student->nis }}</dd>
                </div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <dt class="text-slate-500">Kelas</dt>
                    <dd class="font-bold text-slate-800">{{ $student->getAttribute('class') }}</dd>
                </div>
                <div class="flex justify-between gap-4 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
                    <dt class="text-slate-500">Username</dt>
                    <dd class="font-bold text-slate-800">{{ $student->username }}</dd>
                </div>
            </dl>

            <form class="mt-6" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-[#0b2f57] transition hover:bg-slate-50" type="submit">
                    <x-icon name="login" class="size-5" />
                    Ke Halaman Login
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Jadwal</p>
                    <h2 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Daftar Ujian</h2>
                </div>
                <x-icon name="exam" class="size-8 text-[#0b2f57]" />
            </div>

            <div class="mt-5 grid gap-4">
                @forelse ($exams as $exam)
                    @php($session = $exam->sessions->first())
                    @php($status = $exam->statusFor($student, $session))
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-sky-200 hover:shadow-sm">
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-[#0b2f57]">{{ $exam->subject }} / {{ $exam->getAttribute('class') }}</p>
                                <h3 class="mt-1 truncate text-xl font-bold text-slate-900">{{ $exam->title }}</h3>
                                <p class="mt-2 text-sm text-slate-500">{{ $exam->start_time->format('d M Y H:i') }} - {{ $exam->end_time->format('H:i') }} / {{ $exam->duration_minutes }} menit</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-600">{{ str_replace('_', ' ', $status) }}</span>
                                @if ($status === 'tersedia' || $status === 'berlangsung')
                                    <a class="inline-flex items-center gap-2 rounded-2xl bg-[#0b2f57] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#092846]" href="{{ route('exam.instruction', $exam) }}">
                                        <x-icon name="exam" class="size-4" />
                                        {{ $status === 'berlangsung' ? 'Lanjutkan' : 'Mulai Ujian' }}
                                    </a>
                                @else
                                    <button class="rounded-2xl bg-slate-200 px-4 py-3 text-sm font-bold text-slate-500" disabled>Belum tersedia</button>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-600">
                        Belum ada ujian untuk kelas kamu.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
