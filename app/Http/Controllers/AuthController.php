<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $userInput = $request->validated();
        $userInput['password'] = Hash::make($userInput['password']);
        $user = User::create($userInput);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user)
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $userInput = $request->validated();
        $user = User::where('email', $userInput['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email or password is incorrect',
            ], 401);
        }

        $checkPassword = Hash::check($userInput['password'], $user->password);

        if (!$checkPassword) {
            return response()->json([
                'message' => 'Email or password is incorrect',
            ], 401);
        }

        $accessToken = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'access_token' => $accessToken
        ], 200);
    }

    public function getProfile()
    {
        return new UserResource(auth()->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful. Token revoked.'
        ], 200);
    }
}
