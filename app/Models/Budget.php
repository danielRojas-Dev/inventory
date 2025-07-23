<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Budget extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'customer_id',
        'budget_date',
        'budget_status',
        'total_products',
        'budget_no',
        'total',
        'payment_method',
        'quotas',
        'interest_plan',
        'valid_until',
        'employee_id',
        'converted_order_id',
        'converted_at',
        'notes',
    ];

    public $sortable = [
        'customer_id',
        'budget_date',
        'budget_status',
        'total',
        'valid_until',
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'budget_date' => 'datetime',
        'valid_until' => 'date',
        'converted_at' => 'datetime',
    ];

    public function getBudgetDateFormattedAttribute()
    {
        return $this->budget_date instanceof Carbon
            ? $this->budget_date->format('d/m/Y H:i')
            : null;
    }

    public function getBudgetDateReceiptFormattedAttribute()
    {
        return $this->budget_date instanceof Carbon
            ? $this->budget_date->format('d-m-Y')
            : null;
    }

    public function getValidUntilFormattedAttribute()
    {
        return $this->valid_until instanceof Carbon
            ? $this->valid_until->format('d/m/Y')
            : null;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function budgetDetails()
    {
        return $this->hasMany(BudgetDetail::class, 'budget_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function convertedOrder()
    {
        return $this->belongsTo(Order::class, 'converted_order_id', 'id');
    }

    // Verificar si el presupuesto está vencido
    public function getIsExpiredAttribute()
    {
        return $this->valid_until && $this->valid_until->isPast() && $this->budget_status === 'Pendiente';
    }

    // Verificar si se puede convertir a venta
    public function getCanConvertAttribute()
    {
        return $this->budget_status === 'Pendiente' && !$this->is_expired;
    }

    // Scope para filtrar presupuestos
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query->where('budget_no', 'like', '%' . $search . '%')
                ->orWhereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
        });

        $query->when($filters['status'] ?? false, function ($query, $status) {
            return $query->where('budget_status', $status);
        });
    }
}
