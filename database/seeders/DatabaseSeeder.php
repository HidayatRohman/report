<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $defaultPassword = bcrypt('password');

        // Seeder untuk role akses - menggunakan updateOrCreate untuk menghindari duplicate
        User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'password' => $defaultPassword,
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manajer@example.com'],
            [
                'name' => 'Manajer',
                'password' => $defaultPassword,
                'role' => 'manajer',
            ]
        );

        User::updateOrCreate(
            ['email' => 'spv@example.com'],
            [
                'name' => 'SPV',
                'password' => $defaultPassword,
                'role' => 'spv',
            ]
        );

        User::updateOrCreate(
            ['email' => 'karyawan@example.com'],
            [
                'name' => 'Karyawan',
                'password' => $defaultPassword,
                'role' => 'karyawan',
            ]
        );

        // Seeder untuk app settings
        $this->call(AppSettingSeeder::class);
        
        // Seeder untuk brand
        $this->call(BrandSeeder::class);
        
        // Seeder untuk transaksi
        $this->call(TransaksiSeeder::class);
    }
}
