<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
    use HasFactory;

    protected $table = 'billing_details';
    protected $fillable = [
        'bill_id',
        'to_number',
        'from_cli',
        'simplified_to_descriptive',
        'total_duration',
        'total_amount',
        'currency'
    ];

    public function billing(){
        return $this->belongsTo(Billing::class, 'bill_id', 'id');
    }
}
