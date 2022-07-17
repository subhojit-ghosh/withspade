<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::id());
        $google2fa = new Google2FA();
        if (!$user->google2fa_verified) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
        }
        $user->save();

        $google2fa->setQrcodeService(
            new \PragmaRX\Google2FAQRCode\QRCode\Bacon(
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            )
        );

        $inlineUrl = $google2fa->getQRCodeInline(
            'TestWithspade',
            $user->email,
            $user->google2fa_secret
        );

        return view('dashboard.index', compact('inlineUrl'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        return redirect()->route('login.index');
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        $user->two_step = $request->two_step == 'on';
        $user->mobile = $request->mobile;
        $user->two_step_method = $request->two_step_method;
        $user->save();

        return redirect()->route('dashboard.index');
    }

    public function verify_google2fa(Request $request)
    {
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(Auth::user()->google2fa_secret, $request->secret);

        if ($valid) {
            $user = User::find(Auth::id());
            $user->google2fa_verified = $valid;
            $user->save();
            return response()->json(['message' => 'Vefied'], 200);
        } else {
            return response()->json(['message' => 'Invalid code'], 400);
        }
    }
}
