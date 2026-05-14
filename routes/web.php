<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminExamController;
use App\Http\Controllers\AdminExamResultController;
use App\Http\Controllers\AdminMonitoringController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:siswa')->group(function () {
        Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
        Route::get('/exam/{exam}/instruction', [ExamController::class, 'instruction'])->name('exam.instruction');
        Route::post('/exam/{exam}/start', [ExamSessionController::class, 'start'])->name('exam.start');
        Route::get('/exam/{exam}/session/{session}', [ExamSessionController::class, 'show'])->name('exam.session.show');
        Route::post('/exam/session/{session}/finish', [ExamSessionController::class, 'finish'])->name('exam.session.finish');
        Route::get('/exam/session/{session}/submission-status', [ExamSessionController::class, 'submissionStatus'])->name('exam.session.submission-status');
        Route::post('/exam/session/{session}/tab-switch', [ExamSessionController::class, 'tabSwitch'])->name('exam.session.tab-switch');
        Route::post('/exam/session/{session}/fullscreen-exit', [ExamSessionController::class, 'fullscreenExit'])->name('exam.session.fullscreen-exit');
        Route::get('/exam/session/{session}/finished', [ExamSessionController::class, 'finished'])->name('exam.session.finished');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/students/template', [AdminStudentController::class, 'downloadTemplate'])->name('students.template');
        Route::resource('/students', AdminStudentController::class);
        Route::post('/students/{student}/reset-password', [AdminStudentController::class, 'resetPassword'])->name('students.reset-password');
        Route::post('/students/import-csv', [AdminStudentController::class, 'importCsv'])->name('students.import-csv');
        Route::resource('/exams', AdminExamController::class);
        Route::get('/monitoring', AdminMonitoringController::class)->name('monitoring.index');
        Route::delete('/monitoring/{session}/reset', [AdminMonitoringController::class, 'reset'])->name('monitoring.reset');
        Route::get('/results', [AdminExamResultController::class, 'index'])->name('results.index');
        Route::post('/results/sync', [AdminExamResultController::class, 'sync'])->name('results.sync');
        Route::get('/results/download', [AdminExamResultController::class, 'download'])->name('results.download');
        Route::delete('/results/{result}', [AdminExamResultController::class, 'destroy'])->name('results.destroy');
        Route::get('/activity-logs', ActivityLogController::class)->name('activity-logs.index');
    });
});
