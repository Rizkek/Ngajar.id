<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'aktif',
            ]);

            // Buat record token otomatis dengan saldo awal 0
            Token::create([
                'user_id' => $user->user_id,
                'jumlah' => 0,
                'last_update' => now(),
            ]);

            DB::commit();

            // Cek apakah request API (JSON)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil!',
                    'user' => $user,
                ], 201);
            }

            // Untuk web request, login dan redirect
            Auth::login($user);

            return redirect($this->getRedirectPath($user->role))
                ->with('success', 'Registrasi berhasil! Selamat datang di Ngajar.ID');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registrasi gagal: ' . $e->getMessage(),
                ], 500);
            }

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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            return back()->withInput()->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        // Cek apakah user aktif
        if ($user->status !== 'aktif') {
            Auth::logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ], 403);
            }

            return back()->withInput()->withErrors([
                'email' => 'Akun Anda tidak aktif.',
            ]);
        }

        // Regenerate session untuk mencegah session fixation
        $request->session()->regenerate();

        // Respons API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'user' => $user,
            ]);
        }

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

        // Respons API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);
        }

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
            'murid' => '/murid/dashboard',
            'pengajar' => '/pengajar/dashboard',
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
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'password' => Hash::make(Str::random(16)), // Random password
                        'role' => 'murid', // Default role
                        'status' => 'aktif',
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
}
