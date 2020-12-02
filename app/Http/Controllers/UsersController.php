<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{
    /**
     * Create a new UsersController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create']]);
    }
    
    public function create(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong validation',
                'action' => 'Create commerce',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->email);   
        $user->save();

        if ($user) {

            return response()->json([
                'success' => true,
                'message' => 'Commerce successfully created',
                'action' => 'Create commerce',
                'user' => $user
            ], 201);

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Commerce successfully created',
                'action' => 'Create commerce',
            ], 400);
        }

    }
}
