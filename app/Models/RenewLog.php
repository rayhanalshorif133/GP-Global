<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'acr_key',
        'status_code',
        'status',
        'serverReferenceCode',
        'resourceURL',
        'transactionOperationStatus',
        'totalAmountCharged',
        'amount',
        'description',
        'referenceCode',
        'currency',
        'purchaseCategoryCode',
        'service_keyword',
        'operatorId',
        'subscription',
        'consentId',
        'payload',
        'response',
        'msisdn',
        'keyword',
        'created',
        'updated',
    ];
}
