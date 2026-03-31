<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Descriptive extends Model
{
    use HasFactory;

    protected $table = 'descriptive';

    protected $fillable = [
        'description_name',
        'replace_with',
        'status',
    ];
    
    public function companyServices()
    {
        return $this->belongsToMany(CompanyServices::class, 'company_services', 'descriptive_id');
    }
    
}
