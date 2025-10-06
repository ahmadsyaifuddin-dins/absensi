<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class DailyAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    // Gunakan constructor untuk menerima tanggal dari controller
    public function __construct(string $date)
    {
        $this->date = $date;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Query data yang akan diexport, sama seperti di controller laporan
        return Attendance::with('employee')
                         ->whereDate('date', $this->date)
                         ->get();
    }

    /**
     * Menentukan header untuk file Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'NIP',
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Keterangan',
        ];
    }

    /**
     * Memetakan data untuk setiap baris di Excel.
     *
     * @param mixed $attendance
     * @return array
     */
    public function map($attendance): array
    {
        $nip = $attendance->employee->nip ?? 'N/A';
        return [
            "'" . $nip,  // Prepend single quote untuk memaksa sebagai teks di Excel
            $attendance->employee->nama_lengkap ?? 'Karyawan Tidak Ditemukan',
            Carbon::parse($attendance->date)->format('d-m-Y'),
            $attendance->time_in,
            $attendance->time_out,
            $attendance->status,
            $attendance->notes,
        ];
    }
}