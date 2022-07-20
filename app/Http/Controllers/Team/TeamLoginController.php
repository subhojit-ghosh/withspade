<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamLoginController extends Controller
{
    public function index()
    {
        return view('team.login.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $loginAttempt = Auth::guard('team')->attempt(['username' => $request->username, 'password' => $request->password], true);

        if ($loginAttempt) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login success'], 200);
        } else {
            return response()->json(['message' => 'Invalid login credentials'], 400);
        }
    }
}
