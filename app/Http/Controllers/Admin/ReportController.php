<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman laporan absensi harian.
     */
    public function dailyReport(Request $request)
    {
        // Tentukan tanggal. Jika ada input dari user, gunakan itu. Jika tidak, gunakan hari ini.
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now('Asia/Makassar');

        // Ambil data absensi pada tanggal yang dipilih
        // Kita gunakan 'with' untuk mengambil data relasi 'employee' agar lebih efisien (Eager Loading)
        $attendances = Attendance::with('employee')
                                ->whereDate('date', $date)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // Kirim data ke view
        return view('admin.reports.daily', [
            'attendances' => $attendances,
            'selectedDate' => $date->format('Y-m-d'), // Kirim tanggal yang dipilih ke view
        ]);
    }
}