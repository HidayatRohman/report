<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AppSettingController extends Controller
{
    public function logoSettings()
    {
        $settings = [
            'logo' => AppSetting::get('logo', ''),
            'favicon' => AppSetting::get('favicon', ''),
            'app_name' => AppSetting::get('app_name', 'Report App'),
        ];

        return Inertia::render('AppSettings/LogoSettings', [
            'settings' => $settings
        ]);
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'app_name' => 'nullable|string|max:255'
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                $oldLogo = AppSetting::get('logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }

                $logoPath = $request->file('logo')->store('logos', 'public');
                AppSetting::set('logo', $logoPath, 'Main application logo');
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                // Delete old favicon
                $oldFavicon = AppSetting::get('favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }

                $faviconPath = $request->file('favicon')->store('logos', 'public');
                AppSetting::set('favicon', $faviconPath, 'Application favicon');
            }

            // Handle app name
            if ($request->filled('app_name')) {
                AppSetting::set('app_name', $request->app_name, 'Application name');
            }

            return redirect()->back()->with('success', 'Pengaturan logo berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function deleteLogo(Request $request)
    {
        $type = $request->input('type'); // 'logo' or 'favicon'

        if (!in_array($type, ['logo', 'favicon'])) {
            return redirect()->back()->with('error', 'Tipe file tidak valid.');
        }

        try {
            $filePath = AppSetting::get($type);
            
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            AppSetting::set($type, '', "Application $type");

            return redirect()->back()->with('success', ucfirst($type) . ' berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
