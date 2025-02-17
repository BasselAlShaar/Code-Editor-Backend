<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use JWTAuth;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        

        return response()->json([
            'status' => 'success',
           // 'user' => JWTAuth::user(),
            'role'=>JWTAuth::user()->role,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error during registration: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Internal Server Error',
        ], 500);
    }
}


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
        } catch (Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }

        return response()->json([
            'status' => 'success',
            'user' => JWTAuth::user(),
            'JWTAuthorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

}