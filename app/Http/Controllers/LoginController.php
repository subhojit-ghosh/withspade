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

            $otp = new Otp;
            $otp->code = random_int(100000, 999999);
            $otp->two_step_identifier = $identifier;
            $otp->save();

            Mail::to($user)->send(new LoginVerificationCode($otp->code));

            $request->session()->put('two_step_identifier', $identifier);
            return response()->json(['message' => 'Login success', 'two_step' => true], 200);
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

    public function two_step(Request $request)
    {
        if ($request->session()->has('two_step_identifier')) {
            return view('login.two-step');
        } else {
            return redirect()->route('login.index');
        }
    }

    public function verify(Request $request)
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
}
