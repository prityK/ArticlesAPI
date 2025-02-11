<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $registerUserData = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);
        $user = User::create($registerUserData);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request){
        $data = $request->validate([
            'email'=>'required|email|exists:users',
            'password'=>'required|min:8'
        ]);
        $user = User::where('email',$data['email'])->first();

        if(!$user || !Hash::check($data['password'],$user->password)){
            return response(['message'=>'bad cred'],401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

        // Show the login form
        public function showLoginForm()
        {
            return view('login'); // This points to the login view
        }
    
    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
