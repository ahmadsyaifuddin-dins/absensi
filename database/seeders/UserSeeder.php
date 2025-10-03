<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Buat user Admin (Data tetap)
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@gmail.com', 
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'gender' => 'Laki-laki',
        ]);

        // 2. Buat user Karyawan (Data tetap)
        User::create([
            'name' => 'Budi Karyawan',
            'email' => 'budi.karyawan@gmail.com', 
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'phone' => '089876543210',
            'gender' => 'Laki-laki',
        ]);

        // 3. 6 user nama custom
        $customNames = ['Haldi', 'Ryandy', 'Maulidi', 'Rio', 'Aldy', 'Ahmad S'];

        foreach ($customNames as $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'phone' => $faker->unique()->numerify('08##########'),
                'gender' => 'Laki-laki',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        // 4. 7 user random pakai Factory
        User::factory()->count(7)->create();
    }
}
