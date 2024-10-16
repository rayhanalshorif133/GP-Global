<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnDemandChargeLog extends Model
{
    use HasFactory;

    protected $table = 'on_demand_charges_log';

    protected $fillable = [
        'msisdn',
        'acr',
        'pin_create_payload',
        'description',
        'otp',
        'pin_create_response',
        'acr_create_response',
        'acr_create_messageId',
        'acr_create_failed_text',
        'op_date_time',
    ];
}
