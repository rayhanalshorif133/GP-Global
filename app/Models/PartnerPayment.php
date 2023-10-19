<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'acr_key',
        'referenceCode',
        'service_keyword',
        'mandateId_subscription_num',
        'consentId',
        'response',
    ];
}
