<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\AuthJob;
use Illuminate\Http\Request;
use App\Events\UserRegistered;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $token = $user->createToken('register')->plainTextToken;
        AuthJob::dispatch($user);
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request){
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'User not found or password is incorrect'
            ], 404);
        }
        $token = $user->createToken('login')->plainTextToken;
        return response()->json([
            'message' => 'User logged successfully',
            'token' => $token
        ]);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }
}
