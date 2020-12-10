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
        'key',
        'created_by',
        'client_id'
    ];

}
