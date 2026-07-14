<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerifyEmail;
use App\Mail\WelcomeEmail;
use App\Models\EmailVerification;
use App\Models\Referral;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $referralCode = strtoupper(Str::random(10));
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars/users', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'referral_code' => $referralCode,
                'avatar_path' => $avatarPath,
                'email_notifications' => $request->boolean('email_notifications', true),
                'role' => $request->role,
                'status' => 'aktif',
            ]);

            Token::create([
                'user_id' => $user->user_id,
                'jumlah' => 0,
                'last_update' => now(),
            ]);

            if ($request->filled('referral_code')) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
                if ($referrer) {
                    Referral::create([
                        'referrer_id' => $referrer->user_id,
                        'referred_id' => $user->user_id,
                        'referral_code' => $request->referral_code,
                        'bonus_token' => 500,
                        'status' => 'pending',
                    ]);
                }
            }

            $verificationToken = Str::random(64);
            EmailVerification::create([
                'user_id' => $user->user_id,
                'token' => $verificationToken,
                'expires_at' => now()->addHours(24),
            ]);

            DB::commit();

            if ($user->email_notifications) {
                $verificationUrl = route('api.verify-email', ['token' => $verificationToken]);
                Mail::queue(new WelcomeEmail($user, $verificationUrl));
            }

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan periksa email Anda untuk verifikasi.',
                'user' => $user,
                'requires_email_verification' => true,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ], 403);
        }

        $token = $user->createToken('api-token', ['*'], [
            'expires_at' => now()->addHours(24),
        ])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 86400,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if ($user->isMurid()) {
            $user->load(['kelasIkuti', 'modulDimiliki', 'token']);
        } elseif ($user->isPengajar()) {
            $user->load(['kelasAjar', 'modulDibuat']);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    public function verifyEmail(string $token)
    {
        $verification = EmailVerification::where('token', $token)
            ->with('user')
            ->first();

        if (!$verification || !$verification->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Link verifikasi tidak valid atau telah kadaluarsa.',
            ], 400);
        }

        $verification->markAsVerified();

        $referral = Referral::where('referred_id', $verification->user->user_id)
            ->where('status', 'pending')
            ->first();

        if ($referral) {
            $referral->markAsRedeemed();
        }

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi! Akun Anda sekarang aktif sepenuhnya.',
        ]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah diverifikasi.',
            ], 400);
        }

        $verificationToken = Str::random(64);
        EmailVerification::create([
            'user_id' => $user->user_id,
            'token' => $verificationToken,
            'expires_at' => now()->addHours(24),
        ]);

        $verificationUrl = route('api.verify-email', ['token' => $verificationToken]);
        Mail::queue(new VerifyEmail($user, $verificationUrl));

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang. Silakan periksa inbox Anda.',
        ]);
    }

    public function updateProfile(Request $request)
    {
        // Implementation for API profile update
        return response()->json(['message' => 'Profile updated']);
    }

    public function uploadAvatar(Request $request)
    {
        // Implementation for API avatar upload
        return response()->json(['message' => 'Avatar uploaded']);
    }

    public function getPreferences(Request $request)
    {
        // Implementation
        return response()->json(['preferences' => []]);
    }

    public function updatePreferences(Request $request)
    {
        // Implementation
        return response()->json(['message' => 'Preferences updated']);
    }
}
