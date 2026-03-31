<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalBillingDetail extends Model
{
    use HasFactory;
    protected $table = 'additional_billing_details';
    protected $fillable = [
        'bill_id',
        'additional_service_id',
        'rate',
        'frequency',
        'description',
        'currency',
        'quantity',
        'total',
        'start_from',
        'end_to'
    ];

    public function additionalService()
    {
        return $this->belongsTo(AdditionalService::class, 'additional_service_id', 'id');
    }
}
