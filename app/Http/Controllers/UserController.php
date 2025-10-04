<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan ini
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data user, diurutkan dari yang terbaru.
        // Gunakan 'with('employee')' untuk mengambil relasi employee (Eager Loading).
        // Gunakan paginate() untuk membatasi data per halaman.
        $users = User::with('employee')->latest()->paginate(10);

        // Kirim data users ke view 'users.index'
        return view('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cukup kembalikan view yang berisi form
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,karyawan'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat User Baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // 3. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Kita panggil relasi 'employee' secara eksplisit
        // untuk memastikan datanya termuat.
        $user->load('employee');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Variabel $user sudah otomatis berisi data user yang akan diedit
        // berkat Route Model Binding dari Laravel.
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,karyawan'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Siapkan data untuk di-update
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // 3. Cek apakah password diisi
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // 4. Update data user
        $user->update($updateData);

        // 5. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Hapus pengguna dari database
        $user->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
