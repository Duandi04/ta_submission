<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
     * Indonesian first names
     */
    protected static array $firstNames = [
        'male' => [
            'Ahmad', 'Budi', 'Cahyo', 'Dimas', 'Eko', 'Fajar', 'Gilang', 'Hendra',
            'Irfan', 'Joko', 'Kevin', 'Lukman', 'Muhammad', 'Naufal', 'Omar',
            'Prasetyo', 'Rizky', 'Satria', 'Taufik', 'Umar', 'Wahyu', 'Yoga', 'Zaki',
            'Adi', 'Bayu', 'Candra', 'Dwi', 'Fikri', 'Galih', 'Haris', 'Imam',
        ],
        'female' => [
            'Ayu', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana',
            'Indah', 'Julia', 'Kartika', 'Lestari', 'Maya', 'Nadia', 'Olivia',
            'Putri', 'Ratna', 'Sari', 'Tania', 'Umi', 'Vina', 'Wulan', 'Yuni',
            'Amelia', 'Bella', 'Cantika', 'Diana', 'Farah', 'Galuh', 'Hasna', 'Intan',
        ],
    ];

    protected static array $lastNames = [
        'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Santoso', 'Hidayat', 'Nugroho',
        'Ramadhan', 'Permana', 'Putra', 'Wibowo', 'Setiawan', 'Haryanto', 'Siregar',
        'Hakim', 'Perdana', 'Maulana', 'Gunawan', 'Suryadi', 'Pranata', 'Kurniawan',
        'Susanto', 'Hartono', 'Budiman', 'Firmansyah', 'Sutrisno', 'Handoko', 'Yulianto',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = fake()->randomElement(self::$firstNames[$gender]);
        $lastName = fake()->randomElement(self::$lastNames);
        $name = "{$firstName} {$lastName}";
        
        return [
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@' . fake()->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'nim_nip' => fake()->unique()->numerify('##########'),
            'phone' => '08' . fake()->numerify('##########'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a student (mahasiswa).
     */
    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'nim_nip' => date('Y') . fake()->unique()->numerify('######'),
            'address' => 'Jl. ' . fake()->randomElement(['Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Diponegoro']) . ' No. ' . fake()->numberBetween(1, 200) . ', ' . fake()->randomElement(['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang']),
        ]);
    }

    /**
     * Create a lecturer (dosen).
     */
    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'nim_nip' => 'DSN' . fake()->unique()->numerify('######'),
        ]);
    }

    /**
     * Create a department head (kaprodi).
     */
    public function kaprodi(): static
    {
        return $this->state(fn (array $attributes) => [
            'nim_nip' => 'KPD' . fake()->unique()->numerify('######'),
        ]);
    }

    /**
     * Create an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'nim_nip' => 'ADM' . fake()->unique()->numerify('######'),
        ]);
    }
}
