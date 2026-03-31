<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalService extends Model
{
    use HasFactory;

    protected $table = 'additional_services';
    protected $fillable = [
        'title',
        'description',
        'rate',
        'frequency',
        'status',
        'user_id',
        'currency'
    ];


}
