<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{

    /**
     * Get all settings
     * GET /admin/settings
     */
    public function index(Request $request)
    {
        try {
            $settings = $this->getSettings();

            return view('admin.settings.index', compact('settings'));
        } catch (\Exception $e) {
            \Log::error('AdminSettingsController@index: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update general settings
     * POST /admin/settings/general
     */
    public function updateGeneral(Request $request)
    {
        try {
            $validated = $request->validate([
                'site_name' => 'nullable|string|max:100',
                'site_tagline' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:20',
                'contact_address' => 'nullable|string',
            ]);

            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    \App\Models\Setting::set($key, $value, 'general');
                }
            }

            Cache::forget('settings');

            return back()->with('success', 'General settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminSettingsController@updateGeneral: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update social media settings
     * POST /admin/settings/social
     */
    public function updateSocial(Request $request)
    {
        try {
            $validated = $request->validate([
                'facebook_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
            ]);

            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    \App\Models\Setting::set($key, $value, 'social');
                }
            }

            Cache::forget('settings');

            return back()->with('success', 'Social media settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminSettingsController@updateSocial: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update payment settings
     * POST /admin/settings/payment
     */
    public function updatePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'midtrans_server_key' => 'nullable|string',
                'midtrans_client_key' => 'nullable|string',
                'midtrans_is_production' => 'nullable|boolean',
                'xendit_secret_key' => 'nullable|string',
                'xendit_public_key' => 'nullable|string',
            ]);

            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    if ($key === 'midtrans_is_production') {
                        $value = (bool) $value ? '1' : '0';
                    }
                    \App\Models\Setting::set($key, $value, 'payment');
                }
            }

            Cache::forget('settings');

            return back()->with('success', 'Payment settings updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminSettingsController@updatePayment: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update platform rules (Privacy Policy, Terms of Service)
     * POST /admin/settings/rules
     */
    public function updateRules(Request $request)
    {
        try {
            $validated = $request->validate([
                'privacy_policy' => 'nullable|string',
                'terms_of_service' => 'nullable|string',
            ]);

            foreach ($validated as $key => $value) {
                if ($value !== null) {
                    \App\Models\Setting::set($key, $value, 'rules');
                }
            }

            Cache::forget('settings');

            return back()->with('success', 'Platform rules updated successfully');
        } catch (\Exception $e) {
            \Log::error('AdminSettingsController@updateRules: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get all settings (helper)
     */
    private function getSettings()
    {
        $cached = Cache::rememberForever('settings', function () {
            $dbSettings = \App\Models\Setting::getAllGrouped() ?? [];

            return [
                'general' => array_merge([
                    'site_name' => 'Ngajar.ID',
                    'site_tagline' => 'Platform Pendidikan Inklusif',
                    'contact_email' => config('mail.from.address'),
                    'contact_phone' => '',
                    'contact_address' => '',
                ], $dbSettings['general'] ?? []),
                'social' => array_merge([
                    'facebook_url' => '',
                    'twitter_url' => '',
                    'instagram_url' => '',
                    'youtube_url' => '',
                    'linkedin_url' => '',
                ], $dbSettings['social'] ?? []),
                'payment' => array_merge([
                    'midtrans_server_key' => config('midtrans.server_key') ? '***hidden***' : '',
                    'midtrans_client_key' => config('midtrans.client_key'),
                    'midtrans_is_production' => config('midtrans.is_production') ? 1 : 0,
                    'xendit_secret_key' => config('xendit.api_key') ? '***hidden***' : '',
                    'xendit_public_key' => '',
                ], $dbSettings['payment'] ?? []),
                'rules' => array_merge([
                    'privacy_policy' => '',
                    'terms_of_service' => '',
                ], $dbSettings['rules'] ?? []),
            ];
        });

        return $cached;
    }
}

