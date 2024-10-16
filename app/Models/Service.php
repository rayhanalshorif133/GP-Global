<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';


    protected $fillable = [
        'name',
        'keyword',
        'type',
        'portal_link',
        'redirect_url',
        'amount',
        'productId',
        'description',
        'notification_url',
        'renewal_notification_api',
        'validity',
        'reference_code',
        'channel',
    ];
}
