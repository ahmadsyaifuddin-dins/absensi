<?php

namespace Database\Seeders;

use App\Models\Employee; // Pastikan ini di-import
use App\Models\User;     // Pastikan ini di-import
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Pastikan ini di-import

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 1 User Admin
        // Kita tidak perlu membuat data employee untuk admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Membuat 1 User Karyawan untuk testing dengan data employee
        User::factory()->has(Employee::factory()->state([
            'nama_lengkap' => 'Karyawan Test',
            'nip' => '199001012020121001'
        ]))->create([
            'name' => 'Karyawan Test',
            'email' => 'karyawan@gmail.com',
            'role' => 'karyawan',
            'password' => Hash::make('karyawan123'),
        ]);


        // Membuat 10 Karyawan dummy menggunakan factory
        // has(Employee::factory()) akan otomatis membuat data employee
        // untuk setiap user yang dibuat dan mengisikan user_id-nya.
        User::factory(10)->has(Employee::factory())->create();
    }
}