<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentBackLog extends Model
{
    use HasFactory;


    protected $fillable = [
        'service_id','type',	'customer_reference',	'consentId',	'response'	
    ];
    
}
