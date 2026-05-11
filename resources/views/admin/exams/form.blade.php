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
            @foreach ([['title','Nama Ujian'], ['subject','Mata Pelajaran'], ['class','Kelas']] as [$name, $label])
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
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button class="inline-flex items-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846]" type="submit">
                <x-icon name="file" class="size-5" />
                Simpan
            </button>
            <a class="rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50" href="{{ route('admin.exams.index') }}">Batal</a>
        </div>
    </form>

    @if ($exam->exists)
        <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div class="flex gap-3">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <x-icon name="upload" class="size-6" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Whitelist Peserta</p>
                        <h2 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Import Peserta Ujian</h2>
                        <p class="mt-2 max-w-3xl leading-7 text-slate-600">Peserta yang diimport menjadi daftar akses untuk ujian ini. Hanya username dan password peserta tersebut yang dapat melihat serta mengerjakan ujian.</p>
                        <p class="mt-2 text-sm font-bold text-emerald-700">Total peserta saat ini: {{ $exam->participants_count }}</p>
                    </div>
                </div>
                <a class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-[#0b2f57] hover:bg-slate-50" href="{{ route('admin.exams.participants.template', $exam) }}">
                    <x-icon name="file" class="size-5" />
                    Download Template CSV
                </a>
            </div>

            <form class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5" method="POST" action="{{ route('admin.exams.participants.import', $exam) }}" enctype="multipart/form-data">
                @csrf
                <label class="text-sm font-semibold text-slate-700" for="participants-file">File Peserta CSV</label>
                <div class="mt-2 grid gap-3 md:grid-cols-[1fr_auto]">
                    <input id="participants-file" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" name="file" type="file" accept=".csv,text/csv" required>
                    <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800" type="submit">
                        <x-icon name="upload" class="size-5" />
                        Import Peserta
                    </button>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Upload file <strong>.csv</strong>. Format: <code>name,nis,class,username,email,password</code>. Kolom <code>class</code> boleh kosong, sistem memakai kelas ujian ini.
                </p>
            </form>
        </section>
    @endif
@endsection
