<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['NYORE COFFEE', 'Nike', 'Adidas', 'Puma', 'Samsung', 'Apple'];
        
        return [
            'tanggal' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'brand' => $this->faker->randomElement($brands),
            'nominal' => $this->faker->numberBetween(100000, 15000000),
        ];
    }
}
