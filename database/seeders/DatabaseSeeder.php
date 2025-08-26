<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Seeder untuk role akses
        User::factory()->create([
            'name' => 'Manajer',
            'email' => 'manajer@example.com',
            'password' => $defaultPassword,
            'role' => 'manajer',
        ]);

        User::factory()->create([
            'name' => 'SPV',
            'email' => 'spv@example.com',
            'password' => $defaultPassword,
            'role' => 'spv',
        ]);

        User::factory()->create([
            'name' => 'Karyawan',
            'email' => 'karyawan@example.com',
            'password' => $defaultPassword,
            'role' => 'karyawan',
        ]);

        // Seeder untuk transaksi
        $this->call(TransaksiSeeder::class);
    }
}
