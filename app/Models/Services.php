<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'status',
        'added_date',
        'added_time',
        'user_id','description'
    ];
    // public function companies()
    // {
    //     return $this->belongsToMany(Company::class, 'company_service');
    // }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_services', 'service_id', 'company_id')
                    ->withPivot('descriptive_id', 'buy_rate', 'sell_rate');
    }


}
