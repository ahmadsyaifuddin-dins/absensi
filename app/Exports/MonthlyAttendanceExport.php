<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class MonthlyAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $employeeId;
    protected $month;
    protected $year;

    public function __construct(int $employeeId, int $month, int $year)
    {
        $this->employeeId = $employeeId;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Attendance::with('employee')
                         ->where('employee_id', $this->employeeId)
                         ->whereMonth('date', $this->month)
                         ->whereYear('date', $this->year)
                         ->orderBy('date', 'asc')
                         ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Keterangan',
        ];
    }

    public function map($attendance): array
    {
        return [
            Carbon::parse($attendance->date)->isoFormat('dddd, D MMMM Y'),
            $attendance->time_in,
            $attendance->time_out,
            $attendance->status,
            $attendance->notes,
        ];
    }
}