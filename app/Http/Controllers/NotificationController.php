<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('notification', compact('users'));
    }

    public function send(Request $request)
    {
        if ($request->user == 'all') {
            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new General($request->title, $request->link, $request->message));
            }
        } else {
            $user = User::find($request->user);
            $user->notify(new General($request->title, $request->link, $request->message));
        }
        return response()->json(['message' => 'Notification sent'], 200);
    }

    public function mark_all_read()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([], 200);
    }
}
