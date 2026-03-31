<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInhouseServiceUsage extends Model
{
    use HasFactory;

    protected $table = 'client_inhouse_service_usage';
    protected $fillable = [
        'client_id',
        'additional_service_id',
        'rate',
        'description',
        'quantity',
        'start_from',
        'end_to'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}
