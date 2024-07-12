<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $newUser->createToken('API-TOKEN')->plainTextToken;

            return redirect()->route('dashboard')->with('success', 'Registration successful.')->with('token', $token);

        } catch (\Throwable $error) {
            return redirect()->route('register')->withErrors(['error' => $error->getMessage()]);
        }
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

                return redirect()->route('dashboard')->with('success', 'Login successful.')->with('token', $token);

            } else {
                return redirect()->route('login')->withErrors(['error' => 'Invalid credentials']);
            }

        } catch (\Throwable $error) {
            return redirect()->route('login')->withErrors(['error' => $error->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'Logout successful.');
        } catch (\Throwable $error) {
            return redirect()->route('dashboard')->withErrors(['error' => $error->getMessage()]);
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
