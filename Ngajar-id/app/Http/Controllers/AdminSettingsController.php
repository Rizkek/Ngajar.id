<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class AdminSettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:100',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
        ]);

        $this->saveSettings('general', $validated);

        return back()->with('success', 'General settings updated!');
    }

    /**
     * Update social media links
     */
    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        $this->saveSettings('social', $validated);

        return back()->with('success', 'Social media links updated!');
    }

    /**
     * Update payment gateway settings
     */
    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_is_production' => 'boolean',
            'xendit_secret_key' => 'nullable|string',
            'xendit_public_key' => 'nullable|string',
        ]);

        // Update .env file (be careful with this in production!)
        $this->updateEnvFile([
            'MIDTRANS_SERVER_KEY' => $validated['midtrans_server_key'] ?? '',
            'MIDTRANS_CLIENT_KEY' => $validated['midtrans_client_key'] ?? '',
            'MIDTRANS_IS_PRODUCTION' => $validated['midtrans_is_production'] ? 'true' : 'false',
            'XENDIT_SECRET_KEY' => $validated['xendit_secret_key'] ?? '',
            'XENDIT_PUBLIC_KEY' => $validated['xendit_public_key'] ?? '',
        ]);

        return back()->with('success', 'Payment settings updated! Reload the page to apply changes.');
    }

    /**
     * Update platform rules (Privacy Policy, Terms of Service)
     */
    public function updateRules(Request $request)
    {
        $validated = $request->validate([
            'privacy_policy' => 'nullable|string',
            'terms_of_service' => 'nullable|string',
        ]);

        $this->saveSettings('rules', $validated);

        return back()->with('success', 'Platform rules updated!');
    }

    /**
     * Helper: Get all settings
     */
    private function getSettings()
    {
        return Cache::remember('admin_settings', 3600, function () {
            $configPath = storage_path('app/settings.json');

            if (File::exists($configPath)) {
                return json_decode(File::get($configPath), true);
            }

            // Default settings
            return [
                'general' => [
                    'site_name' => 'Ngajar.ID',
                    'site_tagline' => 'Platform Pendidikan Inklusif',
                    'contact_email' => 'halo@ngajar.id',
                    'contact_phone' => '+62 812-3456-7890',
                    'contact_address' => 'Jl. Pendidikan No. 10, Bandung, Indonesia',
                ],
                'social' => [
                    'facebook_url' => '',
                    'twitter_url' => '',
                    'instagram_url' => '',
                    'youtube_url' => '',
                    'linkedin_url' => '',
                ],
                'rules' => [
                    'privacy_policy' => '',
                    'terms_of_service' => '',
                ],
            ];
        });
    }

    /**
     * Helper: Save settings to JSON file
     */
    private function saveSettings($category, $data)
    {
        $configPath = storage_path('app/settings.json');
        $settings = $this->getSettings();

        $settings[$category] = array_merge($settings[$category] ?? [], $data);

        File::put($configPath, json_encode($settings, JSON_PRETTY_PRINT));
        Cache::forget('admin_settings');
    }

    /**
     * Helper: Update .env file
     */
    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envPath, $envContent);
    }
}
