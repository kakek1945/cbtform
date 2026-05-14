@extends('layouts.app')

@section('title', 'Monitoring Ujian')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
            <x-icon name="monitor" class="size-6" />
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Pengawasan</p>
            <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Monitoring Ujian</h1>
        </div>
    </div>

    <form class="mb-5 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4" method="GET">
        <select class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="exam_id">
            <option value="">Semua ujian</option>
            @foreach ($exams as $exam)
                <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->title }}</option>
            @endforeach
        </select>
        <select class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="class">
            <option value="">Semua kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class }}" @selected(request('class') === $class)>{{ $class }}</option>
            @endforeach
        </select>
        <select class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="status">
            <option value="">Semua status</option>
            @foreach (['belum_mulai', 'berlangsung', 'selesai', 'waktu_habis'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', $status) }}</option>
            @endforeach
        </select>
        <input class="rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="date" type="date" value="{{ request('date') }}">
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846] md:col-span-4" type="submit">
            <x-icon name="check" class="size-5" />
            Filter
        </button>
    </form>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Ujian</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Mulai</th>
                        <th class="px-6 py-4">Selesai</th>
                        <th class="px-6 py-4">Sisa</th>
                        <th class="px-6 py-4">Tab</th>
                        <th class="px-6 py-4">IP</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $session->user->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->user->nisn }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->user->getAttribute('class') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->exam->title }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-600">{{ str_replace('_', ' ', $session->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->started_at?->format('H:i:s') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->finished_at?->format('H:i:s') ?? '-' }}</td>
                            <td class="px-6 py-4 font-mono text-slate-700">{{ $session->isFinished() ? '-' : gmdate('H:i:s', $session->remainingSeconds()) }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->tab_switch_count }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->ip_address }}</td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.monitoring.reset', $session) }}" onsubmit="return confirm('Reset ujian {{ $session->exam->title }} untuk {{ $session->user->name }}? Siswa akan bisa mulai ulang dari awal.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white hover:bg-red-700" type="submit">
                                        <x-icon name="trash" class="size-4" />
                                        Reset
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500" colspan="11">Belum ada sesi ujian sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $sessions->links() }}</div>
    </div>
@endsection
