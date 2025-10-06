<?php

use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Rute bawaan Breeze untuk profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RUTE UNTUK ABSENSI
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index'); // <-- TAMBAHKAN INI
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    Route::post('/attendance/permission', [AttendanceController::class, 'storePermission'])->name('attendance.store_permission');
});


// === RUTE KHUSUS ADMIN UNTUK MANAJEMEN PENGGUNA ===
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Rute resource akan otomatis membuat semua URL CRUD untuk UserController
    // Contoh: /users, /users/create, /users/{user}/edit, dll.
    Route::resource('users', UserController::class);

    // RUTE BARU UNTUK LAPORAN HARIAN
    Route::get('/admin/reports/daily', [ReportController::class, 'dailyReport'])
            ->name('admin.reports.daily');

    Route::post('/admin/attendance/mark-alpa', [AttendanceController::class, 'markAlpa'])
    ->name('admin.attendance.mark_alpa');
    
    Route::resource('/admin/holidays', HolidayController::class)
    ->except(['show']); // Method 'show' tidak kita gunakan
});


require __DIR__.'/auth.php';