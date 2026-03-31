<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientFixedLineServicesBillDetails extends Model
{
    use HasFactory;

    protected $table = 'client_fixed_line_services_bill_details';
    protected $fillable = [
        'client_id',
        'bill_id',
        'to_number',
        'from_cli',
        'simplified_to_descriptive',
        'total_duration',
        'total_amount',
        'currency'
    ];
}
