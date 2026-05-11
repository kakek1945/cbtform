@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    @php
        $cards = [
            ['label' => 'Siswa', 'value' => $summary['students'], 'icon' => 'student', 'tone' => 'text-[#0b2f57] bg-sky-50'],
            ['label' => 'Ujian', 'value' => $summary['exams'], 'icon' => 'exam', 'tone' => 'text-[#0b2f57] bg-sky-50'],
            ['label' => 'Sedang Ujian', 'value' => $summary['active'], 'icon' => 'monitor', 'tone' => 'text-emerald-700 bg-emerald-50'],
            ['label' => 'Selesai', 'value' => $summary['finished'], 'icon' => 'report', 'tone' => 'text-slate-700 bg-slate-100'],
            ['label' => 'Waktu Habis', 'value' => $summary['expired'], 'icon' => 'calendar', 'tone' => 'text-amber-700 bg-amber-50'],
        ];
    @endphp

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        @foreach ($cards as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $card['value'] }}</p>
                    </div>
                    <div class="flex size-11 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                        <x-icon :name="$card['icon']" class="size-5" />
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="mt-6">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 p-6">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Terbaru</p>
                    <h2 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Sesi Ujian</h2>
                </div>
                <x-icon name="report" class="size-8 text-[#0b2f57]" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Siswa</th>
                            <th class="px-6 py-4">Ujian</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Pindah Tab</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSessions as $session)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $session->user->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $session->exam->title }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-600">{{ str_replace('_', ' ', $session->status) }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $session->tab_switch_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-8 text-center text-slate-500" colspan="4">Belum ada sesi ujian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
