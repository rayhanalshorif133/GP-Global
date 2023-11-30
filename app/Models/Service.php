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
        'api_url',
        'redirect_url',
        'amount',
        'description',
        'validity',
        'reference_code',
        'channel',
    ];
}
