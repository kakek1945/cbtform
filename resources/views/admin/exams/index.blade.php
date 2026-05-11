@extends('layouts.app')

@section('title', 'Data Ujian')

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                <x-icon name="exam" class="size-6" />
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Manajemen</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Data Ujian</h1>
            </div>
        </div>
        <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white transition hover:bg-[#092846]" href="{{ route('admin.exams.create') }}">
            <x-icon name="exam" class="size-5" />
            Tambah Ujian
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($exams as $exam)
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-sky-200 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-[#0b2f57]">{{ $exam->subject }} / {{ $exam->getAttribute('class') }}</p>
                        <h2 class="mt-1 truncate text-xl font-bold text-slate-900">{{ $exam->title }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            {{ $exam->start_time->format('d M Y H:i') }} - {{ $exam->end_time->format('d M Y H:i') }}<br>
                            {{ $exam->participants_count }} peserta
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $exam->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-slate-100 text-slate-500' }}">{{ $exam->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a class="inline-flex items-center gap-2 rounded-2xl bg-sky-50 px-4 py-2 font-bold text-[#0b2f57] hover:bg-sky-100" href="{{ route('admin.exams.edit', $exam) }}">
                        <x-icon name="settings" class="size-4" />
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Hapus ujian ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-2xl bg-red-600 px-4 py-2 font-bold text-white hover:bg-red-700" type="submit">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500 md:col-span-2">
                Belum ada ujian.
            </div>
        @endforelse
    </div>

    <div class="mt-5">{{ $exams->links() }}</div>
@endsection
