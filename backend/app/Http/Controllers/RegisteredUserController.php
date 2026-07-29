<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:App\Models\User,email'],
                'password' => ['required', 'confirmed',] // TODO :Password::min(8)->letters()->numbers()
            ]
        );

        if ($validator->fails()) { //->stopOnFirstFailure()

            return response($validator->errors(), 422);
        }

        $credentials = $validator->validated();


        $user = User::create($credentials);

        Auth::login($user);
        return response(['user' => $user, 'success' => true], 200);
    }

    public function addInfo(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'publisher_name' => ['required'],
                'location' => ['required']
            ]
        );

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        $publisherName = $request->input('publisher_name');
        $location = $request->input('location');

        $user = Auth::user();
        if ($user->publisher_name!=null || $user->location != null) {
            return response(['publisher_name'=>' ','location'=>'some of the values have already been set'],422);
        }
        $user->publisher_name = $publisherName;
        $user->location = $location;    
        $user->save();
        return response(['user' => $user, 'success' => true], 200);
    }
}
