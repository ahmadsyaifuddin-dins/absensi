<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance; // <-- Import model
use Carbon\Carbon;          // <-- Import Carbon

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan tanggal filter: dari input request atau default hari ini
        $filterDate = $request->input('date', Carbon::now('Asia/Makassar')->format('Y-m-d'));

        // Query data absensi
        $attendances = Attendance::with(['employee.user']) // Eager loading relasi bertingkat
                                 ->where('date', $filterDate)
                                 ->latest()
                                 ->paginate(15);

        // Kirim data ke view
        return view('reports.index', compact('attendances', 'filterDate'));
    }
}