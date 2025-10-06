<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Contracts\View\View; 
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

// Implementasikan FromView, bukan FromCollection, dll.
class MyAttendanceHistoryExport implements FromView
{
    /**
     * Method ini akan merender view Blade dan datanya.
     */
    public function view(): View
    {
        // Ambil data karyawan yang sedang login
        $employee = Auth::user()->employee;

        // Ambil riwayat absensinya
        $attendances = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->get();

        // Kembalikan view beserta datanya
        return view('attendance.history_pdf', [
            'attendances' => $attendances,
            'employee' => $employee
        ]);
    }
}
