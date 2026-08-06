<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['email', 'required'],
                'password' => ['required']
            ]
        );

        if ($validator->fails()) {
            return response(['email' => ' ', 'password' => 'invalid credentials'], 422);
        }

        if (Auth::attempt($validator->validated())) {
            $request->session()->regenerate();
            return response(['success'=>true],200);
        }
    }
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response(['success' => 'true'], 200);
    }
}
