<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $merek = $this->faker->randomElement(['toyota', 'mitsubishi', 'suzuki', 'daihatsu', 'honda']);

        return [
            'nopol' => strtoupper($this->faker->bothify('AB #### ??')), // Contoh: AB 123 XY
            'merek' => $merek,
            'nama_mobil' => ucfirst($this->faker->words(2, true)), // contoh: Avanza G
            'warna' => ucfirst($this->faker->words(1, true)), // contoh: Avanza G
            'garasi' => $this->faker->city,
            'year' => $this->faker->year,
            'status' => $this->faker->randomElement(['ready', 'disewa', 'perawatan', 'nonaktif']),
            'transmisi' => $this->faker->randomElement(['matic', 'manual']),
            'harga_pokok' => $this->faker->numberBetween(250000, 700000),
            'harga_harian' => $this->faker->numberBetween(250000, 700000),
            'harga_bulanan' => $this->faker->optional()->numberBetween(5000000, 15000000),
            'photo' => null, // opsional, bisa diganti dengan path dummy
        ];
    }
}
