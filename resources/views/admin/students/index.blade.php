@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-4 md:flex-row md:items-center">
        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                <x-icon name="student" class="size-6" />
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Master Data</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">Data Siswa</h1>
            </div>
        </div>
        <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white transition hover:bg-[#092846]" href="{{ route('admin.students.create') }}">
            <x-icon name="student" class="size-5" />
            Tambah Siswa
        </a>
    </div>

    <div class="mb-5 grid gap-4 lg:grid-cols-[1fr_.9fr]">
        <form class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm" method="GET">
            <label class="text-sm font-semibold text-slate-700" for="search">Cari Siswa</label>
            <div class="mt-2 flex gap-3">
                <input id="search" class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="search" value="{{ request('search') }}" placeholder="Nama, NISN, username, kelas">
                <button class="rounded-2xl border border-slate-200 px-4 py-3 font-bold text-slate-700 hover:bg-slate-50" type="submit">Cari</button>
            </div>
        </form>

        <form class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm" method="POST" action="{{ route('admin.students.import-csv') }}" enctype="multipart/form-data" onsubmit="return confirm('Import CSV akan menghapus permanen semua siswa lama dan menggantinya dengan data terbaru. Lanjutkan?')">
            @csrf
            <div class="flex flex-wrap items-center justify-between gap-2">
                <label class="text-sm font-semibold text-slate-700" for="student-file">Import CSV Siswa</label>
                <a class="text-sm font-bold text-[#0b2f57] hover:underline" href="{{ route('admin.students.template') }}">Download Template</a>
            </div>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <input id="student-file" class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm" name="file" type="file" accept=".csv,text/csv" required>
                <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800" type="submit">
                    <x-icon name="upload" class="size-5" />
                    Import
                </button>
            </div>
            <p class="mt-2 text-xs text-slate-500">Import akan menghapus permanen semua siswa lama. Buat ujian terlebih dahulu, lalu isi kolom <code>kode_ujian</code>. Untuk lebih dari satu ujian, pisahkan kode dengan titik koma, contoh <code>MTK-XII;BINDO-XII</code>.</p>
        </form>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Daftar Ujian yang Diikuti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $student->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $student->nisn }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $student->username }}</td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $student->participantExams->pluck('code')->filter()->implode(', ') ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500" colspan="4">Belum ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $students->links() }}</div>
    </div>
@endsection
