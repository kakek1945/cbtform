@extends('layouts.app')

@section('title', $student->exists ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
    <form class="mx-auto max-w-4xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8" method="POST" action="{{ $student->exists ? route('admin.students.update', $student) : route('admin.students.store') }}">
        @csrf
        @if ($student->exists)
            @method('PUT')
        @endif

        <div class="flex items-center gap-3">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-sky-50 text-[#0b2f57] ring-1 ring-sky-100">
                <x-icon name="student" class="size-6" />
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-slate-500">Data Siswa</p>
                <h1 class="text-2xl font-bold tracking-tight text-[#0b2f57]">{{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa' }}</h1>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ([['name','Nama'], ['nisn','NISN'], ['class','Kelas'], ['username','Username'], ['email','Email']] as [$name, $label])
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                    <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="{{ $name }}" value="{{ old($name, $student->getAttribute($name)) }}" @if($name !== 'email') required @endif>
                </label>
            @endforeach
            <label class="block md:col-span-2">
                <span class="text-sm font-semibold text-slate-700">Password / Token {{ $student->exists ? '(kosongkan jika tidak diubah)' : '' }}</span>
                <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-[#0b2f57] focus:ring-4 focus:ring-sky-100" name="password" type="password" @if(! $student->exists) required @endif>
            </label>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button class="inline-flex items-center gap-2 rounded-2xl bg-[#0b2f57] px-5 py-3 font-bold text-white hover:bg-[#092846]" type="submit">
                <x-icon name="file" class="size-5" />
                Simpan
            </button>
            <a class="rounded-2xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 hover:bg-slate-50" href="{{ route('admin.students.index') }}">Batal</a>
        </div>
    </form>

    @if ($student->exists)
        <div class="mx-auto mt-5 grid max-w-4xl gap-4 md:grid-cols-2">
            <form class="rounded-3xl border border-amber-200 bg-white p-5 shadow-sm" method="POST" action="{{ route('admin.students.reset-password', $student) }}">
                @csrf
                <h2 class="font-bold text-slate-900">Reset Password/Token</h2>
                <label class="mt-3 block">
                    <span class="text-sm font-semibold text-slate-700">Password baru</span>
                    <input class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 outline-none focus:border-amber-600 focus:ring-4 focus:ring-amber-100" name="password" type="password" required>
                </label>
                <button class="mt-4 rounded-2xl bg-amber-600 px-4 py-2 font-bold text-white hover:bg-amber-700" type="submit">Reset</button>
            </form>

            <form class="rounded-3xl border border-red-200 bg-white p-5 shadow-sm" method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus siswa ini?')">
                @csrf
                @method('DELETE')
                <h2 class="font-bold text-slate-900">Hapus Data Siswa</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Semua sesi ujian siswa ikut terhapus.</p>
                <button class="mt-4 rounded-2xl bg-red-600 px-4 py-2 font-bold text-white hover:bg-red-700" type="submit">Hapus</button>
            </form>
        </div>
    @endif
@endsection
