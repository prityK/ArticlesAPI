<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;


class PasswordResetController extends Controller
{
    public function request()
    {
        return view('password.email');
    }

    public function sendResetLink(Request $request)
    {
        try {
            // Validate the request
            $request->validate(['email' => 'required|email']);

            // Attempt to send the password reset link
            $status = Password::sendResetLink($request->only('email'));

            // Check the response status
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => trans($status)], 200);
            }

            if ($status === Password::INVALID_USER) {
                return response()->json(['message' => trans($status)], 404);
            }

        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error sending password reset link: ' . $e->getMessage());

            // Return a generic error response
            return response()->json(['message' => 'An error occurred while sending the reset link.'], 500);
        }
    }

    public function reset($token)
    {
        return view('password.reset', ['token' => $token]);
    }

    public function update(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);
    
            // Retrieve token from database
            $tokenData = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();
    
            if (!$tokenData) {
                return response()->json(['message' => 'Token not found or expired.'], 400);
            }
    Log::info($request->token);
    Log::info($tokenData->token);

            // Verify token (tokens are hashed in Laravel)
            if ($request->token !== $tokenData->token) {
                return response()->json(['message' => 'Invalid token.'], 400);
            }
    
            // Find user by email
            $user = User::where('email', $request->email)->first();
    
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404);
            }
    
            // Update password
            $user->update([
                'password' => Hash::make($request->password),
            ]);
    
            // Delete the used token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
    
            // Logout user from all devices (Revoke all tokens)
            $user->tokens()->delete();
    
            return response()->json(['message' => 'Password reset successful. Please log in again.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong. Please try again later.'], 500);
        }
      }
}