<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderquotasDetails extends Model
{
    use HasFactory;

    protected $table = 'orders_quotas_details';

    protected $fillable = [
        'order_id',
        'number_quota',
        'estimated_payment',
        'invoice_no',
        'interest_plan',
        'interest_due',
        'increment_due',
        'total_payment',
        'estimated_payment_date',
        'status_payment',
        'payment_method',
        'payment_date',
        'payment_currency',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}