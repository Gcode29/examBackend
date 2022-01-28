<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function store(Request $request)
    {   
        // validate required fields
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('username', $request->username)->first();

        // Throw's Error if User does not exist or invalid password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The Provided credentials are incorrect.'],
            ]);
        }

        // generate API token
        $token = $user->createToken($request->header('user_agent'))->plainTextToken;

        $response = [
            'token' => $token,
            'user' => $user,
        ];

        return response()->json($response);
    }

    public function register(Request $request)
    {   
        $request->validate([
            'fullname' => ['required'],
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::create($request->all());

        $response = [
            'message' => 'Successfully Registered',
            'user' => $user,
        ];

        return response()->json($response);
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();
        
        return response()->json('logout', 201);
    }

}
