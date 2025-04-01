<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use App\Mail\TwoFCodeEmail;
use Illuminate\Support\Facades\Validator;
use App\Models\Staff;

class AuthController extends Controller
{
    public function verify(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Si la validación falla, redirigir con errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buscar al usuario por su email
        $user = Staff::where('email', $request->email)->first();

        // Verificar si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['email' => 'Credenciales incorrectas'])
                ->withInput();
        }

        if (!$user->active) {
            return redirect()->back()
                ->withErrors(['email' => 'Usuario inactivo'])
                ->withInput();
        }

        // Generar y enviar el código de 2FA
        $code = rand(100000, 999999);
        $user->two_factor_code = bcrypt($code);
        $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new TwoFCodeEmail($code));

        session(['2fa_user_id' => $user->staff_id]);

        // Redirigir a la vista de 2FA
        return redirect()->route('2fa.verify')->with('message', 'Código de verificación enviado');
    }


    public function logout()
    {
        session()->forget('jwt_token');
        Auth::logout();
        return redirect()->route('login');
    }

    public function sendTwoFactorCode()
    {
        $user = Auth::user();
        $code = rand(100000, 999999);
        $user->two_factor_code = bcrypt($code);
        $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new TwoFCodeEmail($code));

        return redirect()->route('2fa.verify')->with('message', 'Código de verificación enviado');
    }

    public function verifyTwoFactorCode(Request $request)
    {
        // Obtener el ID del usuario desde la sesión
        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['error' => 'No se encontró una sesión activa para la verificación.']);
        }

        // Buscar al usuario por su ID
        $user = Staff::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Usuario no encontrado.']);
        }

        // Verificar el código de 2FA
        if (Hash::check($request->code, $user->two_factor_code) && now()->lt($user->two_factor_expires_at)) {
            // Limpiar el código de 2FA
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // Autenticar al usuario
            Auth::login($user);

            // Generar el token JWT
            $token = JWTAuth::fromUser($user);
            session(['jwt_token' => $token]);
            session(['role_id' => $user->role_id]);

            // Limpiar la sesión temporal
            session()->forget('2fa_user_id');

            return redirect()->route('home');
        }

        return redirect()->back()->withErrors(['code' => 'Código incorrecto o expirado']);
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Enviar código de recuperación por correo
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:staff,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Staff::where('email', $request->email)->first();

        // Generar código de verificación
        $code = rand(100000, 999999);
        $user->two_factor_code = bcrypt($code);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Guardar el correo en la sesión
        session(['reset_email' => $request->email]);

        // Enviar correo con el código
        Mail::to($user->email)->send(new TwoFCodeEmail($code));

        return redirect()->route('password.reset')->with('message', 'Código de verificación enviado a tu correo.');
    }

    // Mostrar formulario para restablecer contraseña
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    // Restablecer la contraseña
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Obtener el correo de la sesión
        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'No se encontró un correo asociado a esta solicitud.']);
        }

        $user = Staff::where('email', $email)->first();

        // Verificar el código de recuperación
        if (!Hash::check($request->code, $user->two_factor_code) || now()->gt($user->two_factor_expires_at)) {
            return redirect()->back()->withErrors(['code' => 'El código es incorrecto o ha expirado.']);
        }

        // Restablecer la contraseña
        $user->password = Hash::make($request->password);
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // Limpiar la sesión
        session()->forget('reset_email');

        return redirect()->route('login')->with('message', 'Contraseña restablecida correctamente. Ahora puedes iniciar sesión.');
    }
}
