<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\QrAttendanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('members', MemberController::class);
    Route::resource('meetings', MeetingController::class);

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{meeting}/mark', [AttendanceController::class, 'mark'])->name('attendances.mark');
    Route::get('/attendances/{meeting}/verify', [AttendanceController::class, 'verifyForm'])->name('attendances.verify.form');

    Route::post('/attendances', [AttendanceController::class, 'store'])
        ->middleware('throttle:form-submit')
        ->name('attendances.store');

    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::post('/attendances/verify', [AttendanceController::class, 'verify'])
        ->middleware('throttle:verify-attendance')
        ->name('attendances.verify');

    // PDF exports
    Route::get('/export/attendance-pdf', [ExportController::class, 'attendancePdf'])->name('export.attendance.pdf');
    Route::get('/export/members-pdf', [ExportController::class, 'membersPdf'])->name('export.members.pdf');

    // QR code display pages (admin)
    Route::get('/qr/matc', [QrAttendanceController::class, 'show'])->defaults('category', 'matc')->name('admin.qr.matc');
    Route::get('/qr/amk', [QrAttendanceController::class, 'show'])->defaults('category', 'amk')->name('admin.qr.amk');
    Route::get('/qr/wanita', [QrAttendanceController::class, 'show'])->defaults('category', 'wanita')->name('admin.qr.wanita');
});

// Public category attendance form pages
Route::get('/matc', [QrAttendanceController::class, 'publicForm'])->defaults('category', 'matc')->name('qr.matc');
Route::get('/amk', [QrAttendanceController::class, 'publicForm'])->defaults('category', 'amk')->name('qr.amk');
Route::get('/wanita', [QrAttendanceController::class, 'publicForm'])->defaults('category', 'wanita')->name('qr.wanita');

Route::post('/matc', [QrAttendanceController::class, 'publicVerify'])->defaults('category', 'matc')->name('qr.matc.verify')->middleware(['throttle:verify-attendance', 'honeypot']);
Route::post('/amk', [QrAttendanceController::class, 'publicVerify'])->defaults('category', 'amk')->name('qr.amk.verify')->middleware(['throttle:verify-attendance', 'honeypot']);
Route::post('/wanita', [QrAttendanceController::class, 'publicVerify'])->defaults('category', 'wanita')->name('qr.wanita.verify')->middleware(['throttle:verify-attendance', 'honeypot']);

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
});

// Public QR scan destination (signed URL, no auth)
Route::get('/hadir/{category}/{meeting}', [QrAttendanceController::class, 'hadir'])
    ->name('hadir')
    ->middleware('signed');

Route::post('/hadir/{category}/{meeting}', [QrAttendanceController::class, 'verify'])
    ->name('hadir.verify')
    ->middleware(['signed', 'throttle:verify-attendance', 'honeypot']);

require __DIR__.'/auth.php';
