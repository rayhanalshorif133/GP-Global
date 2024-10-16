<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renew extends Model
{
    use HasFactory;

    protected $fillable = [
        'acr_key',
        'referenceCode',
        'service_keyword',
        'subscription',
        'consentId',
        'response',
    ];
}
