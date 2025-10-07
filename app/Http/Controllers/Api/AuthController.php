<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST /api/auth/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
            'device'   => ['nullable','string'],
        ]);

        if (!Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']])) {
            return response()->json(['message'=>'Invalid credentials'], 422);
        }

        $user  = $request->user();
        $token = $user->createToken($data['device'] ?? 'api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user'  => $user,
        ]);
    }

    // POST /api/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message'=>'Logged out']);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
