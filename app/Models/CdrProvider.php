<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdrProvider extends Model
{
    use HasFactory;

    protected $table = 'cdr_providers';
    protected $fillable = [
        'name',
        'status'
    ];
}
