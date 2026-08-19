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
                'email' => [
                    'required',
                    'email',
                ],
                'password' => [
                    'required',
                    'string',
                ],
            ]
        );

        if ($validator->fails()) {
            return response(
              $validator->errors(),
                422
            );
        }

        $credentials = $validator->validated();

        if (!Auth::attempt($credentials)) {
            return response(['email' => ' ', 'password' => 'invalid credentials'], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'success' => true,
        ], 200);
    }
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response(['success' => 'true'], 200);
    }
}
