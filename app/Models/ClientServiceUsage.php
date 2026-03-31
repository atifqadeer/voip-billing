<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientServiceUsage extends Model
{
    use HasFactory;

    protected $table = 'client_service_usage';
    protected $fillable = [
        'client_id',
        'company_id',
        'service_id',
        'sub_service_id',
        'fixed_rate',
        'percentage',
        'charges_description'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
