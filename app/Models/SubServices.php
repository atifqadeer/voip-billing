<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubServices extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'status',
        'added_date',
        'added_time',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class);
    }
}
