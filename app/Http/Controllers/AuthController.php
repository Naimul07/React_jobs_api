<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $attribute = $request->validate([
                'name' => ['required', 'max:255', 'string'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                'password' => ['required', 'confirmed', 'min:6', Password::default()]
            ]);


            $user =  User::create($attribute);
            Auth::login($user);
            // return a token
            // $token = $user->createToken('auth_token')->plainTextToken;
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Registration successfull',
                'auth_token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                $e->errors()
            ], 422);
        }
    }

    //login
    public function login(Request $request)
    {
        $attribute = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($attribute)) {
            $user = $request->user(); // Get the authenticated user

            // Generate a token for the logged-in user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return a success response with the token
            return response()->json([
                'message' => 'Login successful',
                'auth_token' => $token,
            ], 200);
        }

        // Return an error response if credentials are invalid
        return response()->json([
            'message' => 'Invalid login credentials',
        ], 401);
    }

    public function logout(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if the user is authenticated
        if ($user) {
            // Revoke the token that was used to authenticate the current request
            $user->tokens()->delete(); // Revokes all tokens

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'User not authenticated',
        ], 401);
    }
}
