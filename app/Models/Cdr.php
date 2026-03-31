<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    use HasFactory;
    protected $table = 'cdrs';

    protected $fillable = [
        'reference',
        'client_id',
        'trunk',
        'tag',
        'date',
        'time',
        'from_cli',
        'from_descriptive',
        'to_number',
        'to_descriptive',
        'destination_id',
        'duration_seconds',
        'billable_duration_seconds',
        'peak_duration',
        'off_peak_duration',
        'weekend_duration',
        'peak_rate',
        'off_peak_rate',
        'weekend_rate',
        'connection_rate',
        'total_charge',
        'currency',
        'simplified_to_descriptive',
        'bill_amount',
        'calculated_duration',
        'provider_id'
    ];

    public function cdr_providers()
    {
        return $this->belongsTo(CdrProvider::class, 'provider_id');
    }
}
