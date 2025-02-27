<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderQuotasDetails extends Model
{
    use HasFactory;

    protected $table = 'orders_quotas_details';

    protected $fillable = [
        'order_id',
        'number_quota',
        'estimated_payment',
        'invoice_no',
        'interest_due',
        'increment_due',
        'total_payment',
        'estimated_payment_date',
        'status_payment',
        'cancelated',
        'payment_method',
        'payment_date',
        'payment_currency',
        'amount_difference',
    ];

    public function getQuotaDateFormattedAttribute()
    {
        return $this->payment_date instanceof Carbon
            ? $this->payment_date->format('d/m/Y H:i')
            : null;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
