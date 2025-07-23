<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\InvoiceHelper;
use App\Models\Budget;
use App\Models\BudgetDetail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderQuotasDetails;
use App\Models\Product;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class BudgetController extends Controller
{
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        return view('budgets.index', [
            'budgets' => Budget::with(['customer', 'employee'])
                ->filter(request(['search', 'status']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function show(Budget $budget)
    {
        $budget->load(['budgetDetails.product', 'customer']);

        return view('budgets.show', [
            'budget' => $budget,
        ]);
    }

    public function createBudget(Request $request)
    {
        try {
            $rules = [
                'customer_id' => 'required|numeric',
                'payment_method' => 'required|string',
                'quotas' => 'sometimes|nullable|integer|min:1',
                'interest_rate' => 'sometimes|nullable|numeric|min:0',
                'estimated_payment_date' => 'sometimes|nullable',
                'entrega' => 'sometimes|nullable|numeric',
                'payment_month' => 'sometimes|nullable|string',
                'valid_days' => 'sometimes|nullable|integer|min:1|max:90',
                'notes' => 'sometimes|nullable|string|max:500',
            ];

            $validatedData = $request->validate($rules);

            $customer = Customer::find($validatedData['customer_id']);
            $content = Cart::content();
            $totalOriginal = Cart::total();

            $quotas = $validatedData['quotas'] ?? null;
            $interestRate = $validatedData['interest_rate'] ?? 0;

            $totalConInteres = $totalOriginal;

            if ($quotas && $interestRate > 0) {
                $totalConInteres *= (1 + ($interestRate / 100));
                $montoCuota = $totalConInteres / $quotas;
            } else {
                $montoCuota = $quotas ? ($totalOriginal / $quotas) : 0;
            }

            // Calcular fecha de validez (por defecto 30 días)
            $validDays = $validatedData['valid_days'] ?? 30;
            $validUntil = Carbon::now()->addDays($validDays);

            return view('budgets.create-budget', [
                'customer' => $customer,
                'content' => $content,
                'payment_method' => $validatedData['payment_method'],
                'quotas' => $quotas,
                'interest_rate' => $interestRate,
                'estimated_payment_date' => $validatedData['estimated_payment_date'] ?? null,
                'total_original' => $totalOriginal,
                'total_con_interes' => $totalConInteres,
                'monto_cuota' => $montoCuota,
                'entrega' => $validatedData['entrega'] ?? null,
                'payment_month' => $validatedData['payment_month'] ?? null,
                'valid_until' => $validUntil,
                'notes' => $validatedData['notes'] ?? null,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrió un error al crear el presupuesto.');
        }
    }

    public function storeBudget(Request $request)
    {
        try {
            $rules = [
                'customer_id' => 'required|numeric',
                'total' => 'required|numeric',
                'payment_method' => 'required|string',
                'quotas' => 'sometimes|nullable|integer|min:1',
                'interest_rate' => 'sometimes|nullable|numeric|min:0',
                'valid_until' => 'required|date',
                'notes' => 'sometimes|nullable|string',
            ];

            $validatedData = $request->validate($rules);

            // Generar número de presupuesto
            $budget_no = IdGenerator::generate([
                'table' => 'budgets',
                'field' => 'budget_no',
                'length' => 10,
                'prefix' => 'PRES-'
            ]);

            DB::beginTransaction();

            // Crear el presupuesto
            $budgetData = [
                'customer_id' => $validatedData['customer_id'],
                'budget_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'budget_status' => 'Pendiente',
                'total_products' => Cart::count(),
                'budget_no' => $budget_no,
                'total' => $validatedData['total'],
                'payment_method' => $validatedData['payment_method'],
                'quotas' => $validatedData['quotas'],
                'interest_plan' => $validatedData['interest_rate'] ?? 0,
                'valid_until' => $validatedData['valid_until'],
                'employee_id' => auth()->id(),
                'notes' => $validatedData['notes'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $budget_id = Budget::insertGetId($budgetData);

            // Crear los detalles del presupuesto
            $contents = Cart::content();
            $budgetDetails = [];

            foreach ($contents as $content) {
                $budgetDetails[] = [
                    'budget_id' => $budget_id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->total,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // Insertar todos los detalles de productos en la tabla 'budget_details'
            BudgetDetail::insert($budgetDetails);

            DB::commit();

            // Vaciar el carrito
            Cart::destroy();

            return redirect()->route('budgets.index')->with('success', "¡Presupuesto creado con éxito! Número de presupuesto: $budget_no");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el presupuesto.');
        }
    }

    public function convertToSale(Budget $budget)
    {
        // Verificar si se puede convertir
        if (!$budget->can_convert) {
            return redirect()->back()->with('error', 'Este presupuesto no puede ser convertido a venta.');
        }

        try {
            DB::beginTransaction();

            // Verificar stock antes de convertir
            foreach ($budget->budgetDetails as $detail) {
                $product = Product::find($detail->product_id);
                if ($product->product_store < $detail->quantity) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Stock insuficiente para el producto: {$product->product_name}. Disponible: {$product->product_store}, Requerido: {$detail->quantity}");
                }
            }

            // Generar número de factura para la orden
            $invoice_no = InvoiceHelper::generateInvoiceNo();

            // Crear la orden
            $orderData = [
                'customer_id' => $budget->customer_id,
                'payment_method' => $budget->payment_method,
                'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'order_status' => $budget->payment_method === 'CUOTAS' ? 'Pendiente' : 'Pagado',
                'total_products' => $budget->total_products,
                'invoice_no' => $invoice_no,
                'total' => $budget->total,
                'pay' => $budget->payment_method === 'CUOTAS' ? 0 : $budget->total,
                'quotas' => $budget->quotas,
                'interest_plan' => $budget->interest_plan,
                'employee_id' => auth()->id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            $order_id = Order::insertGetId($orderData);

            // Crear los detalles de la orden
            $orderDetails = [];
            foreach ($budget->budgetDetails as $budgetDetail) {
                $orderDetails[] = [
                    'order_id' => $order_id,
                    'product_id' => $budgetDetail->product_id,
                    'quantity' => $budgetDetail->quantity,
                    'unitcost' => $budgetDetail->unitcost,
                    'total' => $budgetDetail->total,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            OrderDetails::insert($orderDetails);

            // Si es pago en cuotas, crear las cuotas
            if ($budget->payment_method === 'CUOTAS' && $budget->quotas) {
                $montoCuota = $budget->total / $budget->quotas;
                $quotaDetails = [];

                for ($i = 1; $i <= $budget->quotas; $i++) {
                    $quotaDetails[] = [
                        'order_id' => $order_id,
                        'number_quota' => $i,
                        'estimated_payment' => round($montoCuota),
                        'total_payment' => null,
                        'estimated_payment_date' => Carbon::now()->addMonths($i)->format('Y-m-d'),
                        'status_payment' => 'Pendiente',
                        'invoice_no' => null,
                        'payment_method' => null,
                        'payment_currency' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }

                OrderQuotasDetails::insert($quotaDetails);
            }

            // Actualizar el stock de los productos
            foreach ($budget->budgetDetails as $budgetDetail) {
                Product::where('id', $budgetDetail->product_id)->decrement('product_store', $budgetDetail->quantity);
            }

            // Actualizar el presupuesto como convertido
            $budget->update([
                'budget_status' => 'Convertido',
                'converted_order_id' => $order_id,
                'converted_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('budgets.index')->with('success', "¡Presupuesto convertido a venta exitosamente! Número de orden: $invoice_no");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al convertir el presupuesto a venta.');
        }
    }

    public function cancel(Budget $budget)
    {
        if ($budget->budget_status !== 'Pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden cancelar presupuestos pendientes.');
        }

        $budget->update(['budget_status' => 'Cancelado']);

        return redirect()->back()->with('success', 'Presupuesto cancelado exitosamente.');
    }

    public function printBudget(Budget $budget)
    {
        $budget->load(['budgetDetails.product', 'customer']);

        // Variables exactamente como en downloadReceiptVentaNormal
        $cliente = $budget->customer;
        $details = $budget->budgetDetails;

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/png;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/png;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdf = Pdf::loadView('budgets.print-budget', compact('budget', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'details'))
            ->setPaper('cart', 'vertical');

        return $pdf->stream('presupuesto.pdf', array('Attachment' => 0));
    }
}
