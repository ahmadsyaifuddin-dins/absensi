<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $lateTime = Carbon::createFromTimeString('08:01:00', $timezone);
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
            ->where('date', '>=', $today)
            ->whereNull('time_out') // Pastikan belum absen pulang
            ->first();


        // 2. Jika tidak ditemukan data absen masuk
        if (!$attendance) {
            return redirect()->route('dashboard')->with('error', 'Anda belum melakukan absen masuk hari ini.');
        }

        // 3. Jika sudah ada data absen pulang (sebagai double check)
        if ($attendance->time_out) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah melakukan absen pulang hari ini.');
        }

        // 4. Tentukan status Pulang Cepat
        $officialTimeOut = Carbon::createFromTimeString('14:00:00', $timezone);

        // Ambil status jam masuk sebelumnya (Hadir atau Terlambat)
        $statusMasuk = $attendance->status;

        // Default status pulang adalah status saat masuk
        $newStatus = $statusMasuk;

        if ($now->lt($officialTimeOut)) {
            // Gabungkan status masuk dengan status pulang cepat
            // Contoh: "Hadir, Pulang Cepat" atau "Terlambat, Pulang Cepat"
            $newStatus .= ', Pulang Cepat';
        }

        // 5. Update data absen pulang dengan status baru
        $attendance->update([
            'time_out' => $time,
            'status' => $newStatus, // Gunakan status yang sudah diperbarui
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil melakukan absen pulang. Selamat beristirahat!');
    }

    public function storePermission(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'status' => 'required|in:Izin,Sakit',
            'notes'  => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $timezone = 'Asia/Makassar';
        $today = Carbon::now($timezone)->format('Y-m-d');

        // 2. Cek apakah sudah ada absensi hari ini (masuk/pulang/izin/sakit)
        $existingAttendance = Attendance::where('employee_id', $user->employee->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah memiliki catatan absensi untuk hari ini.');
        }

        // 3. Simpan data izin/sakit
        Attendance::create([
            'employee_id' => $user->employee->id,
            'date' => $today,
            'status' => $request->status,
            'notes' => $request->notes,
            'time_in' => null, // Tidak ada jam masuk
            'time_out' => null, // Tidak ada jam pulang
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan ' . $request->status . ' berhasil disimpan.');
    }

    /**
     * Mark absent employees as 'Alpa' for today.
     * This action is triggered manually by an admin.
     */
    public function markAlpa(Request $request)
    {
        // 1. Pastikan hanya admin yang bisa mengakses
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $timezone = 'Asia/Makassar';
        $today = Carbon::now($timezone)->format('Y-m-d');
        $now = Carbon::now($timezone);

        // 2. Jangan jalankan jika hari libur (Sabtu/Minggu)
        if ($now->isWeekend()) {
            return redirect()->route('dashboard')->with('info', 'Tidak ada tindakan yang diambil pada hari libur.');
        }

        // 3. Dapatkan semua ID karyawan yang statusnya 'aktif'
        $activeEmployeeIds = Employee::where('status', 'aktif')->pluck('id');

        // 4. Dapatkan ID karyawan yang sudah punya catatan absensi hari ini
        $attendedEmployeeIds = Attendance::where('date', $today)->pluck('employee_id');

        // 5. Cari ID karyawan yang aktif tapi belum absen (dianggap Alpa)
        $absentEmployeeIds = $activeEmployeeIds->diff($attendedEmployeeIds);

        if ($absentEmployeeIds->isEmpty()) {
            return redirect()->route('dashboard')->with('info', 'Semua karyawan aktif sudah memiliki catatan absensi hari ini.');
        }

        // 6. Buat catatan 'Alpa' untuk setiap karyawan yang absen
        foreach ($absentEmployeeIds as $employeeId) {
            Attendance::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'status' => 'Alpa',
                'notes' => 'Tidak melakukan absensi masuk hingga akhir hari kerja.',
            ]);
        }

        return redirect()->route('dashboard')->with('success', $absentEmployeeIds->count() . ' karyawan telah ditandai Alpa.');
    }
}
