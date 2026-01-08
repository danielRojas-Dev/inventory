<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanDetail;
use App\Models\OrderQuotasDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $userId = Auth::id();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $salesQuotas = OrderQuotasDetails::with(['order.customer', 'order.orderDetails.product'])
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('employee_id', $userId);
            })
            ->whereBetween('estimated_payment_date', [$startOfMonth, $endOfMonth])
            ->where('status_payment', '!=', 'Pagado')
            ->orderBy('estimated_payment_date')
            ->get();

        $loanQuotas = LoanDetail::with(['loan.customer'])
            ->whereHas('loan', function ($q) use ($userId) {
                $q->where('employee_id', $userId);
            })
            ->whereBetween('estimated_payment_date', [$startOfMonth, $endOfMonth])
            ->where('status_payment', '!=', 'Pagado')
            ->orderBy('estimated_payment_date')
            ->get();

        return view('dashboard.index', [
            'salesQuotas' => $salesQuotas,
            'loanQuotas' => $loanQuotas,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
        ]);
    }
}