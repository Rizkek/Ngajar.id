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

class AuthController extends Controller
{
    /**
     * Register a new user
     * 
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'aktif',
            ]);

            // Auto-create token record with initial balance of 0
            Token::create([
                'user_id' => $user->user_id,
                'jumlah' => 0,
                'last_update' => now(),
            ]);

            DB::commit();

            // Check if request expects JSON (API)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil!',
                    'user' => $user,
                ], 201);
            }

            // For web requests, login and redirect
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

        // Attempt authentication
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

        // Check if user is active
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

        // Regenerate session to prevent session fixation
        $request->session()->regenerate();

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'user' => $user,
            ]);
        }

        // Web response - redirect based on role
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
        // Perform logout first
        Auth::guard('web')->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);
        }

        // Web redirect to landing page
        return redirect('/')->with('success', 'Anda telah logout.');
    }

    /**
     * Get authenticated user data
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Load relationships based on role
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
     * Get redirect path based on user role
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
}
