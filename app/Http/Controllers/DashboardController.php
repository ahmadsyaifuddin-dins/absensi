<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Logika untuk Dashboard Admin
            $today = Carbon::now('Asia/Makassar')->format('Y-m-d');

            // 1. Statistik Kartu
            $totalEmployees = Employee::where('status', 'aktif')->count();
            $presentToday = Attendance::where('date', $today)->whereIn('status', ['Hadir', 'Terlambat'])->count();
            $lateToday = Attendance::where('date', $today)->where('status', 'like', '%Terlambat%')->count();
            $onLeaveToday = Attendance::where('date', $today)->whereIn('status', ['Izin', 'Sakit'])->count();

            // 2. Aktivitas Terbaru
            $recentActivities = Attendance::with('employee')
                                ->where('date', $today)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
            
            return view('dashboard-admin', compact(
                'totalEmployees', 
                'presentToday', 
                'lateToday', 
                'onLeaveToday',
                'recentActivities'
            ));

        } elseif ($user->role === 'karyawan' && $user->employee) {
            // Logika untuk Dashboard Karyawan (tetap sama)
            $today = Carbon::now('Asia/Makassar')->format('Y-m-d');
            $todaysAttendance = Attendance::where('employee_id', $user->employee->id)
                                          ->where('date', $today)
                                          ->first();
            return view('dashboard-karyawan', compact('todaysAttendance'));
        }

        // Fallback jika karyawan tapi data employee belum ada
        return view('dashboard-karyawan');
    }
}