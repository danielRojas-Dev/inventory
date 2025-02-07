<?php

namespace App\Helpers;

use App\Models\Order;
use App\Models\OrderquotasDetails;
use App\Models\Loan;
use App\Models\LoanDetail;

class InvoiceHelper
{
    public static function generateInvoiceNo()
    {
        // Obtener el último invoice_no de todas las tablas relevantes
        $lastOrderInvoice = Order::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        $lastQuotaInvoice = OrderquotasDetails::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        $lastLoanInvoice = Loan::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        $lastLoanDetailInvoice = LoanDetail::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        // Determinar cuál es el último invoice_no de todas las tablas
        $lastInvoice = max($lastOrderInvoice, $lastQuotaInvoice, $lastLoanInvoice, $lastLoanDetailInvoice);

        // Extraer el número y generar el siguiente
        if ($lastInvoice) {
            preg_match('/INV-(\d+)/', $lastInvoice, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1; // Si no hay registros, iniciamos en 1
        }

        // Formatear el nuevo invoice_no
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}