<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUnSubLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'keyword',
        'status',
        'opt_date',
        'opt_time',
    ];
}
