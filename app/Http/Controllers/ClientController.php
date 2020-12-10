<?php

namespace App\Http\Controllers;

use App\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = $this->user->clients()->get([
            'id',
            'name', 
            'email', 
            'document'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Get clients successfully',
            'data' => $clients
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string', 
                'email' => 'required|email',
                'document' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong request',
                    $validator->errors()
                ], 400);
            }
    
            $client = new Client;
            $client->name = $request->name;
            $client->email = $request->email;
            $client->document = $request->document;
    
            if($this->user->clients()->save($client)){
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully',
                    'data' => $client
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the client could not be saved',
                ], 400);
            }
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' =>'Oops, the client could not be saved',
                'data' => $ex->getMessage()
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show($client)
    {
        try {
            $client = Client::findOrFail($client);
            return response()->json([
                'success' => true,
                'message' => 'Client show successfully',
                'data' => $client
            ], 200);    
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the client could not be find',
                'errors' =>  $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string', 
                'email' => 'required|email',
                'document' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong request',
                    $validator->errors()
                ], 400);
            }
    
            
            $client->name = $request->name;
            $client->email = $request->email;
            $client->document = $request->document;

            if($this->user->clients()->save($client)){
                return response()->json([
                    'success' => true,
                    'message' => 'Client updated successfully',
                    'data' => $client
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the client could not be updated',
                ], 400);
            }  
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the client could not be updated',
                'errors' =>  $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        try {
            if ($client->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client deleted successfully',
                    'data' => $client
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the client could not be deleted',
                ], 400);
            }
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the client could not be deleted',
                'errors' =>  $ex->getMessage()
            ], 500);
        }
    }

    protected function guard(){
        return Auth::guard();
    }
}
