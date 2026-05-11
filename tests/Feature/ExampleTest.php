<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_student_can_start_available_exam(): void
    {
        $this->seed();

        $student = User::where('username', 'siswa001')->firstOrFail();
        $exam = $student->getAttribute('class')
            ? \App\Models\Exam::where('class', $student->getAttribute('class'))->firstOrFail()
            : null;

        $response = $this
            ->actingAs($student)
            ->post(route('exam.start', $exam));

        $response->assertRedirect();
        $this->assertDatabaseHas('exam_sessions', [
            'user_id' => $student->id,
            'exam_id' => $exam->id,
            'status' => 'berlangsung',
        ]);
    }

    public function test_admin_can_open_admin_dashboard(): void
    {
        $this->seed();

        $admin = User::where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_import_exam_participants_from_csv(): void
    {
        $this->seed();

        $admin = User::where('username', 'admin')->firstOrFail();
        $exam = \App\Models\Exam::firstOrFail();
        $csv = "name,nis,class,username,email,password\n"
            ."Peserta Baru,7777,{$exam->getAttribute('class')},peserta7777,peserta7777@example.com,password123\n";
        $file = UploadedFile::fake()->createWithContent('peserta.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.exams.participants.import', $exam), ['file' => $file])
            ->assertRedirect();

        $student = User::where('username', 'peserta7777')->firstOrFail();

        $this->assertDatabaseHas('exam_participants', [
            'exam_id' => $exam->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_admin_can_import_exam_participants_from_semicolon_csv(): void
    {
        $this->seed();

        $admin = User::where('username', 'admin')->firstOrFail();
        $exam = \App\Models\Exam::firstOrFail();
        $csv = "\xEF\xBB\xBFname;nis;class;username;email;password\n"
            ."Peserta Titik Koma;8888;{$exam->getAttribute('class')};peserta8888;peserta8888@example.com;password123\n";
        $file = UploadedFile::fake()->createWithContent('peserta-semicolon.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.exams.participants.import', $exam), ['file' => $file])
            ->assertRedirect();

        $student = User::where('username', 'peserta8888')->firstOrFail();

        $this->assertDatabaseHas('exam_participants', [
            'exam_id' => $exam->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_admin_can_reset_exam_session_from_monitoring(): void
    {
        $this->seed();

        $admin = User::where('username', 'admin')->firstOrFail();
        $student = User::where('username', 'siswa001')->firstOrFail();
        $exam = $student->participantExams()->firstOrFail();
        $session = $student->examSessions()->create([
            'exam_id' => $exam->id,
            'started_at' => now(),
            'finished_at' => now(),
            'status' => 'selesai',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.monitoring.reset', $session))
            ->assertRedirect();

        $this->assertDatabaseMissing('exam_sessions', [
            'id' => $session->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity_type' => 'exam_session_reset',
            'exam_id' => $exam->id,
        ]);
    }
}
