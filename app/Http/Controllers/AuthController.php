<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong request',
                $validator->errors()
            ], 400);
        }

        $tokenValidity = 24 * 60;

        $this->guard()->factory()->setTTL($tokenValidity);

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'document' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong request',
                $validator->errors()
            ], 400);
        }

        $user = User::create(
            array_merge($validator->validated() , [
            'password' => bcrypt($request->password)
        ])
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Commerce created successfully',
            'data' => $user
        ], 201);
        

    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Commerce logged out successfully'
        ], 201);
    }

    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Get commerce successfully',
            'data' => $this->guard()->user()
        ], 201);
    }

    public function refresh()
    {
        $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'token_validity' =>  $this->guard()->factory()->getTTL($token) * 60
        ]);
    }

    protected function guard(){
        return Auth::guard();
    }
}
