<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCDRHistory extends Model
{
    use HasFactory;
    
    protected $table = 'client_cdr_history';
    protected $fillable = [
        'bill_id',
        'reference',
        'client_id',
        'cdr_id',
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

    public function cdr()
    {
        return $this->belongsTo(Cdr::class, 'cdr_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
