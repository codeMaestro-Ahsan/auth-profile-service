<?php
namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $user->profile()->create([]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return (new UserResource($user))
            ->additional([
                'success'=>true,
                'message'=>'User registered successfully',
                'access_token'=>'Bearer',
            ]);
    }

    public function login(LoginRequest $request){
        $user = User::where('email',$request->email)->first();

        if(!$user||!Hash::check($request->password,$user->password)){
            return response()->json([
                'message'=>'Invalid credentials'
            ],422);
        }
        $user->token()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'Login successful',
            'token'=>$token,
            'user'=>new UserResource($user),
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out successfully']);
    }
}
