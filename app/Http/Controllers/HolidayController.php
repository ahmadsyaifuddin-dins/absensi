<?php

namespace App\Http\Controllers;

use App\Models\Holiday; // <-- Import model
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data hari libur, urutkan berdasarkan tanggal
        $holidays = Holiday::orderBy('date', 'desc')->paginate(10);

        return view('holidays.index', compact('holidays'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'date' => ['required', 'date', 'unique:holidays,date'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        // Buat data baru
        Holiday::create([
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('holidays.index')
                         ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holiday $holiday)
    {
        // Hapus data
        $holiday->delete();

        return redirect()->route('holidays.index')
                         ->with('success', 'Hari libur berhasil dihapus.');
    }
}