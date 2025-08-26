<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'nama_brand' => 'Nyore Nyante',
                'pemilik' => 'CV Kelola Pusat',
                'deskripsi' => 'Brand premium coffee dari CV Kelola Pusat dengan citarasa khas Indonesia'
            ],
            [
                'nama_brand' => 'Kemangi',
                'pemilik' => 'CV Kelola Pusat',
                'deskripsi' => 'Brand inovatif dengan konsep natural dan organik dari CV Kelola Pusat'
            ],
            [
                'nama_brand' => 'Art Coffee',
                'pemilik' => 'CV Kelola Pusat',
                'deskripsi' => 'Brand artisan coffee dengan sentuhan seni dalam setiap racikan dari CV Kelola Pusat'
            ]
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
