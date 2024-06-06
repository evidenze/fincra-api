<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * New user registration.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $is_admin = false;
        if ($request->is_admin == '') {
            $is_admin = false;
        } else {
            $is_admin = $request->is_admin;
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'wallet_id' => rand(1000000000, 9999999999),
            'is_admin' => $is_admin,
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json(['status' => true, 'message' => 'User registered successfully', 'token' => $token, 'data' => $user], 201);
    }

    /**
     * User login.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json(['status' => true, 'message' => 'Login successful', 'token' => $token]);
    }
}
