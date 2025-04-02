<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Mail\TwoFactorCode;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

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
            'show2faForm', 'verify2fa', 'resend2fa', 'logout',
            'forgotPassword', 'sendPasswordCode', 'showResetPassword', 'resetPassword',
            'apiLogin', 'apiLogout', 'refresh'
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
            // Find the staff member by email
            $staff = Staff::where('email', $request->email)->first();

            // Check if staff exists and is active
            if (!$staff || $staff->active != 1) {
                return back()->withErrors([
                    'email' => 'These credentials do not match our records or the account is inactive.',
                ])->onlyInput('email');
            }

            // Verify password
            if (!Hash::check($request->password, $staff->password)) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->onlyInput('email');
            }

            // Login successful, generate 2FA code
            $code = rand(100000, 999999);

            // Store the code in session
            Session::put('2fa_code', $code);
            Session::put('2fa_staff_id', $staff->staff_id);

            // Send email with verification code
            try {
                Mail::to($request->email)->send(new TwoFactorCode($code));
                return redirect()->route('2fa.show')->with('success', 'Please check your email for verification code');
            } catch (\Exception $e) {
                Log::error('Failed to send 2FA email: ' . $e->getMessage());
                return back()->with('error', 'Could not send verification code: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during login: ' . $e->getMessage());
        }
    }

    /**
     * Show the 2FA verification form.
     */
    public function show2faForm()
    {
        if (!Session::has('2fa_staff_id')) {
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
        $staffId = Session::get('2fa_staff_id');

        if (!$sessionCode || !$staffId) {
            return redirect()->route('login')->with('error', 'Verification session expired. Please login again.');
        }

        if ($request->verification_code == $sessionCode) {
            // Clear 2FA session data
            Session::forget('2fa_code');
            Session::forget('2fa_staff_id');

            // Log the staff in
            $staff = Staff::find($staffId);
            if (!$staff) {
                return redirect()->route('login')->with('error', 'Staff not found. Please login again.');
            }

            // Create a custom auth login for the staff
            Auth::guard('web')->loginUsingId($staff->staff_id);

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
        $staffId = Session::get('2fa_staff_id');

        if (!$staffId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $staff = Staff::find($staffId);
        $code = rand(100000, 999999);

        Session::put('2fa_code', $code);

        try {
            Mail::to($staff->email)->send(new TwoFactorCode($code));
            return back()->with('success', 'Verification code has been resent');
        } catch (\Exception $e) {
            return back()->with('error', 'Could not send verification code. Please try again.');
        }
    }

    /**
     * Handle staff registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:staff'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:255', 'unique:staff'],
        ]);

        try {
            // Default values for new staff
            $staff = Staff::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $request->username,
                'address_id' => 1, // Default address ID
                'store_id' => 1,   // Default store ID
                'active' => 1,     // Active by default
                'rol_id' => 2,     // Changed from 3 to 2 as requested
                'last_update' => now()
            ]);

            // Ensure the staff was created successfully
            if (!$staff) {
                return back()->with('error', 'Failed to create staff account. Please try again.');
            }

            // After registration, automatically log in the staff
            Auth::loginUsingId($staff->staff_id);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('Staff registration failed: ' . $e->getMessage());
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to dashboard instead of login
        return redirect()->route('dashboard');
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset code to email
     */
    public function sendPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:staff,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $staff = Staff::where('email', $request->email)->first();

            // Generate verification code
            $code = rand(100000, 999999);
            $staff->two_factor_code = Hash::make($code);
            $staff->two_factor_expires_at = now()->addMinutes(10);
            $staff->save();

            // Store email in session for password reset
            Session::put('reset_email', $request->email);

            // Send email with the code
            Mail::to($staff->email)->send(new TwoFactorCode($code));

            return redirect()->route('password.reset')
                ->with('success', 'Reset code sent to your email.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset code: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to send reset code. Please try again later.')
                ->withInput();
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword()
    {
        // Check if reset email exists in session
        if (!Session::has('reset_email')) {
            return redirect()->route('password.forgot')
                ->with('error', 'Please request a password reset code first.');
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $email = Session::get('reset_email');
        if (!$email) {
            return redirect()->route('password.forgot')
                ->with('error', 'No email found for password reset. Please try again.');
        }

        try {
            $staff = Staff::where('email', $email)->first();

            if (!$staff) {
                return redirect()->route('password.forgot')
                    ->with('error', 'User not found. Please try again.');
            }

            // Check if the code is valid
            if (!Hash::check($request->code, $staff->two_factor_code) ||
                now()->gt($staff->two_factor_expires_at)) {
                return back()->withErrors(['code' => 'Invalid or expired code.']);
            }

            // Update password
            $staff->password = Hash::make($request->password);
            $staff->two_factor_code = null;
            $staff->two_factor_expires_at = null;
            $staff->save();

            Session::forget('reset_email');

            return redirect()->route('login')
                ->with('success', 'Password reset successfully. Please login with your new password.');
        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            return back()->with('error', 'Password reset failed. Please try again.');
        }
    }

    /**
     * API login, returns JWT token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * API logout (Invalidate the token)
     *
     * @return \Illuminate\Http\Response
     */
    public function apiLogout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout, please try again.'
            ], 500);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not refresh token'
            ], 401);
        }
    }
}
