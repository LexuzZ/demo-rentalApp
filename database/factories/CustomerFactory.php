<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'ktp' => $this->faker->unique()->numerify('################'), // gunakan nik() kalau pakai faker ID, atau fake number
            'no_telp' => $this->faker->unique()->phoneNumber(),
            'alamat' => $this->faker->address(),
        ];
    }
}
