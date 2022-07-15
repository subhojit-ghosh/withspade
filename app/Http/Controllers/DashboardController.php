<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
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
        $user->save();

        return redirect()->route('dashboard.index');
    }
}
