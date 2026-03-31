<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'clients';
    protected $fillable = [
        'account_number',
        'user_id',
        'client_name',
        'client_email',
        'client_phone_number',
        'client_outgoing_number',
        'client_incoming_number',
        'client_address',
        'status',
        'added_date',
        'added_time',
        'is_deleted',
        'tag_name',
        'notes',
        'frequency',
        'trunk_number',
        'is_enable_vat_tax',
        'is_enable_fixed_line_services',
        'fixed_line_service_number'
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'client_service_usage', 'client_id', 'company_id');
    }
    
    public function services()
    {
        return $this->belongsToMany(Services::class, 'client_service_usage', 'client_id', 'service_id');
    }

    public function clientInHouseServices()
    {
        return $this->hasMany(ClientInhouseServiceUsage::class);
    }
    
    public function clientServiceUsages()
    {
        return $this->hasMany(ClientServiceUsage::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class, 'client_id', 'id');
    }

}
