<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminAppBrandingController extends Controller
{
    /**
     * Display the application branding & identity configuration page.
     */
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $branding = AppSetting::getBranding();

        return view('admin.settings.branding', [
            'school' => $school,
            'branding' => $branding,
        ]);
    }

    /**
     * Update application branding (App Name, Tagline, and Logo).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:60'],
            'app_tagline' => ['nullable', 'string', 'max:100'],
            'app_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ], [
            'app_name.required' => 'Nama aplikasi wajib diisi.',
            'app_name.max' => 'Nama aplikasi maksimal 60 karakter.',
            'app_tagline.max' => 'Tagline maksimal 100 karakter.',
            'app_logo.image' => 'Berkas logo harus berupa gambar valid.',
            'app_logo.mimes' => 'Format logo yang didukung: PNG, JPG, JPEG, SVG, atau WebP.',
            'app_logo.max' => 'Ukuran berkas logo maksimal 2 MB.',
        ]);

        // Simpan nama aplikasi & tagline
        AppSetting::set('app_name', trim($validated['app_name']), 'branding');
        AppSetting::set('app_tagline', trim($validated['app_tagline'] ?? ''), 'branding');

        // Proses unggah logo jika ada berkas baru
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');

            // Hapus berkas logo lama jika ada
            $oldLogo = AppSetting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Simpan berkas baru ke direktori branding
            $path = $file->store('branding', 'public');
            AppSetting::set('app_logo', $path, 'branding');
        }

        AppSetting::clearCache();

        return redirect()->route('admin.settings.branding')
            ->with('success', 'Identitas aplikasi (Logo, Nama, dan Tagline) berhasil diperbarui!');
    }

    /**
     * Remove the custom logo and revert to the default graduation icon.
     */
    public function removeLogo(): RedirectResponse
    {
        $oldLogo = AppSetting::get('app_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        AppSetting::set('app_logo', null, 'branding');
        AppSetting::clearCache();

        return redirect()->route('admin.settings.branding')
            ->with('success', 'Logo kustom berhasil dihapus. Aplikasi kini menggunakan ikon default.');
    }

    /**
     * Revert all branding settings back to system defaults.
     */
    public function resetDefaults(): RedirectResponse
    {
        $oldLogo = AppSetting::get('app_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        AppSetting::set('app_name', 'Sekolahku', 'branding');
        AppSetting::set('app_tagline', 'v2.0', 'branding');
        AppSetting::set('app_logo', null, 'branding');
        AppSetting::clearCache();

        return redirect()->route('admin.settings.branding')
            ->with('success', 'Pengaturan branding berhasil dikembalikan ke standar awal sistem.');
    }
}
