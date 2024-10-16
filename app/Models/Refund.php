<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;
    
    protected $table = 'refunds';

    protected $fillable = [
        'acr_key',
        'consentId',
        'referenceCode',
        'service_keyword',
        'sent_response',
        'get_response',
        'status',
    ];
}
