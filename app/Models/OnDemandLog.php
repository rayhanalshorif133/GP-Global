<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnDemandLog extends Model
{
    use HasFactory;

    protected $table = 'on_demand_logs';

    protected $fillable = [
        'acr_key',
        'msisdn',
        'tid',
        'amount',
        'keyword',
        'consentId',
        'opt_date',
        'opt_time',
    ];

}
