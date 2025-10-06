<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // <-- Tambahkan ini di atas
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ReportController;

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
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::post('/admin/attendance/mark-alpa', [AttendanceController::class, 'markAlpa'])
    ->name('admin.attendance.mark_alpa');
    
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

});


require __DIR__.'/auth.php';