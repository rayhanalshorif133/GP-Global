<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'acr',
        'tid',
        'status',
        'keyword',
        'subs_date',
        'unsubs_date'
    ];

}
