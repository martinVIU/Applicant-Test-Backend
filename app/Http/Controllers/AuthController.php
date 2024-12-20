<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * User login method.
     */
public function login(Request $request)
{
    // Validate input fields with custom messages
    $validator = \Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
    ], [
        'name.required' => 'The name field is required.',
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters.',
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'error' => 'Validation failed',
            'message' => $validator->errors(),
        ], 400); // HTTP 400 Bad Request
    }

    // Retrieve the user with the provided name and email
    $user = User::where('name', $request->name)
        ->where('email', $request->email)
        ->first();

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'Invalid credentials',
            'message' => 'The provided username or email does not match our records.',
        ], 401); // HTTP 401 Unauthorized
    }

    // Verify the password
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'error' => 'Invalid credentials',
            'message' => 'The password is incorrect.',
        ], 401); // HTTP 401 Unauthorized
    }

    // Generate a login token
    $loginToken = $user->createToken('login-token')->plainTextToken;
    $refreshToken = Str::random(60); // Unique random refresh toquen

    // Store the refresh token in the database
    $user->update(['refresh_token' => $refreshToken]);
    
    return response()->json([
        'message' => 'Login successful',
        'login_token' => $loginToken,
    ], 200); // HTTP 200 OK
}


    /**
     * User logout method.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Refresh the current refresh_token for the current user
     */

public function refreshToken(Request $request)
{
    // Validate the refresh token that is going to be sent
    $validator = \Validator::make($request->all(), [
        'refresh_token' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error' => 'Validation failed',
            'message' => $validator->errors(),
        ], 400);
    }

    // Find user by the refresh token
    $user = User::where('refresh_token', $request->refresh_token)->first();

    if (!$user) {
        return response()->json([
            'error' => 'Invalid token',
            'message' => 'The provided refresh token is invalid.',
        ], 401);
    }

    // Generate a newlogin token
    $loginToken = $user->createToken('login-token')->plainTextToken;

    return response()->json([
        'message' => 'Token refreshed successfully',
        'login_token' => $loginToken,
    ], 200);
}

    /**
     * Refresh the login token through the refresh token
     */
     
public function refreshLoginToken(Request $request)
{
    // Validate the refresh token field
    $validator = \Validator::make($request->all(), [
        'refresh_token' => 'required|string',
    ], [
        'refresh_token.required' => 'The refresh token is required.',
    ]);

    // Check for validation errors
    if ($validator->fails()) {
        return response()->json([
            'error' => 'Validation failed',
            'message' => $validator->errors(),
        ], 400); // HTTP 400 Bad Request
    }

    // Retrieve the user associated with the provided refresh token
    $user = User::where('refresh_token', $request->refresh_token)->first();

    // Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'Invalid token',
            'message' => 'The provided refresh token is invalid.',
        ], 401); // HTTP 401 Unauthorized
    }

    // Generate a new login token
    $newLoginToken = $user->createToken('login-token')->plainTextToken;

    return response()->json([
        'message' => 'New login token generated successfully',
        'login_token' => $newLoginToken,
    ], 200); // HTTP 200 OK
}


    /**
     * User registration method
     */
public function register(Request $request)
{
    // Validate input with custom error messages
    try {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Ensure email is unique in the users table
            'password' => 'required|string|min:8|confirmed', // Password must be at least 8 characters and confirmed
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return error response with validation errors
        return response()->json([
            'error' => 'Validation failed',
            'message' => $e->errors(), // Detailed validation errors
        ], 422); // HTTP status code 422 Unprocessable Entity
    }

    // Create the user if validation passes
    try {
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hash the password for security
        ]);

        // Return success response with user details
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201); // HTTP status code 201 Created
    } catch (\Exception $e) {
        // Return error response if user creation fails
        return response()->json([
            'error' => 'Server error',
            'message' => 'An error occurred while creating the user. Please try again.',
        ], 500); // HTTP status code 500 Internal Server Error
    }
}


    /**
     * Get information about current user
     */
     
public function getUserInfo(Request $request)
{
    try {
        // Retrieve the authenticated user
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You need to log in to access this resource.',
            ], 401); // HTTP 401 Unauthorized
        }

        // Return user information
        return response()->json([
            'message' => 'User information retrieved successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
        ], 200); // HTTP 200 OK
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'An unexpected error occurred.',
            'message' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}

}

