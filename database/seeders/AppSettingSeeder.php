<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'app_name',
                'setting_value' => 'Kelola Pusat App',
                'description' => 'Application name displayed throughout the system'
            ],
            [
                'setting_key' => 'logo',
                'setting_value' => null,
                'description' => 'Application logo file path'
            ],
            [
                'setting_key' => 'favicon',
                'setting_value' => null,
                'description' => 'Application favicon file path'
            ]
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                [
                    'setting_value' => $setting['setting_value'],
                    'description' => $setting['description']
                ]
            );
        }
    }
}