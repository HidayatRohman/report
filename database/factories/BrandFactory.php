<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brandNames = [
            'Nyore Nyante', 'Kemangi', 'Art Coffee',
            'Arabica Nusantara', 'Kopi Tradisi', 'Java Premium'
        ];
        
        return [
            'nama_brand' => $this->faker->unique()->randomElement($brandNames),
            'pemilik' => $this->faker->randomElement(['CV Kelola Pusat', 'PT Kopi Indonesia', 'CV Mitra Sejahtera']),
            'deskripsi' => $this->faker->sentence(10),
        ];
    }
}
