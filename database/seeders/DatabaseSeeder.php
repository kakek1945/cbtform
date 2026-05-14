<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin CBT',
                'nisn' => null,
                'class' => null,
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        $students = [
            ['name' => 'Alya Putri', 'nisn' => '1001', 'class' => 'XII IPA 1', 'username' => 'siswa001'],
            ['name' => 'Bima Prakoso', 'nisn' => '1002', 'class' => 'XII IPA 1', 'username' => 'siswa002'],
            ['name' => 'Citra Lestari', 'nisn' => '2001', 'class' => 'XII IPS 1', 'username' => 'siswa003'],
        ];

        $createdStudents = collect();

        foreach ($students as $student) {
            $createdStudents->push(User::query()->updateOrCreate(
                ['username' => $student['username']],
                [
                    ...$student,
                    'email' => $student['username'].'@example.com',
                    'password' => 'password',
                    'role' => 'siswa',
                ]
            ));
        }

        $dummyForm = 'https://docs.google.com/forms/d/e/1FAIpQLSfDummyGoogleForm/viewform?embedded=true';

        $literasi = Exam::query()->updateOrCreate(
            ['title' => 'Ujian Literasi Digital'],
            [
                'code' => 'LITERASI-XIIIPA1',
                'subject' => 'Informatika',
                'class' => 'XII IPA 1',
                'google_form_url' => $dummyForm,
                'prefill_name_field' => 'entry.111111',
                'prefill_nisn_field' => 'entry.222222',
                'prefill_class_field' => 'entry.333333',
                'prefill_exam_field' => 'entry.444444',
                'start_time' => now()->subMinutes(15),
                'end_time' => now()->addDay(),
                'duration_minutes' => 45,
                'is_active' => true,
                'allow_retake' => false,
                'instructions' => 'Pastikan membuka ujian dalam fullscreen. Jangan pindah tab selama mengerjakan.',
            ]
        );

        $tryout = Exam::query()->updateOrCreate(
            ['title' => 'Tryout Ekonomi'],
            [
                'code' => 'EKONOMI-XIIIPS1',
                'subject' => 'Ekonomi',
                'class' => 'XII IPS 1',
                'google_form_url' => $dummyForm,
                'prefill_name_field' => 'entry.111111',
                'prefill_nisn_field' => 'entry.222222',
                'prefill_class_field' => 'entry.333333',
                'prefill_exam_field' => 'entry.444444',
                'start_time' => now()->addHour(),
                'end_time' => now()->addHours(3),
                'duration_minutes' => 60,
                'is_active' => true,
                'allow_retake' => true,
                'instructions' => 'Baca soal dengan teliti dan kirim jawaban Google Form sebelum menekan selesai.',
            ]
        );

        $literasi->participants()->syncWithoutDetaching(
            $createdStudents->where('class', 'XII IPA 1')->pluck('id')->all()
        );

        $tryout->participants()->syncWithoutDetaching(
            $createdStudents->where('class', 'XII IPS 1')->pluck('id')->all()
        );
    }
}
