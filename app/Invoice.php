<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'discount',
        'subtotal', 
        'total', 
        'created_by',
        'client_id'
    ];

    public function client(){
        
    }
}
