<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Loan extends Model
{
    use HasFactory, Sortable;
    protected $fillable = [
        'customer_id',
        'loan_date',
        'loan_status',
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
        'loan_date',
        'pay',
        'total',
    ];

    protected $guarded = [
        'id',
    ];

    public function getLoanDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function loanDetails()
    {
        return $this->hasMany(LoanDetail::class, 'loan_id', 'id');
    }


    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'loan_id', 'id');
    }

    public function getCantidadDeudasAttribute()
    {
        $loanId = $this::where('customer_id', '=', $this->customer_id)->value('id');


        $debeCuotas = LoanDetail::where('loan_id', $loanId)
            ->where('estimated_payment_date', '<', date('Y-m-d'))
            ->where('status_payment', '!=', 'Pagado')
            ->count();


        return $debeCuotas;
    }
}