<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyServices extends Model
{
    use HasFactory;
    protected $table = 'company_services';
    protected $fillable = [
        'company_id',
        'service_id',
        'buy_rate',
        'sell_rate',
        'descriptive_id'
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'id');
    }
    
    public function services()
    {
        return $this->belongsToMany(Services::class, 'id');
    }

}
