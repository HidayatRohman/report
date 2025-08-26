<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data transaksi sample berdasarkan data NYORE COFFEE
        $transaksiData = [
            ['tanggal' => '2025-01-25', 'brand' => 'NYORE COFFEE', 'nominal' => 7110000],
            ['tanggal' => '2025-02-25', 'brand' => 'NYORE COFFEE', 'nominal' => 5114000],
            ['tanggal' => '2025-03-25', 'brand' => 'NYORE COFFEE', 'nominal' => 2733000],
            ['tanggal' => '2025-04-25', 'brand' => 'NYORE COFFEE', 'nominal' => 2409000],
            ['tanggal' => '2025-05-25', 'brand' => 'NYORE COFFEE', 'nominal' => 3103900],
            ['tanggal' => '2025-06-25', 'brand' => 'NYORE COFFEE', 'nominal' => 8769490],
            ['tanggal' => '2025-07-25', 'brand' => 'NYORE COFFEE', 'nominal' => 8501700],
            ['tanggal' => '2025-08-25', 'brand' => 'NYORE COFFEE', 'nominal' => 12340100],
        ];

        foreach ($transaksiData as $data) {
            Transaksi::create($data);
        }

        // Generate data random tambahan
        Transaksi::factory(50)->create();
    }
}
