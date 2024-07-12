<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // email = 'user@domain.com';
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $newUser->createToken('API-TOKEN')->plainTextToken;

            return response()->json([
                'status' => 'Ok',
                'message' => 'User created successfully',
                'token' => $token,
            ], 200);

        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:8',
            ]);

            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $request->session()->regenerate();
                $token = $request->user()->createToken('API-TOKEN')->plainTextToken;
                return response()->json([
                    'status' => 'Ok',
                    'message' => 'Login successful',
                    'token' => $token,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'Ok',
                'message' => 'Logout successful',
            ], 200);
        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function userInfo()
    {
        try {
            $userData = auth()->user();
            return response()->json([
                'status' => 'Ok',
                'message' => 'User profile retrieved',
                'data' => $userData,
            ], 200);
        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
}
