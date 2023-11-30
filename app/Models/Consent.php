<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;


    protected $fillable = [
        'service_id',
        'amount',
        'msisdn',
        'currency',
        'subscriptionPeriod',
        'urls',
        'customer_reference',
        'consentId',
        'response',
        'is_subscription',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
