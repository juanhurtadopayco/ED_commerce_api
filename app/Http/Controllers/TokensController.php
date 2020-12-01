<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokensController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email','password');

        $validator = Validator::make($credentials,[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong validation',
                'errors' => $validator->errors()
            ], 422);
        }

        return null;
        //https://www.youtube.com/watch?v=KI2Qoa3PKtU&t=727s&ab_channel=AlexArriaga
        //minuto: 32:30
    }
}
