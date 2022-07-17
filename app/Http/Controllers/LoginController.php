<?php

namespace App\Http\Controllers;

use App\Mail\LoginVerificationCode;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;
use Twilio\Rest\Client;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid email or password'], 400);
        }

        if ($user->two_step) {
            $identifier = (string) Str::uuid();
            $user->two_step_identifier = $identifier;
            $user->save();

            $request->session()->put('two_step_identifier', $identifier);

            if ($user->two_step_method == 'email') {
                $otp = new Otp;
                $otp->code = random_int(100000, 999999);
                $otp->two_step_identifier = $identifier;
                $otp->save();

                Mail::to($user)->send(new LoginVerificationCode($otp->code));

                return response()->json(['message' => 'Complete 2FA', 'two_step' => true, 'two_step_method' => 'email'], 200);
            }

            if ($user->two_step_method == 'mobile') {
                $otp = new Otp;
                $otp->code = random_int(100000, 999999);
                $otp->two_step_identifier = $identifier;
                $otp->save();

                $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                $twilio->messages->create(
                    $user->mobile,
                    [
                        'from' => env('TWILIO_AUTH_NUMBER'),
                        'body' => 'Your login verification code is ' . $otp->code
                    ]
                );

                return response()->json(['message' => 'Complete 2FA', 'two_step' => true, 'two_step_method' => 'mobile'], 200);
            }

            if ($user->two_step_method == 'google2fa') {

                return response()->json(['message' => 'Complete 2FA', 'two_step' => true, 'two_step_method' => 'google2fa'], 200);
            }
        } else {
            $loginAttempt = Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ], true);

            if ($loginAttempt) {
                $request->session()->regenerate();
                return response()->json(['message' => 'Login success'], 200);
            } else {
                return response()->json(['message' => 'Invalid login credentials'], 400);
            }
        }
    }

    public function two_step_email(Request $request)
    {
        if ($request->session()->has('two_step_identifier')) {
            return view('login.two-step.email');
        } else {
            return redirect()->route('login.index');
        }
    }

    public function verify_email(Request $request)
    {

        $request->validate([
            'otp' => ['required']
        ]);

        $otp = Otp::where('two_step_identifier', $request->session()->get('two_step_identifier'))->where('code', $request->otp)->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $user = User::where('two_step_identifier', $otp->two_step_identifier)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $loginAttempt = Auth::loginUsingId($user->id, true);

        if ($loginAttempt) {
            $request->session()->forget('two_step_identifier');
            $request->session()->regenerate();
            return response()->json(['message' => 'Login success'], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 400);
        }
    }

    public function two_step_mobile(Request $request)
    {
        if ($request->session()->has('two_step_identifier')) {
            return view('login.two-step.mobile');
        } else {
            return redirect()->route('login.index');
        }
    }

    public function verify_mobile(Request $request)
    {

        $request->validate([
            'otp' => ['required']
        ]);

        $otp = Otp::where('two_step_identifier', $request->session()->get('two_step_identifier'))->where('code', $request->otp)->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $user = User::where('two_step_identifier', $otp->two_step_identifier)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $loginAttempt = Auth::loginUsingId($user->id, true);

        if ($loginAttempt) {
            $request->session()->forget('two_step_identifier');
            $request->session()->regenerate();
            return response()->json(['message' => 'Login success'], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 400);
        }
    }

    public function two_step_google2fa(Request $request)
    {
        if ($request->session()->has('two_step_identifier')) {
            return view('login.two-step.google2fa');
        } else {
            return redirect()->route('login.index');
        }
    }

    public function verify_google2fa(Request $request)
    {
        $request->validate([
            'secret' => ['required']
        ]);

        $user = User::where('two_step_identifier', $request->session()->get('two_step_identifier'))->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->secret);

        if (!$valid) {
            return response()->json(['message' => 'Invalid varification code'], 400);
        }

        $loginAttempt = Auth::loginUsingId($user->id, true);

        if ($loginAttempt) {
            $request->session()->forget('two_step_identifier');
            $request->session()->regenerate();
            return response()->json(['message' => 'Login success'], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 400);
        }
    }
}
