<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inclusive extends Model
{
    use HasFactory;

    protected $table = 'inclusives';
    protected $fillable = [
        'inhouse_service_id',
        'skip_to',
        'status',
    ];

    public function inhouseService()
    {
        return $this->belongsTo(AdditionalService::class, 'inhouse_service_id', 'id');
    }
    
}
