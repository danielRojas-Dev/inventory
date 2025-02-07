<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\InvoiceHelper;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\OrderquotasDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{

    public function completeLoans()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        $customers = Customer::whereHas('loans')
            ->withCount('loans')
            ->sortable()
            ->paginate($row);


        return view('loans.complete-loans', [
            'customers' => $customers
        ]);
    }

    public function createLoan()
    {
        return view('loans.create', [
            'customers' => Customer::all()
        ]);
    }

    public function storeLoan(Request $request)
    {
        try {
            // Validación de los datos
            $rules = [
                'customer_id' => 'required|numeric',
                'total_loan' => 'required|numeric',
                'payment_method' => 'required|string',
                'quotas' => 'sometimes|nullable|integer|min:1',
                'interest_rate' => 'sometimes|nullable|numeric|min:0',
                'estimated_payment_date' => 'sometimes|nullable|string',
            ];

            // Generación del número de factura
            $invoice_no = InvoiceHelper::generateInvoiceNo();

            // Validación de los datos de entrada
            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            $totalOriginal = $validatedData['total_loan'];
            $interestRate = $validatedData['interest_rate'] ?? 0;
            $totalConInteres = $totalOriginal * (1 + ($interestRate / 100));
            $montoCuota = $totalConInteres / $validatedData['quotas'];

            $validatedData = array_merge($validatedData, ['pay' => 0]);

            $loanData = [
                'customer_id' => $validatedData['customer_id'],
                'payment_method' => $validatedData['payment_method'],
                'loan_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'loan_status' => 'Pendiente',
                'invoice_no' => $invoice_no,
                'quotas' => $validatedData['quotas'],
                'interest_plan' => $interestRate,
                'total' => $totalConInteres,
                'pay' => 0,
                'employee_id' => auth()->id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $loan_id = Loan::insertGetId($loanData);


            $quotaDetails = [];
            for ($i = 1; $i <= $validatedData['quotas']; $i++) {
                $quotaDetails[] = [
                    'loan_id' => $loan_id,
                    'number_quota' => $i,
                    'estimated_payment' => round($montoCuota),
                    'total_payment' => null,
                    'estimated_payment_date' => Carbon::now()->day($validatedData['estimated_payment_date'])->addMonths($i)->format('Y-m-d'),
                    'status_payment' => 'Pendiente',
                    'invoice_no' => null,
                    'payment_method' => null,
                    'payment_currency' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            LoanDetail::insert($quotaDetails);

            DB::commit();


            return redirect()->route('loan.completeLoans')->with('success', "Prestamo creado con éxito! <a href=" . route('order.downloadReceiptVenta', $loan_id) . " target='_blank'>Haga click aqui </a> para descargar el comprobante del Prestamo.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el prestamo.');
        }
    }

    public function downloadReceiptPrestamo(Loan $loan)
    {
        // Buscar si hay un attachment asociado al pedido
        $attachment = Attachment::where('loan_id', $loan->id)->first();


        if ($attachment) {
            // Obtener la ruta completa del archivo
            $filePath = storage_path('app/public/' . $attachment->path);

            // Verificar si el archivo realmente existe
            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }

        // Si no hay attachment, generar el PDF como antes
        $cliente = Customer::find($loan->customer_id);

        $valorCuota = LoanDetail::where('loan_id', $loan->id)->value('estimated_payment');
        $estimatedPaymentDate = LoanDetail::where('loan_id', $loan->id)->value('estimated_payment_date');

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdfFileName = 'Factura_Venta' . $loan->invoice_no . '_cliente_' . $cliente->name . '.pdf';

        $pdf = Pdf::loadView('loans.payment-receipt-loan', compact(
            'loan',
            'cliente',
            'htmlLogo',
            'htmlTitle',
            'valorCuota',
            'estimatedPaymentDate',
        ))->setPaper('cart', 'vertical');

        return $pdf->stream($pdfFileName, ['Attachment' => 0]);
    }

    public function downloadReceiptQuotaLoan(LoanDetail $quota)
    {
        $loan = Loan::where('id', $quota->loan_id)->first();
        $cliente = Customer::where('id', $loan->customer_id)->first();
        $valorCuota = LoanDetail::where('loan_id', $loan->id)->value('estimated_payment');

        // Cargar imágenes en base64
        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);
        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '" width="100" height="" />';

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '" width="300" height="" />';

        // Cargar imagen de cancelado si la cuota está cancelada
        $htmlCancelado = '';
        if ($quota->cancelated) {
            $pathCancelado = storage_path('app/public/cancelated/cancelado.png'); // Ruta correcta
            if (file_exists($pathCancelado)) {
                $cancelado = file_get_contents($pathCancelado);
                $htmlCancelado = '<img src="data:image/png;base64,' . base64_encode($cancelado) . '" width="150" height="" />';
            }
        }

        // Generar PDF
        $pdfFileName = 'Factura_Cuota' . $loan->invoice_no . '_cliente_' . $cliente->name . '.pdf';

        $pdf = Pdf::loadView('loans.payment-receipt-quota', compact('quota', 'loan', 'cliente', 'htmlLogo', 'htmlTitle', 'htmlCancelado', 'valorCuota'))
            ->setPaper('cart', 'vertical');

        return $pdf->stream($pdfFileName, ['Attachment' => 0]);
    }


    /**
     * Display the orders for a specific customer.
     */
    public function customerLoanDetails(Int $customer_id)
    {
        $loans = Loan::where('customer_id', $customer_id)
            ->with('loanDetails', 'customer', 'attachments')
            ->orderBy('loan_date', 'DESC')
            ->get();

        return view('loans.customer-loans', compact('loans'));
    }

    public function quotasLoan(Int $loan_id)
    {
        $loan = Loan::findOrFail($loan_id)->load('customer');
        $quotas = LoanDetail::where('loan_id', $loan_id)
            ->orderByRaw('CAST(number_quota AS UNSIGNED) DESC')
            ->get();

        return view('loans.quotas', [
            'loan' => $loan,
            'quotas' => $quotas,
        ]);
    }

    public function paymentQuotaLoan(LoanDetail $quota)
    {
        // Obtener la cuota con su orden relacionada
        $quota = LoanDetail::where('id', $quota->id)->with('loan')->first();

        // Calcular los días vencidos (si la fecha estimada de pago ya pasó)
        $today = \Carbon\Carbon::now();
        $estimatedDate = \Carbon\Carbon::parse($quota->estimated_payment_date);

        // Si la fecha estimada ya pasó, calcular los días de vencimiento
        $daysOverdue = $today->greaterThan($estimatedDate) ? $estimatedDate->diffInDays($today) : 0;

        return view('loans.payment-quota', compact('quota', 'daysOverdue'));
    }


    public function paymentLoan(Request $request)
    {
        try {
            $rules = [
                'quotaId' => 'required|numeric',
                'payment_method' => 'required|nullable|string',
                'currency' => 'required|nullable|string',
                'interest' => 'sometimes|nullable|string',
                'increment' => 'sometimes|nullable|string',
            ];

            // Generación del número de factura
            $invoice_no = InvoiceHelper::generateInvoiceNo();

            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            // Obtener la cuota
            $quota = LoanDetail::findOrFail($validatedData['quotaId']);

            // Calcular el total a pagar
            $increment = $validatedData['increment'] ?? 0;
            $totalPaid = $quota->estimated_payment + $increment;

            // Registrar el pago
            $quota->update([
                'payment_method' => $validatedData['payment_method'],
                'payment_currency' => $validatedData['currency'],
                'interest_due' => $validatedData['interest'] ?? null,
                'payment_date' => now(),
                'increment_due' => $increment,
                'total_payment' => round($totalPaid),
                'invoice_no' => $invoice_no,
                'status_payment' => 'Pagado',
                'updated_at' => now()
            ]);

            // Verificar si es la última cuota del préstamo
            $ultimaCuota = LoanDetail::where('loan_id', $quota->loan_id)
                ->orderByRaw("CAST(number_quota AS UNSIGNED) DESC")
                ->first();

            if ($ultimaCuota && $ultimaCuota->id == $quota->id) {
                // Si es la última cuota, marcarla como cancelada
                $quota->update(['cancelated' => true]);

                // Actualizar el estado del préstamo
                $loan = $quota->loan; // Asegúrate de que la relación `loan` está definida en el modelo
                if ($loan) {
                    $loan->update(['loan_status' => 'Cancelado']);
                }
            }

            DB::commit();

            return redirect()->route('loan.quotasLoan', $quota->loan_id)->with('success', 'Pago registrado correctamente.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function attachmentLoanCustomer(Request $request, Loan $loan)
    {
        try {
            $request->validate([
                'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10048',
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = $file->getClientOriginalName();
                $path = $file->storeAs('attachments/loans', $filename, 'public');

                // Eliminar archivo anterior si existe
                if ($loan->attachments->count()) {
                    Storage::disk('public')->delete($loan->attachments->first()->path);
                    $loan->attachments()->delete();
                }

                // Guardar nuevo archivo
                $loan->attachments()->create([
                    'path' => $path,
                    'filename' => $filename,
                ]);
            }

            return redirect()->back()->with('success', 'Comprobante subido correctamente.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error al subir el comprobante.');
        }
    }
}
