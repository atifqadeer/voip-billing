<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';
    protected $fillable = [
        'year',
        'month',
        'client_id',
        'total_payment',
        'payment_status',
        'total_duration',
        'currency',
        'pdf_file_name',
        'uuid'
    ];

    public function client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }
    
    public function billing_details(){
        return $this->hasMany(BillingDetail::class,'bill_id','id');
    }
   
    public function additional_billing_details(){
        return $this->hasMany(AdditionalBillingDetail::class,'bill_id','id');
    }
    
    public function tax_billing_details(){
        return $this->hasMany(TaxBillingDetail::class,'bill_id','id');
    }
   
    public function client_fixedLineService_bill_details(){
        return $this->hasMany(clientFixedLineServicesBillDetails::class,'bill_id','id');
    }
}
