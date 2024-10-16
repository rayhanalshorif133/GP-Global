<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvalidAcrs extends Model
{
    use HasFactory;

    protected $fillable = [
        'acr_key',
        'response'
    ];
}
