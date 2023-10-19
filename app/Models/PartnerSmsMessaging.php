<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSmsMessaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'senderNumber',
        'service_keyword',
        'acr_key',
        'senderName',
        'messageType',
        'message',
        'response'
    ];
}
