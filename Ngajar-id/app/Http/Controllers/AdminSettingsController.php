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

        foreach ($validated as $key => $value) {
            \App\Models\Setting::set($key, $value, 'general');
        }

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

        foreach ($validated as $key => $value) {
            \App\Models\Setting::set($key, $value, 'social');
        }

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
            'midtrans_is_production' => 'nullable|boolean',
            'xendit_secret_key' => 'nullable|string',
            'xendit_public_key' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            // Handle logical checkbox for production
            if ($key === 'midtrans_is_production') {
                $value = $request->has('midtrans_is_production') ? '1' : '0';
            }
            \App\Models\Setting::set($key, $value, 'payment');
        }

        return back()->with('success', 'Payment settings updated successfully!');
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

        foreach ($validated as $key => $value) {
            \App\Models\Setting::set($key, $value, 'rules');
        }

        return back()->with('success', 'Platform rules updated!');
    }

    /**
     * Helper: Get all settings
     */
    private function getSettings()
    {
        $dbSettings = \App\Models\Setting::getAllGrouped();

        // Merge with defaults if not exists
        return [
            'general' => array_merge([
                'site_name' => 'Ngajar.ID',
                'site_tagline' => 'Platform Pendidikan Inklusif',
                'contact_email' => 'halo@ngajar.id',
                'contact_phone' => '+62 812-3456-7890',
                'contact_address' => 'Jl. Pendidikan No. 10, Bandung, Indonesia',
            ], $dbSettings['general'] ?? []),
            'social' => array_merge([
                'facebook_url' => '',
                'twitter_url' => '',
                'instagram_url' => '',
                'youtube_url' => '',
                'linkedin_url' => '',
            ], $dbSettings['social'] ?? []),
            'payment' => array_merge([
                'midtrans_server_key' => config('midtrans.server_key'),
                'midtrans_client_key' => config('midtrans.client_key'),
                'midtrans_is_production' => config('midtrans.is_production') ? '1' : '0',
                'xendit_secret_key' => config('xendit.api_key'),
                'xendit_public_key' => '',
            ], $dbSettings['payment'] ?? []),
            'rules' => array_merge([
                'privacy_policy' => '',
                'terms_of_service' => '',
            ], $dbSettings['rules'] ?? []),
        ];
    }
}

