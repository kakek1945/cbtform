@extends('layouts.app')

@section('title', $exam->exists ? 'Edit Ujian' : 'Tambah Ujian')

@section('content')
    <form class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam) : route('admin.exams.store') }}">
        @csrf
        @if ($exam->exists)
            @method('PUT')
        @endif

        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                <x-icon name="exam" class="size-6" />
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Data Ujian</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">{{ $exam->exists ? 'Edit Ujian' : 'Tambah Ujian' }}</h1>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ([['title','Nama Ujian'], ['code','Kode Ujian'], ['subject','Mata Pelajaran'], ['class','Kelas']] as [$name, $label])
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                    <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="{{ $name }}" value="{{ old($name, $exam->getAttribute($name)) }}" required>
                </label>
            @endforeach
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Durasi Menit</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="duration_minutes" type="number" min="1" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required>
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-semibold text-slate-700">Google Form URL</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="google_form_url" type="url" value="{{ old('google_form_url', $exam->google_form_url) }}" required>
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Spreadsheet ID Hasil</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="result_spreadsheet_id" value="{{ old('result_spreadsheet_id', $exam->result_spreadsheet_id) }}" placeholder="ID dari URL Google Sheets">
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Field Username Google Form</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="prefill_username_field" value="{{ old('prefill_username_field', $exam->prefill_username_field) }}" placeholder="Contoh: entry.555555">
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Nama Sheet Hasil</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="result_sheet_name" value="{{ old('result_sheet_name', $exam->result_sheet_name) }}" placeholder="Contoh: Form Responses 1">
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Mulai</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="start_time" type="datetime-local" value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" required>
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Selesai</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="end_time" type="datetime-local" value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" required>
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-semibold text-slate-700">Instruksi</span>
                <textarea class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="instructions" rows="4">{{ old('instructions', $exam->instructions) }}</textarea>
            </label>
        </div>

        <div class="mt-5 flex flex-wrap gap-4">
            <label class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-100">
                <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $exam->exists ? $exam->is_active : true))>
                <span class="font-semibold">Aktif</span>
            </label>
            <label class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-100">
                <input name="allow_retake" type="checkbox" value="1" @checked(old('allow_retake', $exam->allow_retake))>
                <span class="font-semibold">Boleh mengulang</span>
            </label>
            <label class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-slate-100">
                <input name="show_score" type="checkbox" value="1" @checked(old('show_score', $exam->show_score))>
                <span class="font-semibold">Tampilkan nilai ke siswa</span>
            </label>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button class="inline-flex items-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846]" type="submit">
                <x-icon name="file" class="size-5" />
                Simpan
            </button>
            <a class="rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50" href="{{ route('admin.exams.index') }}">Batal</a>
        </div>
    </form>

@endsection
