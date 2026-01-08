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

        // Límite para considerar cuotas vencidas: más de 1 mes antes de hoy
        $overdueLimitDate = Carbon::now()->subMonth()->toDateString();

        $salesOverdueQuotas = OrderQuotasDetails::with(['order.customer', 'order.orderDetails.product'])
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('employee_id', $userId);
            })
            ->where('estimated_payment_date', '<', $overdueLimitDate)
            ->where('status_payment', '!=', 'Pagado')
            ->orderBy('estimated_payment_date')
            ->get();

        $loanOverdueQuotas = LoanDetail::with(['loan.customer'])
            ->whereHas('loan', function ($q) use ($userId) {
                $q->where('employee_id', $userId);
            })
            ->where('estimated_payment_date', '<', $overdueLimitDate)
            ->where('status_payment', '!=', 'Pagado')
            ->orderBy('estimated_payment_date')
            ->get();

        // Agrupar por cliente para obtener resumen de deudores de cuotas de ventas
        $salesDebtors = $salesOverdueQuotas
            ->groupBy(function ($quota) {
                return optional(optional($quota->order)->customer)->id;
            })
            ->filter(function ($group, $customerId) {
                return !is_null($customerId);
            })
            ->map(function ($group) {
                $firstQuota = $group->first();
                $order = $firstQuota->order;
                $customer = $order->customer;
                $firstDetail = $order->orderDetails[0] ?? null;
                $productName = $firstDetail && $firstDetail->product ? $firstDetail->product->product_name : null;

                return [
                    'customer' => $customer,
                    'product_name' => $productName,
                    // Cuotas vencidas (cantidad de cuotas atrasadas)
                    'quotas_count' => $group->count(),
                    // Datos del plan de venta original
                    'plan_quotas' => $order->quotas,
                    'interest_plan' => $order->interest_plan,
                    'total_estimated' => $group->sum('estimated_payment'),
                ];
            })
            ->values();

        // Agrupar por cliente para obtener resumen de deudores de cuotas de préstamos
        $loanDebtors = $loanOverdueQuotas
            ->groupBy(function ($quota) {
                return optional(optional($quota->loan)->customer)->id;
            })
            ->filter(function ($group, $customerId) {
                return !is_null($customerId);
            })
            ->map(function ($group) {
                $loan = $group->first()->loan;
                $customer = $loan->customer;

                return [
                    'customer' => $customer,
                    // Cuotas vencidas (cantidad de cuotas atrasadas)
                    'quotas_count' => $group->count(),
                    // Datos del préstamo original
                    'loan_total' => $loan->total,
                    'plan_quotas' => $loan->quotas,
                    'interest_plan' => $loan->interest_plan,
                    'total_estimated' => $group->sum('estimated_payment'),
                ];
            })
            ->values();

        return view('dashboard.index', [
            'salesQuotas' => $salesQuotas,
            'loanQuotas' => $loanQuotas,
            'salesDebtors' => $salesDebtors,
            'loanDebtors' => $loanDebtors,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
        ]);
    }
}
