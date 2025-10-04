<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 1 User Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Membuat 1 User Karyawan untuk testing
        User::factory()->create([
            'name' => 'Test Karyawan',
            'email' => 'karyawan@gmail.com',
            'role' => 'karyawan',
            'password' => Hash::make('karyawan123'),
        ]);

        // Membuat 10 Karyawan dummy menggunakan factory
        User::factory(10)->create()->each(function ($user) {
            // Untuk setiap user yang dibuat, buatkan juga data employee
            Employee::factory()->create(['user_id' => $user->id]);
        });
    }
}
