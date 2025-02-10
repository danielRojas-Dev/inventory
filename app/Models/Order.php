<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_status',
        'total_products',
        'invoice_no',
        'total',
        'payment_method',
        'pay',
        'quotas',
        'interest_plan',
        'employee_id',
    ];

    public $sortable = [
        'customer_id',
        'order_date',
        'pay',
        'total',
    ];

    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'order_date' => 'datetime', // Laravel convierte automáticamente en Carbon
    ];

    public function getOrderDateFormattedAttribute()
    {
        return $this->order_date instanceof Carbon
            ? $this->order_date->format('d/m/Y H:i')
            : null;
    }

    public function getOrderDateReceiptFormattedAttribute()
    {
        return $this->order_date instanceof Carbon
            ? $this->order_date->format('d-m-Y')
            : null;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'order_id', 'id');
    }

    public function orderquotaDetails()
    {
        return $this->hasMany(OrderquotasDetails::class, 'order_id', 'id');
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'order_id', 'id');
    }

    public function getCantidadDeudasAttribute()
    {
        $orderId = $this::where('customer_id', '=', $this->customer_id)->value('id');


        $debeCuotas = OrderquotasDetails::where('order_id', $orderId)
            ->where('estimated_payment_date', '<', date('Y-m-d'))
            ->where('status_payment', '!=', 'Pagado')
            ->count();


        return $debeCuotas;
    }
}