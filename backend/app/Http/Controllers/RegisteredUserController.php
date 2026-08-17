<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Psy\Readline\Hoa\Console;

class RegisteredUserController extends Controller
{


    public function index()
    {
        return User::all();
    }
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

        $request->session()->flash('credentials', $credentials);

        return response(['success' => true], 200);
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
        if (!$request->session()->has('credentials')) {
            return response(['error' => 'invalid entry'], 404);
        }

        $user = User::create([
            ...$request->session()->get('credentials'),
            ...$validator->safe()->all()
        ]);
        Auth::login($user);
        return response(['user' => $user, 'success' => true], 200);
    }

    public function storeNumber(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'phoneNumber' => ['required', 'regex:/\d{10}/'],
                'countryCode' => ['required']
            ]
        );

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        $user = $request->user();
        $user->phone_number = $request['phoneNumber'];
        $user->country_code = trim($request['countryCode'], '+');
        $user->save();

        return response(['success' => true], 200);
    }
    public function getCode(Request $request)
    {
        $faker = fake();
        $code = $faker->randomNumber(6, true);

        $request->session()->put('code', $code);
        return response(['code' => $code], 200);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'code' => [
                    'required',
                    Rule::in([session('code')])
                ]
            ]
        );

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }
        $request->session()->forget('code');
        return response(['success'=>true],200);
    }
}
