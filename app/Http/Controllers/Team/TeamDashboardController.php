<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamDashboardController extends Controller
{
    public function index()
    {
        return view('team.dashboard.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('team')->logout();

        $request->session()->invalidate();

        return redirect()->route('team.login.index');
    }
}
