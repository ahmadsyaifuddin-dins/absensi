<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Employee;
use App\Exports\DailyAttendanceExport; // <-- TAMBAHKAN INI
use App\Exports\MonthlyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;   // <-- TAMBAHKAN INI

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

    public function exportExcel(Request $request)
    {
        // Ambil tanggal dari request, jika tidak ada, gunakan hari ini
        $date = $request->input('date') ? Carbon::parse($request->input('date'))->format('Y-m-d') : Carbon::now('Asia/Makassar')->format('Y-m-d');

        // Buat nama file yang dinamis
        $fileName = 'laporan-absensi-harian-' . $date . '.xlsx';

        // Panggil class Export dan download filenya
        return Excel::download(new DailyAttendanceExport($date), $fileName);
    }

    /**
     * Menampilkan halaman laporan absensi bulanan.
     */
    public function monthlyReport(Request $request)
    {
        // Ambil daftar karyawan aktif untuk dropdown filter
        $employees = Employee::where('status', 'aktif')->orderBy('nama_lengkap')->get();

        // Ambil input dari filter, jika tidak ada, gunakan bulan dan tahun saat ini
        $selectedEmployeeId = $request->input('employee_id');
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        $attendances = collect(); // Buat collection kosong sebagai default
        $recap = [];

        // Jika seorang karyawan sudah dipilih, baru kita proses datanya
        if ($selectedEmployeeId) {
            // Query untuk mengambil semua data absensi karyawan di bulan dan tahun yang dipilih
            $attendances = Attendance::where('employee_id', $selectedEmployeeId)
                ->whereMonth('date', $selectedMonth)
                ->whereYear('date', $selectedYear)
                ->orderBy('date', 'asc')
                ->get();

            // Hitung rekapitulasi
            $recap = [
                'hadir' => $attendances->where('status', 'Hadir')->count(),
                'terlambat' => $attendances->where('status', 'like', '%Terlambat%')->count(),
                'izin' => $attendances->where('status', 'Izin')->count(),
                'sakit' => $attendances->where('status', 'Sakit')->count(),
                'alpa' => $attendances->where('status', 'Alpa')->count(),
            ];
        }

        return view('admin.reports.monthly', compact(
            'employees',
            'selectedEmployeeId',
            'selectedMonth',
            'selectedYear',
            'attendances',
            'recap'
        ));
    }

    public function exportMonthlyExcel(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Validasi: pastikan employee_id ada
        if (!$employeeId) {
            return redirect()->route('admin.reports.monthly')->with('error', 'Silakan pilih karyawan terlebih dahulu.');
        }

        $employee = Employee::findOrFail($employeeId);
        $monthName = Carbon::create()->month($month)->isoFormat('MMMM');
        $fileName = 'laporan-bulanan-' . str_replace(' ', '-', $employee->nama_lengkap) . '-' . $monthName . '-' . $year . '.xlsx';

        return Excel::download(new MonthlyAttendanceExport($employeeId, $month, $year), $fileName);
    }
}
