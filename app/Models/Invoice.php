<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Customer;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'customer_id',
        'customer_name',
        'total_amount',
        'tax',
        'discount',
        'payable_amount',
        'payment_method',
        'status',
        'tendered_amount',
        'buyer_pntn',
        'buyer_cnic',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function fbrLog()
    {
        return $this->hasOne(FbrLog::class);
    }

    public function getCustomerNameAttribute($value)
    {
        if (empty($value)) return $value;
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setCustomerNameAttribute($value)
    {
        $this->attributes['customer_name'] = empty($value) ? $value : \Illuminate\Support\Facades\Crypt::encryptString($value);
    }
}
