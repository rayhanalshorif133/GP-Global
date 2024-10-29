<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeAndBuy extends Model
{
    use HasFactory;

    protected $table = 'recharge_and_buy';

    protected $fillable = [
        'msisdn',
        'acr',
        'keyword',
        'hit_url',
        'recharge_amount',
        'originalReferenceCode',
        'referenceCode',
        'transaction_date',
        'status',
        'recharge_status',
        'payload',
        'response'
    ];
    
}
