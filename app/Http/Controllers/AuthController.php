<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Mail\TwoFactorCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Skip JWT middleware for these methods
        $this->middleware('auth:api', ['except' => [
            'login', 'register', 'showLoginForm', 'showRegistrationForm',
            'show2faForm', 'verify2fa', 'resend2fa', 'logout'
        ]]);
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            // Explicitly use web guard to avoid JWT confusion
            if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
                // Get the authenticated user
                $user = Auth::guard('web')->user();

                // Check if we got a valid user object
                if (!$user || !$user->id) {
                    Auth::guard('web')->logout();
                    return back()->withErrors([
                        'email' => 'Authentication error. Please try again.',
                    ])->onlyInput('email');
                }

                // Generate and store 2FA code
                $code = rand(100000, 999999);

                // Store the code and user ID in session
                Session::put('2fa_code', $code);
                Session::put('2fa_user_id', $user->id);

                // Log the user out temporarily until they verify
                Auth::guard('web')->logout();

                // Send email with verification code
                try {
                    Mail::to($request->email)->send(new TwoFactorCode($code));
                    return redirect()->route('2fa.show')->with('success', 'Please check your email for verification code');
                } catch (Exception $e) {
                    Log::error('Failed to send 2FA email: ' . $e->getMessage());
                    return back()->with('error', 'Could not send verification code: ' . $e->getMessage());
                }
            }
        } catch (TokenInvalidException $e) {
            Log::error('Invalid token error: ' . $e->getMessage());
            // Continue with login anyway as we're not using tokens for web login
        } catch (JWTException $e) {
            Log::error('JWT error: ' . $e->getMessage());
            // Continue with login anyway as we're not using JWT for web login
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during login: ' . $e->getMessage());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the 2FA verification form.
     */
    public function show2faForm()
    {
        if (!Session::has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-2fa');
    }

    /**
     * Verify the 2FA code.
     */
    public function verify2fa(Request $request)
    {
        $request->validate([
            'verification_code' => ['required', 'numeric'],
        ]);

        $sessionCode = Session::get('2fa_code');
        $userId = Session::get('2fa_user_id');

        if (!$sessionCode || !$userId) {
            return redirect()->route('login')->with('error', 'Verification session expired. Please login again.');
        }

        if ($request->verification_code == $sessionCode) {
            // Clear 2FA session data
            Session::forget('2fa_code');
            Session::forget('2fa_user_id');

            // Log the user in (explicitly use web guard)
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not found. Please login again.');
            }

            Auth::guard('web')->login($user);

            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Invalid verification code');
    }

    /**
     * Resend 2FA verification code.
     */
    public function resend2fa()
    {
        $userId = Session::get('2fa_user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::find($userId);
        $code = rand(100000, 999999);

        Session::put('2fa_code', $code);

        try {
            Mail::to($user->email)->send(new TwoFactorCode($code));
            return back()->with('success', 'Verification code has been resent');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not send verification code. Please try again.');
        }
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Ensure the user was created successfully
            if (!$user) {
                return back()->with('error', 'Failed to create user account. Please try again.');
            }

            // After registration, automatically log in the user
            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }


    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }
    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
