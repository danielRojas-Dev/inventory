<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'number_quota',
        'estimated_payment',
        'invoice_no',
        'interest_due',
        'increment_due',
        'total_payment',
        'cancelated',
        'estimated_payment_date',
        'status_payment',
        'payment_method',
        'payment_date',
        'payment_currency',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
}
