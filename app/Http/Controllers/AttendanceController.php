<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance; // <-- Import model Attendance
use Carbon\Carbon;          // <-- Import Carbon untuk manajemen waktu

class AttendanceController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Pastikan user adalah karyawan dan memiliki data employee
        if ($user->role !== 'karyawan' || !$user->employee) {
            // Redirect atau tampilkan error jika bukan karyawan
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Ambil semua data absensi milik karyawan tersebut,
        // urutkan dari yang terbaru, dan paginasi (15 data per halaman)
        $attendances = Attendance::where('employee_id', $user->employee->id)
            ->latest() // ini sama dengan orderBy('created_at', 'desc')
            ->paginate(15);

        // Kirim data ke view
        return view('attendance.index', compact('attendances'));
    }

    /**
     * Store a newly created resource in storage for clock in.
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        // Pastikan user memiliki relasi employee
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $timezone = 'Asia/Makassar'; // WITA
        $now = Carbon::now($timezone);
        $today = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // 1. Cek apakah sudah absen masuk hari ini
        $existingAttendance = Attendance::where('employee_id', $user->employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // 2. Cek apakah hari ini adalah hari kerja (Senin-Jumat)
        if ($now->isWeekend()) {
            return redirect()->route('dashboard')->with('error', 'Absensi hanya bisa dilakukan pada hari kerja.');
        }

        // 3. Tentukan status (Terlambat atau Hadir)
        $lateTime = Carbon::createFromTimeString('08:00:00', $timezone);
        $status = 'Hadir';
        if ($now->gt($lateTime)) {
            $status = 'Terlambat';
        }

        // 4. Simpan data absensi
        Attendance::create([
            'employee_id' => $user->employee->id,
            'date' => $today,
            'time_in' => $time,
            'status' => $status,
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil melakukan absen masuk.');
    }

    /**
     * Update the specified resource in storage for clock out.
     */
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $timezone = 'Asia/Makassar'; // WITA
        $now = Carbon::now($timezone);
        $today = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        // 1. Cari data absensi masuk hari ini
        $attendance = Attendance::where('employee_id', $user->employee->id)
            ->where('date', $today)
            ->first();

        // 2. Jika tidak ditemukan data absen masuk
        if (!$attendance) {
            return redirect()->route('dashboard')->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        // 3. Jika sudah ada data absen pulang
        if ($attendance->time_out) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // 4. Update data absen pulang
        $attendance->update([
            'time_out' => $time,
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil melakukan absen pulang. Selamat beristirahat!');
    }
}
