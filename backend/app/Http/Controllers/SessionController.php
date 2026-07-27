<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function store(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);



        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response(['redirect' => 'login', 'user' => Auth::user()], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 422);
    }
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return response(['success' => 'true'], 200);
    }
}
