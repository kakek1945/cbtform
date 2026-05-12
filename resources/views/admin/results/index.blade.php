@extends('layouts.app')

@section('title', 'Hasil Ujian')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
            <x-icon name="report" class="size-6" />
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Google Sheets</p>
            <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Hasil Ujian</h1>
        </div>
    </div>

    <section class="mb-5 grid gap-4 xl:grid-cols-2">
        <form class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm" method="GET" action="{{ route('admin.results.index') }}">
            <label class="text-sm font-semibold text-slate-700" for="exam-filter">Pilih Ujian</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <select id="exam-filter" class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#948979] focus:ring-4 focus:ring-[#948979]/20" name="exam_id">
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}" @selected($selectedExam?->id === $exam->id)>
                            {{ $exam->title }} / {{ $exam->subject }} / {{ $exam->getAttribute('class') }}
                        </option>
                    @endforeach
                </select>
                <button class="rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white" type="submit">Tampilkan</button>
            </div>
            <p class="mt-3 text-sm text-slate-500">
                Pilih ujian untuk melihat hasil yang sudah disinkronkan.
            </p>
        </form>

        <form class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm" method="POST" action="{{ route('admin.results.sync') }}">
            @csrf
            <input name="exam_id" type="hidden" value="{{ $selectedExam?->id }}">

            <label class="text-sm font-semibold text-slate-700" for="spreadsheet-id">Sinkron Otomatis Google Sheets</label>
            <input id="spreadsheet-id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm" name="result_spreadsheet_id" value="{{ old('result_spreadsheet_id', $selectedExam?->result_spreadsheet_id) }}" placeholder="Spreadsheet ID atau URL Google Sheets" required @disabled(! $selectedExam)>
            <input class="mt-3 w-full rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-sm" name="result_sheet_name" value="{{ old('result_sheet_name', $selectedExam?->result_sheet_name ?? 'Form Responses 1') }}" placeholder="Nama sheet/tab, contoh: Form Responses 1" @disabled(! $selectedExam)>
            <button class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#DFD0B8] px-5 py-3 font-bold text-[#222831] hover:bg-[#cfc0a9]" type="submit" @disabled(! $selectedExam)>
                <x-icon name="settings" class="size-5" />
                Sinkronkan
            </button>
            <p class="mt-3 text-sm leading-6 text-slate-500">
                Sheet harus bisa dibaca lewat link. Set akses Google Sheets ke <strong>Anyone with the link can view</strong>.
            </p>
        </form>
    </section>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 p-5 md:flex-row md:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Daftar Nilai</p>
                <h2 class="text-xl font-bold tracking-tight text-[#0b2f57]">
                    {{ $selectedExam ? $selectedExam->title : 'Belum ada ujian' }}
                </h2>
            </div>
            @if ($selectedExam)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700">
                        {{ $results->total() }} hasil
                    </span>
                    <a class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm hover:border-[#948979] hover:text-[#222831]"
                        href="{{ route('admin.results.download', ['exam_id' => $selectedExam->id]) }}">
                        <x-icon name="download" class="size-4" />
                        Download CSV
                    </a>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Siswa</th>
                        <th class="px-6 py-4">Identifier</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4">Nilai</th>
                        <th class="px-6 py-4">Persen</th>
                        <th class="px-6 py-4">Submit</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($results as $result)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $result->user?->name ?? $result->student_name ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $result->nis ?: $result->user?->nis }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $result->identifier ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $result->class ?: $result->user?->getAttribute('class') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $result->score ?? '-' }}
                                @if ($result->max_score)
                                    / {{ $result->max_score }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $result->percentage ? $result->percentage.'%' : '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $result->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($result->user)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Cocok</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Belum cocok</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500" colspan="7">
                                Belum ada hasil yang disinkronkan untuk ujian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $results->links() }}</div>
    </div>
@endsection
