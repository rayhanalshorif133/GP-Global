<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'rmsisdn',
        'opr',
        'channel',
        'status',
        'service',
        'sub_service',
        'subs_date',
        'unsubs_date',
        'charge_status',
        'charge_date',
        'shortcode',
        'entry',
        'last_update',
        'in_msg_id',
        'zoneid',
        'flag',
        'tid',
    ];
}
