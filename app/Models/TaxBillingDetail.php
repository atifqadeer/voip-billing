<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxBillingDetail extends Model
{
    use HasFactory;
    protected $table = 'tax_billing_details';
    protected $fillable = [
        'bill_id',
        'tax_type',
        'tax_id',
        'tax_name',
        'tax_amount',
        'tax_rate',
        'currency'
    ];
}
