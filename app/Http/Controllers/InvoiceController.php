<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Client;
use App\Invoice;
use App\Mail\InvoiceCreated;
use Exception;
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
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|string', 
                'description' => 'required|string', 
                'discount' => 'required|numeric',
                'subtotal' =>  'required|numeric', 
                'total' =>  'required|numeric', 
                'client_id' =>  'required|numeric', 
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong request',
                    $validator->errors()
                ], 400);
            }
            $client = Client::find($request->client_id);
            if ($client) {
                
                $keyToOpen = time();

                $invoice = new Invoice;
                $invoice->name = $request->name;
                $invoice->description = $request->description;
                $invoice->discount = $request->discount;
                $invoice->subtotal =  $request->subtotal;
                $invoice->total =  $request->total;
                $invoice->client_id =  $request->client_id;
                $invoice->key = base64_encode($keyToOpen);    
                
                if($this->user->invoices()->save($invoice)){

                    $notification = [
                        "client_name" => $client->name,
                        "invoice_name" => $invoice->name,
                        "invoice_description" => $invoice->description,
                        "invoice_total" =>  $request->total,
                        "invoice_key_open" => $invoice->key,
                        "url_payment" => env("APP_URL_FRONT") . "/invoice/$invoice->key"
                    ];

                    Mail::to($client->email)->send(new InvoiceCreated($notification));

                    //return (new InvoiceCreated($notification))->render();

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

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, the client associated not exists',
                ], 400);
            }    
            
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the invoice could not be saved',
                'errors' =>  $ex->getMessage()
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     *s
     * @return \Illuminate\Http\Response
     */
    public function show($invoice)
    {
        try {
            $invoice = Invoice::findOrFail($invoice);
            return response()->json([
                'success' => true,
                'message' => 'Invoice show successfully',
                'data' => $invoice
            ], 200);    
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the invoice could not be find',
                'errors' =>  $ex->getMessage()
            ], 400);
        }
         
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

    public function processPayment(Request $request)
    {
        try {
            
            //Consultar detalle de transaccion para aplicar el pago

        }catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, the invoice could not be saved',
                'errors' =>  $ex->getMessage()
            ], 500);
        }
    }

    protected function guard(){
        return Auth::guard();
    }
}
