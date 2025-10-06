<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        // Pastikan pengguna belum memiliki data employee
        if ($user->employee) {
            return redirect()->route('users.show', $user)->with('error', 'Pengguna ini sudah memiliki data karyawan.');
        }
        return view('admin.employees.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        // Pastikan pengguna belum memiliki data employee
        if ($user->employee) {
            return redirect()->route('users.show', $user)->with('error', 'Pengguna ini sudah memiliki data karyawan.');
        }

        // Validasi data
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => 'required|string|max:16|unique:employees,nip',
            'posisi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_perekrutan' => 'required|date',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status' => 'required|in:aktif,tidak aktif,dihentikan',
        ]);

        // Tambahkan user_id dan buat data employee
        $validatedData['user_id'] = $user->id;
        Employee::create($validatedData);

        return redirect()->route('users.show', $user)->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Pastikan pengguna memiliki data employee untuk diedit
        if (!$user->employee) {
            return redirect()->route('users.show', $user)->with('error', 'Data karyawan tidak ditemukan untuk pengguna ini.');
        }
        return view('admin.employees.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Pastikan pengguna memiliki data employee untuk diupdate
        $employee = $user->employee;
        if (!$employee) {
            return redirect()->route('users.show', $user)->with('error', 'Data karyawan tidak ditemukan untuk pengguna ini.');
        }

        // Validasi data
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip' => ['required', 'string', 'max:16', Rule::unique('employees')->ignore($employee->id)],
            'posisi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_perekrutan' => 'required|date',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status' => 'required|in:aktif,tidak aktif,dihentikan',
        ]);

        // Update data employee
        $employee->update($validatedData);

        return redirect()->route('users.show', $user)->with('success', 'Data karyawan berhasil diperbarui.');
    }
}