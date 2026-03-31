<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'companies';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone_number',
        'company_landline_number',
        'company_address',
        'status',
        'is_deleted',
        'service_id'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Services::class, 'company_services', 'company_id', 'service_id')
                    ->withPivot('buy_rate', 'sell_rate', 'descriptive_id')
                    ->withTimestamps();
    }
    
}
