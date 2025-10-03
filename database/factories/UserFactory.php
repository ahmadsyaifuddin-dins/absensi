<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID'); // Pake lokal Indonesia

        $gender = $faker->randomElement(['Laki-laki', 'Perempuan']);
        $name   = $gender == 'Laki-laki' ? $faker->firstNameMale() : $faker->firstNameFemale();
        $lastName = $faker->lastName();

        return [
            'name' => $name . ' ' . $lastName,
            'email' => strtolower(
                str_replace(['.', ' ', '-'], '', $name) .
                    '.' .
                    str_replace(['.', ' ', '-'], '', $lastName) .
                    $faker->unique()->numerify('##')
            ) . '@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'karyawan',
            'phone' => $faker->unique()->numerify('08##########'),
            'gender' => $gender,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
