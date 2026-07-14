<?php

namespace App\Http\Controllers\Auth;

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Registrasi user baru
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Generate referral code for new user
            $referralCode = strtoupper(Str::random(10));

            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars/users', 'public');
            }

            // Buat user baru
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

            // Buat record token otomatis dengan saldo awal 0
            Token::create([
                'user_id' => $user->user_id,
                'jumlah' => 0,
                'last_update' => now(),
            ]);

            // Handle referral code dari parameter
            if ($request->filled('referral_code')) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
                if ($referrer) {
                    Referral::create([
                        'referrer_id' => $referrer->user_id,
                        'referred_id' => $user->user_id,
                        'referral_code' => $request->referral_code,
                        'bonus_token' => 500, // Bonus untuk yang mereferensikan
                        'status' => 'pending',
                    ]);
                }
            }

            // Create email verification token if email verification is required
            $verificationToken = Str::random(64);
            EmailVerification::create([
                'user_id' => $user->user_id,
                'token' => $verificationToken,
                'expires_at' => now()->addHours(24),
            ]);

            DB::commit();

            // Send welcome email with verification link
            if ($user->email_notifications) {
                $verificationUrl = route('auth.verify-email', ['token' => $verificationToken]);
                Mail::queue(new WelcomeEmail($user, $verificationUrl));
            }

            // Untuk web request, login dan redirect
            Auth::login($user);

            return redirect($this->getRedirectPath($user->role))
                ->with('success', 'Registrasi berhasil! Silakan verifikasi email Anda untuk akses penuh.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => 'Registrasi gagal. Silakan coba lagi.']);
        }
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Coba autentikasi
        if (!Auth::attempt($credentials)) {
            return back()->withInput()->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        // Cek apakah user aktif
        if ($user->status !== 'aktif') {
            Auth::logout();

            return back()->withInput()->withErrors([
                'email' => 'Akun Anda tidak aktif.',
            ]);
        }

        // Regenerate session untuk mencegah session fixation
        $request->session()->regenerate();

        // Respons web - redirect berdasarkan role
        return redirect($this->getRedirectPath($user->role))
            ->with('success', 'Selamat datang kembali, ' . $user->name);
    }

    /**
     * Logout user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Lakukan logout terlebih dahulu
        Auth::guard('web')->logout();

        // Invalidate dan regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect web ke halaman landing
        return redirect('/')->with('success', 'Anda telah logout.');
    }

    /**
     * Ambil data user yang sedang login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Load relasi berdasarkan role
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

    /**
     * Ambil path redirect berdasarkan role user
     *
     * @param string $role
     * @return string
     */
    private function getRedirectPath(string $role): string
    {
        return match ($role) {
            'murid' => '/student/dashboard',
            'pengajar' => '/teacher/dashboard',
            'admin' => '/admin',
            default => '/',
        };
    }

    /**
     * Redirect to Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google Callback
     */
    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Check if user exists with email (merge account) or create new
                $user = User::where('email', $googleUser->email)->first();

                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar
                    ]);
                } else {
                    DB::beginTransaction();
                    $referralCode = strtoupper(Str::random(10));

                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'referral_code' => $referralCode,
                        'password' => Hash::make(Str::random(16)), // Random password
                        'role' => 'murid', // Default role
                        'status' => 'aktif',
                        'email_verified_at' => now(), // Google verified
                    ]);

                    Token::create([
                        'user_id' => $user->user_id,
                        'jumlah' => 0,
                        'last_update' => now(),
                    ]);
                    DB::commit();
                }
            }

            Auth::login($user);

            return redirect($this->getRedirectPath($user->role))
                ->with('success', 'Login berhasil dengan Google!');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Login Google gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(string $token)
    {
        $verification = EmailVerification::where('token', $token)
            ->with('user')
            ->first();

        if (!$verification) {
            return redirect('/login')->withErrors(['error' => 'Link verifikasi tidak valid atau telah kadaluarsa.']);
        }

        if (!$verification->isValid()) {
            return redirect('/login')->withErrors(['error' => 'Link verifikasi telah kadaluarsa. Silakan minta ulang.']);
        }

        // Mark as verified
        $verification->markAsVerified();

        // Handle referral bonus if exists
        $referral = Referral::where('referred_id', $verification->user->user_id)
            ->where('status', 'pending')
            ->first();

        if ($referral) {
            $referral->markAsRedeemed();
        }

        Auth::login($verification->user);

        return redirect($this->getRedirectPath($verification->user->role))
            ->with('success', 'Email berhasil diverifikasi! Akun Anda sekarang aktif sepenuhnya.');
    }

    /**
     * Resend email verification
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return back()->withErrors(['email' => 'Email sudah diverifikasi.']);
        }

        // Create new verification token
        $verificationToken = Str::random(64);
        EmailVerification::create([
            'user_id' => $user->user_id,
            'token' => $verificationToken,
            'expires_at' => now()->addHours(24),
        ]);

        $verificationUrl = route('auth.verify-email', ['token' => $verificationToken]);
        Mail::queue(new VerifyEmail($user, $verificationUrl));

        return back()->with('success', 'Email verifikasi telah dikirim ulang. Silakan periksa inbox Anda.');
    }
}


