<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'consent_id',
        'customer_reference',
        'consentId',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
