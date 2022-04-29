<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        // create token for registered user
        $token = $user->createToken('webapitoken')->plainTextToken;

        // respone data and status code
        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $data = $request->validated();

        // find login user on the db
        $user = User::where('email', $data['email'])->first();

        // check registed user or match password
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'message' => 'invalid Credentails'
            ], 401);
        }

        // create token for registered user
        $token = $user->createToken('web@p1token')->plainTextToken;

        // respone data and status code
        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Successfully logout'
        ], 205);
    }
}
