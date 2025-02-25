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
use App\Models\OrderQuotasDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function completeOrders()
    {
        // Obtener todos los clientes con órdenes
        $customers = Customer::whereHas('orders') // Filtra solo clientes con órdenes
            ->withCount('orders') // Cuenta las órdenes de cada cliente
            ->get(); // Obtiene todos los clientes sin paginación

        // Retorna la vista con todos los clientes
        return view('orders.complete-orders', [
            'customers' => $customers,
        ]);
    }


    public function stockManage()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        return view('stock.index', [
            'products' => Product::with(['category', 'supplier'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function storeOrder(Request $request)
    {
        try {
            // Validación de los datos
            $rules = [
                'customer_id' => 'required|numeric',
                'payment_method' => 'required|string',
                'quotas' => 'sometimes|nullable|integer|min:1',
                'estimated_payment_date' => 'sometimes|nullable|string',
                'interest_rate' => 'sometimes|nullable|numeric|min:0',
                'entrega' => 'sometimes|nullable|numeric|min:0',
            ];

            // Generación del número de factura
            $invoice_no = InvoiceHelper::generateInvoiceNo();

            // Validación de los datos de entrada
            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            if ($validatedData['payment_method'] == 'CUOTAS') {
                // Calcular el total con el interés usando el interest_rate proporcionado
                $totalOriginal = Cart::total();
                $interestRate = $validatedData['interest_rate'] ?? 0; // Valor por defecto 0 si no se proporciona
                $totalConInteres = $totalOriginal * (1 + ($interestRate / 100));

                $entrega = $validatedData['entrega'] ?? 0; // Si no se proporciona, es 0
                $montoCuota = $totalConInteres / $validatedData['quotas'];

                $validatedData = array_merge($validatedData, ['pay' => 0]);

                // Asignación de datos adicionales
                $orderData = [
                    'customer_id' => $validatedData['customer_id'],
                    'payment_method' => $validatedData['payment_method'],
                    'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'order_status' => 'Pendiente',
                    'total_products' => Cart::count(),
                    'invoice_no' => $invoice_no,
                    'quotas' => $validatedData['quotas'],
                    'interest_plan' => $interestRate,
                    'total' => $totalConInteres,
                    'pay' => 0,
                    'employee_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $order_id = Order::insertGetId($orderData);

                // Crear los registros en OrderQuotasDetails
                $quotaDetails = [];
                for ($i = 1; $i <= $validatedData['quotas']; $i++) {
                    $montoFinal = ($i == 1 && $entrega > 0) ? $entrega : round($montoCuota);

                    $quotaDetails[] = [
                        'order_id' => $order_id,
                        'number_quota' => $i,
                        'estimated_payment' => $montoFinal,
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

                OrderQuotasDetails::insert($quotaDetails);
            } else {
                $orderData = [
                    'customer_id' => $validatedData['customer_id'],
                    'payment_method' => $validatedData['payment_method'],
                    'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'order_status' => 'Pagado',
                    'total_products' => Cart::count(),
                    'invoice_no' => $invoice_no,
                    'total' => Cart::total(),
                    'pay' => Cart::total(),
                    'employee_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $order_id = Order::insertGetId($orderData);
            }

            // Crear los detalles del pedido
            $contents = Cart::content();
            $orderDetails = [];

            foreach ($contents as $content) {
                $orderDetails[] = [
                    'order_id' => $order_id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->total,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // Insertar todos los detalles de productos en la tabla 'order_details'
            OrderDetails::insert($orderDetails);

            // **Actualizar el stock de los productos**
            foreach ($contents as $content) {
                Product::where('id', $content->id)->decrement('product_store', $content->qty);
            }

            DB::commit();

            // Vaciar el carrito
            Cart::destroy();

            if ($validatedData['payment_method'] == 'CUOTAS') {
                return redirect()->route('order.completeOrders')->with('success', "¡Venta creada con éxito! <a href=" . route('order.downloadReceiptVenta', $order_id) . " target='_blank'>Haga click aqui </a> para descargar el comprobante de la Venta.");
            }
            return redirect()->route('order.completeOrders')->with('success', "¡Venta creada con éxito! <a href=" . route('order.downloadReceiptVentaNormal', $order_id) . " target='_blank'>Haga click aqui </a> para descargar el comprobante de la Venta.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al procesar la orden.');
        }
    }



    public function downloadReceiptVenta(Order $order)
    {
        // Buscar si hay un attachment asociado al pedido
        $attachment = Attachment::where('order_id', $order->id)->first();


        if ($attachment) {
            // Obtener la ruta completa del archivo
            $filePath = storage_path('app/public/' . $attachment->path);

            // Verificar si el archivo realmente existe
            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }

        // Si no hay attachment, generar el PDF como antes
        $cliente = Customer::find($order->customer_id);

        $valorCuota = OrderQuotasDetails::where('order_id', $order->id)->where('number_quota', '2')->value('estimated_payment');
        $estimatedPaymentDate = OrderQuotasDetails::where('order_id', $order->id)->value('estimated_payment_date');

        $details = OrderDetails::where('order_id', $order->id)->with('product.brand')->get();

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdfFileName = 'Venta_' . $details[0]->product->name . '_' . $cliente->name . '.pdf';

        $pdf = Pdf::loadView('orders.payment-receipt-venta-quota', compact(
            'order',
            'cliente',
            'htmlLogo',
            'htmlTitle',
            'valorCuota',
            'estimatedPaymentDate',
            'details'
        ))->setPaper('cart', 'vertical');

        return $pdf->download($pdfFileName, ['Attachment' => 0]);
    }


    public function downloadReceiptVentaNormal(Order $order)
    {
        $cliente = Customer::all()->where('id', '=', $order->customer_id)->first();

        $details = OrderDetails::where('order_id', $order->id)->with('product')->get();

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdfFileName = 'Venta_normal_' . $order->invoice_no . '_' . $cliente->name . '.pdf';


        $pdf = Pdf::loadView('orders.payment-receipt-venta-normal', compact('order', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'details'))
            ->setPaper('cart', 'vertical');

        return $pdf->download($pdfFileName, array('Attachment' => 0));
    }



    public function downloadReceiptQuota(OrderQuotasDetails $quota)
    {
        $order = Order::all()->where('id', '=', $quota->order_id)->first();
        $cliente = Customer::all()->where('id', '=', $order->customer_id)->first();
        $details = OrderDetails::where('order_id', $order->id)->with('product')->get();
        $valorCuota = OrderQuotasDetails::where('order_id', $order->id)->first()->value('estimated_payment');

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        // Cargar imagen de cancelado si la cuota está cancelada
        $htmlCancelado = '';
        if ($quota->cancelated) {
            $pathCancelado = storage_path('app/public/cancelated/cancelado.png'); // Ruta correcta
            if (file_exists($pathCancelado)) {
                $cancelado = file_get_contents($pathCancelado);
                $htmlCancelado = '<img src="data:image/png;base64,' . base64_encode($cancelado) . '" width="150" height="" />';
            }
        }

        $pdfFileName = 'Cuota_' . $quota->number_quota . '_' . $details[0]->product->name . '_' . $cliente->name . '.pdf';


        $pdf = Pdf::loadView('orders.payment-receipt-quota', compact('quota', 'order', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'details', 'valorCuota', 'htmlCancelado'))
            ->setPaper('cart', 'vertical');

        return $pdf->download($pdfFileName . ".pdf", array('Attachment' => 0));
    }


    /**
     * Display the orders for a specific customer.
     */
    public function customerDetails(Int $customer_id)
    {
        $orders = Order::where('customer_id', $customer_id)
            ->with('orderDetails.product', 'customer', 'attachments')
            ->orderBy('order_date', 'DESC')
            ->get();

        return view('orders.customer-orders', compact('orders'));
    }

    public function quotas(Int $order_id)
    {
        $order = Order::findOrFail($order_id)->load('customer');
        $quotas = OrderQuotasDetails::where('order_id', $order_id)
            ->orderByRaw('CAST(number_quota AS UNSIGNED) DESC')
            ->get();


        return view('orders.quotas', [
            'order' => $order,
            'quotas' => $quotas,
        ]);
    }

    public function paymentQuota(OrderQuotasDetails $quota)
    {
        // Obtener la cuota con su orden relacionada
        $quota = OrderQuotasDetails::where('id', $quota->id)->with('order')->first();

        if (!$quota) {
            return redirect()->back()->with('error', 'Cuota no encontrada.');
        }

        // Calcular los días vencidos (si la fecha estimada de pago ya pasó)
        $today = Carbon::now();
        $estimatedDate = Carbon::parse($quota->estimated_payment_date);
        $daysOverdue = $today->greaterThan($estimatedDate) ? $estimatedDate->diffInDays($today) : 0;

        // Obtener todas las cuotas anteriores
        $previousQuotas = OrderQuotasDetails::where('order_id', $quota->order_id)
            ->where('number_quota', '<', $quota->number_quota)
            ->get();

        // Calcular la suma de amount_difference de todas las cuotas anteriores
        $totalPreviousAmountDifference = $previousQuotas->sum('amount_difference');

        return view('orders.payment-quota', compact('quota', 'daysOverdue', 'totalPreviousAmountDifference'));
    }

    public function payment(Request $request)
    {
        try {
            $rules = [
                'quotaId' => 'required|numeric',
                'payment_method' => 'required|nullable|string',
                'currency' => 'required|nullable|string',
                'interest' => 'sometimes|nullable|string',
                'increment' => 'sometimes|nullable|string',
                'total_to_pay' => 'required|numeric',
            ];

            // Generación del número de factura
            $invoice_no = InvoiceHelper::generateInvoiceNo();

            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            // Obtener la cuota actual
            $quota = OrderQuotasDetails::findOrFail($validatedData['quotaId']);
            $totalPayment = round($validatedData['total_to_pay']);
            $estimatedPayment = round($quota->estimated_payment) ?? 0;
            // Obtener todas las cuotas anteriores con amount_difference pendiente
            $previousQuotas = OrderQuotasDetails::where('order_id', $quota->order_id)
                ->where('number_quota', '<', $quota->number_quota)
                ->where(function ($query) {
                    $query->where('amount_difference', '<>', 0)
                        ->orWhere('increment_due', '<>', 0); // Considerar también increment_due
                })
                ->orderByRaw("CAST(number_quota AS UNSIGNED) ASC") // Ordenar de la más antigua a la más reciente
                ->get();

            // Sumar el total de amount_difference y increment_due de las cuotas anteriores (deuda acumulada)
            $totalPreviousAmount = $previousQuotas->sum('amount_difference') + (-$validatedData['increment'] ?? 0);


            // **Verificar si hay saldo a favor o deuda acumulada**
            if ($totalPreviousAmount > 0) {
                // Si hay saldo a favor, restar de la cuota actual
                $estimatedPayment -= $totalPreviousAmount;
            } else if ($totalPreviousAmount < 0) {
                // Si hay deuda acumulada, sumar a la cuota actual
                $estimatedPayment += abs($totalPreviousAmount);
            }

            // Determinar cuánto se pagó de más o menos
            $amountDifference = $totalPayment - $estimatedPayment;

            // **Distribuir el pago primero en las cuotas anteriores**
            $remainingPayment = $totalPayment;

            if ($remainingPayment == $estimatedPayment) {

                if ($remainingPayment <= 0) return;

                // Si el pago es exactamente igual al total esperado, marcar todas las cuotas anteriores como pagadas
                foreach ($previousQuotas as $previousQuota) {
                    $previousQuota->amount_difference = 0;
                    $previousQuota->save();
                }
            }

            // dd($totalPayment, $estimatedPayment, $amountDifference, $previousQuotas);


            // **Actualizar la cuota actual con la diferencia final**
            $quota->update([
                'payment_method' => $validatedData['payment_method'],
                'payment_currency' => $validatedData['currency'],
                'interest_due' => $validatedData['interest'] ?? null,
                'payment_date' => now(),
                'increment_due' => $validatedData['increment'] ?? 0,
                'total_payment' => $totalPayment,
                'invoice_no' => $invoice_no,
                'status_payment' => 'Pagado',
                'updated_at' => now(),
                'amount_difference' => $amountDifference,
            ]);

            // Si es la última cuota del pedido, marcarla como cancelada
            $ultimaCuota = OrderQuotasDetails::where('order_id', $quota->order_id)
                ->orderByRaw("CAST(number_quota AS UNSIGNED) DESC")
                ->first();

            if ($ultimaCuota && $ultimaCuota->id == $quota->id) {
                $quota->update(['cancelated' => true]);

                // Actualizar el estado del pedido
                $order = $quota->order;
                if ($order) {
                    $order->update(['order_status' => 'Cancelado']);
                }
            }

            DB::commit();

            return redirect()->route('order.quotas', $quota->order_id)->with('success', 'Pago registrado correctamente.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }









    public function attachmentOrderCustomer(Request $request, Order $order)
    {
        try {
            $request->validate([
                'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10048',
            ]);

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = $file->getClientOriginalName();
                $path = $file->storeAs('attachments/orders', $filename, 'public');

                // Eliminar archivo anterior si existe
                if ($order->attachments->count()) {
                    Storage::disk('public')->delete($order->attachments->first()->path);
                    $order->attachments()->delete();
                }

                // Guardar nuevo archivo
                $order->attachments()->create([
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
