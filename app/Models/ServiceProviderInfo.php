<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderInfo extends Model
{
    use HasFactory;

    protected $table = 'service_provider_infos';

    protected $fillable = [
        'username',
        'password',
        'url',
        'operatorId',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];



}
