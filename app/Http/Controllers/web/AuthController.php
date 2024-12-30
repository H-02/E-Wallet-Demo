<?php

namespace App\Http\Controllers\web;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Show the login form for web users.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login for web users.
     *
     * @param LoginRequest $loginRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $loginRequest)
    {
        try {
            $credentials = $loginRequest->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if (!$user->is_active) {
                    Auth::logout();
                    return redirect()->back()->withErrors(['email' => 'Your account is inactive.']);
                }

                $loginRequest->session()->regenerate();

                // Redirect based on role
                if ($user->role === 'ADMIN') {
                    return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
                } else {
                    return redirect()->route('user.dashboard')->with('success', 'Welcome back!');
                }
            }

            // If authentication fails
            return redirect()->back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['email' => 'An error occurred during login. Please try again later.']);
        }
    }

    /**
     * Handle logout functionality for web users.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();

            // Invalidate and regenerate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'You have been logged out successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['email' => 'An error occurred during logout. Please try again later.']);
        }
    }
}
