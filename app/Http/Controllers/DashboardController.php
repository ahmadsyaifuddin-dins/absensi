<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance; // <-- Tambahkan ini
use Carbon\Carbon;          // <-- Tambahkan ini

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('dashboard-admin');
        } elseif ($user->role === 'karyawan' && $user->employee) {
            // Ambil data absensi HARI INI untuk karyawan yang login
            $today = Carbon::now('Asia/Makassar')->format('Y-m-d');
            $todaysAttendance = Attendance::where('employee_id', $user->employee->id)
                ->where('date', $today)
                ->first();

            // Kirim data absensi ke view
            return view('dashboard-karyawan', compact('todaysAttendance'));
        }

        // Jika karyawan tapi data employee belum ada, atau role lain.
        return view('dashboard-karyawan');
    }
}
