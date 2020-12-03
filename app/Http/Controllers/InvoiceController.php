<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
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
        $invoices = $this->user->invoices()->get([
            'id',
            'name', 
            'description', 
            'discount',
            'subtotal', 
            'total', 
            'created_by',
            'client_id'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Get invoices successfully',
            'data' => $invoices
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
        $validator = Validator::make($request->all(),[
            'name' => 'required|string', 
            'description' => 'required|string', 
            'discount' => 'required|numeric',
            'subtotal' =>  'required|numeric', 
            'total' =>  'required|numeric', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong request',
                $validator->errors()
            ], 400);
        }

        $invoice = new Invoice;

        $invoice->name = $request->name;
        $invoice->description = $request->description;
        $invoice->discount = $request->discount;
        $invoice->subtotal =  $request->subtotal;
        $invoice->total =  $request->total;
        $invoice->client_id =  $request->client_id;

        if($this->user->invoices()->save($invoice)){
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Oops, the invoice could not be saved',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return response()->json([
            'success' => true,
            'message' => 'Invoice show successfully',
            'data' => $invoice
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string', 
            'description' => 'required|string', 
            'discount' => 'required|numeric',
            'subtotal' =>  'required|numeric', 
            'total' =>  'required|numeric', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong request',
                $validator->errors()
            ], 400);
        }

        if (!$invoice->paid) {
            $invoice->name = $request->name;
            $invoice->description = $request->description;
            $invoice->discount = $request->discount;
            $invoice->subtotal =  $request->subtotal;
            $invoice->total =  $request->total;
            $invoice->client_id =  $request->client_id;

            if($this->user->invoices()->save($invoice)){
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice updated successfully',
                    'data' => $invoice
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the invoice could not be updated',
                ], 400);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => "The invoice has a payment, cannot be updated" ,
            ], 400);
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if (!$invoice->paid) {
            if ($invoice->delete()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice deleted successfully',
                    'data' => $invoice
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the invoice could not be deleted',
                ], 400);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => "The invoice has a payment, cannot be deleted" ,
            ], 400);
        }  
    }

    protected function guard(){
        return Auth::guard();
    }
}
