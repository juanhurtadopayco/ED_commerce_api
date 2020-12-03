<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 
        'email', 
        'document'
    ];

    public function invoices(){
        return $this->hasMany(Invoice::class, 'client_id', 'id');
    }

}
