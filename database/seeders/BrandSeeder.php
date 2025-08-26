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
                'deskripsi' => 'Brand premium coffee dari CV Kelola Pusat dengan citarasa khas Indonesia',
                'logo_path' => 'logos/nyore-nyante.svg'
            ],
            [
                'nama_brand' => 'Kemangi',
                'pemilik' => 'CV Kelola Pusat',
                'deskripsi' => 'Brand inovatif dengan konsep natural dan organik dari CV Kelola Pusat',
                'logo_path' => 'logos/kemangi.svg'
            ],
            [
                'nama_brand' => 'Art Coffee',
                'pemilik' => 'CV Kelola Pusat',
                'deskripsi' => 'Brand artisan coffee dengan sentuhan seni dalam setiap racikan dari CV Kelola Pusat',
                'logo_path' => 'logos/art-coffee.svg'
            ]
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
