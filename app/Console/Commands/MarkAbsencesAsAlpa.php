<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class MarkAbsencesAsAlpa extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'attendance:mark-alpa';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Mark active employees as Alpa if they did not clock in today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timezone = 'Asia/Makassar';
        $today = Carbon::now($timezone)->format('Y-m-d');
        $now = Carbon::now($timezone);

        // Jangan jalankan command ini pada hari libur (Sabtu/Minggu)
        if ($now->isWeekend()) {
            $this->info('Skipping on a weekend.');
            return;
        }

        // 1. Dapatkan semua ID karyawan yang aktif
        $activeEmployeeIds = Employee::where('status', 'aktif')->pluck('id');

        // 2. Dapatkan semua ID karyawan yang sudah memiliki catatan absensi hari ini
        $attendedEmployeeIds = Attendance::where('date', $today)->pluck('employee_id');

        // 3. Dapatkan ID karyawan yang aktif tapi belum absen
        $absentEmployeeIds = $activeEmployeeIds->diff($attendedEmployeeIds);

        if ($absentEmployeeIds->isEmpty()) {
            $this->info('All active employees have attendance records for today.');
            return;
        }

        // 4. Buat catatan 'Alpa' untuk setiap karyawan yang tidak hadir
        foreach ($absentEmployeeIds as $employeeId) {
            Attendance::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'status' => 'Alpa',
                'time_in' => null,
                'time_out' => null,
                'notes' => 'Tidak melakukan absensi masuk.',
            ]);
        }

        $this->info($absentEmployeeIds->count() . ' employees have been marked as Alpa.');
    }
}