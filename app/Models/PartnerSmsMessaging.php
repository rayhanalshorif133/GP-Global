<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSmsMessaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'senderNumber',
        'keyword',
        'acr_key',
        'senderName',
        'messageType',
        'message',
        'payload',
        'response',
        'status'
    ];

}
