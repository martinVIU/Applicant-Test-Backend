<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new user with a hashed password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Check if the user already exists in the database
        $existingUser = User::where('email', $validatedData['email'])->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'A user with this email already exists.',
            ], 409); // HTTP 409 Conflict
        }

        // Create the user and hash the password
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Return success response
        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201); // HTTP 201 Created
    }
}

