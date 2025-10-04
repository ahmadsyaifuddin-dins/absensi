<?php
namespace Database\Factories;

// Tambahkan use statement ini
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        // Inisiasi Faker dengan lokal Indonesia
        $faker = FakerFactory::create('id_ID');

        return [
            // Gunakan $faker yang sudah disetel
            'nama_lengkap' => $faker->name(),
            'nip' => $faker->unique()->numerify('##################'), // NIP biasanya 18 digit
            'posisi' => $faker->jobTitle(),
            'jabatan' => $faker->randomElement(['Staff', 'Supervisor', 'Manajer', 'Direktur']),
            'tanggal_perekrutan' => $faker->date(),
            'no_hp' => $faker->phoneNumber(),
            'alamat' => $faker->address(),
            'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
            'status' => 'aktif',
        ];
    }
}