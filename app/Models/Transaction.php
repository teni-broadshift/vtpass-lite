<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable= [
        'type',
        'wallet_id',
        'user_id',
        'status',
        'request_id',
        'product_id',
        'service_id',
        'ref_no',
        'amount',
        'phone',
        'email',
        'meter_no',
        'smartcard_no',
        'variation_code'
    ];
}
