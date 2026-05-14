@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
            <x-icon name="report" class="size-6" />
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Laporan</p>
            <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Log Aktivitas</h1>
        </div>
    </div>

    <form class="mb-5 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-3" method="GET">
        <select class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="activity_type">
            <option value="">Semua aktivitas</option>
            @foreach ($activityTypes as $type)
                <option value="{{ $type }}" @selected(request('activity_type') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <select class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="exam_id">
            <option value="">Semua ujian</option>
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->title }}</option>
            @endforeach
        </select>
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="date" type="date" value="{{ request('date') }}">
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846] md:col-span-3" type="submit">
            <x-icon name="check" class="size-5" />
            Filter
        </button>
    </form>

    <div class="grid gap-3">
        @forelse ($logs as $log)
            <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <div class="flex gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-[#0b2f57]">
                            <x-icon name="report" class="size-5" />
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $log->activity_type }}</p>
                            <p class="mt-1 leading-6 text-slate-600">{{ $log->description }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $log->user?->name ?? 'System' }} / {{ $log->exam?->title ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-slate-500 md:text-right">
                        <p>{{ $log->created_at->format('d M Y H:i:s') }}</p>
                        <p>{{ $log->ip_address }}</p>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                Belum ada log aktivitas sesuai filter.
            </div>
        @endforelse
    </div>

    <div class="mt-5">{{ $logs->links() }}</div>
@endsection
