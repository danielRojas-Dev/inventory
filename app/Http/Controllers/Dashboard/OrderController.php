<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrderquotasDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function completeOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        $customers = Customer::whereHas('orders') // Filtra solo clientes con órdenes
            ->withCount('orders') // Opcional: cuenta las órdenes de cada cliente
            ->sortable()
            ->paginate($row);


        return view('orders.complete-orders', [
            'customers' => $customers
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
                'quotas' => 'sometimes|nullable|in:6,9',
                'estimated_payment_date' => 'sometimes|nullable|string',
            ];

            // Generación del número de factura
            $invoice_no = $this->generateInvoiceNo();

            // Validación de los datos de entrada
            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            if ($validatedData['payment_method'] == 'CUOTAS') {
                // Calcular el total con el interés
                $totalOriginal = Cart::total();
                $interesMin = 0.35;
                $interesMax = 0.70;
                $interes = $validatedData['quotas'] == 6 ? $interesMin : $interesMax;
                $totalConInteres = $totalOriginal * (1 + $interes);
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
                    'total' => $totalConInteres,
                    'pay' => 0,
                    'employee_id' => auth()->id(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $order_id = Order::insertGetId($orderData);

                // Crear los registros en OrderquotasDetails
                $quotaDetails = [];
                for ($i = 1; $i <= $validatedData['quotas']; $i++) {
                    $quotaDetails[] = [
                        'order_id' => $order_id,
                        'number_quota' => $i,
                        'estimated_payment' => $montoCuota,
                        'interest_plan' => $interes,
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

                OrderquotasDetails::insert($quotaDetails);
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
        $cliente = Customer::all()->where('id', '=', $order->customer_id)->first();

        $valorCuota = OrderquotasDetails::where('order_id', $order->id)->first()->value('estimated_payment');
        $estimatedPaymentDate = OrderquotasDetails::where('order_id', $order->id)->first()->value('estimated_payment_date');

        $details = OrderDetails::where('order_id', $order->id)->with('product.brand')->get();

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdfFileName = 'Factura_Venta' . $order->invoice_no . '_cliente_' . $cliente->name . '.pdf';

        $pdf = Pdf::loadView('orders.payment-receipt-venta-quota', compact('order', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'valorCuota', 'estimatedPaymentDate', 'details'))
            ->setPaper('cart', 'vertical');

        return $pdf->stream($pdfFileName, array('Attachment' => 0));
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

        $pdfFileName = 'Factura_Venta' . $order->invoice_no . '_cliente_' . $cliente->name . '.pdf';


        $pdf = Pdf::loadView('orders.payment-receipt-venta-normal', compact('order', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'details'))
            ->setPaper('cart', 'vertical');

        return $pdf->stream($pdfFileName, array('Attachment' => 0));
    }



    public function downloadReceiptQuota(OrderquotasDetails $quota)
    {
        $order = Order::all()->where('id', '=', $quota->order_id)->first();
        $cliente = Customer::all()->where('id', '=', $order->customer_id)->first();
        $details = OrderDetails::where('order_id', $order->id)->with('product')->get();
        $valorCuota = OrderquotasDetails::where('order_id', $order->id)->first()->value('estimated_payment');

        $pathLogo = public_path('assets/images/login/electrodr.png');
        $logo = file_get_contents($pathLogo);

        $pathTitle = public_path('assets/images/login/title.png');
        $title = file_get_contents($pathTitle);

        $htmlLogo = '<img src="data:image/svg+xml;base64,' . base64_encode($logo) . '"  width="100" height="" />';
        $htmlTitle = '<img src="data:image/svg+xml;base64,' . base64_encode($title) . '"  width="300" height="" />';

        $pdfFileName = 'Factura_Cuota' . $order->invoice_no . '_cliente_' . $cliente->name . '.pdf';


        $pdf = Pdf::loadView('orders.payment-receipt-quota', compact('quota', 'order', 'cliente', 'pathLogo', 'htmlLogo', 'htmlTitle', 'details', 'valorCuota'))
            ->setPaper('cart', 'vertical');

        return $pdf->stream(date('d-m-Y') . ".pdf", array('Attachment' => 0));
    }


    /**
     * Display the orders for a specific customer.
     */
    public function customerDetails(Int $customer_id)
    {
        $orders = Order::where('customer_id', $customer_id)
            ->with('orderDetails', 'customer')
            ->orderBy('order_date', 'DESC')
            ->get();

        return view('orders.customer-orders', compact('orders'));
    }

    public function quotas(Int $order_id)
    {
        $order = Order::findOrFail($order_id)->load('customer');
        $quotas = OrderquotasDetails::where('order_id', $order_id)
            ->orderByRaw('CAST(number_quota AS UNSIGNED) DESC')
            ->get();


        return view('orders.quotas', [
            'order' => $order,
            'quotas' => $quotas,
        ]);
    }

    public function paymentQuota(OrderquotasDetails $quota)
    {
        // Obtener la cuota con su orden relacionada
        $quota = OrderquotasDetails::where('id', $quota->id)->with('order')->first();

        // Calcular los días vencidos (si la fecha estimada de pago ya pasó)
        $today = \Carbon\Carbon::now();
        $estimatedDate = \Carbon\Carbon::parse($quota->estimated_payment_date);

        // Si la fecha estimada ya pasó, calcular los días de vencimiento
        $daysOverdue = $today->greaterThan($estimatedDate) ? $estimatedDate->diffInDays($today) : 0;

        return view('orders.payment-quota', compact('quota', 'daysOverdue'));
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
            ];

            // Generación del número de factura
            $invoice_no = $this->generateInvoiceNo();


            $validatedData = $request->validate($rules);



            DB::beginTransaction();


            // Obtener la cuota
            $quota = OrderquotasDetails::findOrFail($validatedData['quotaId']);

            // Calcular el total a pagar
            $increment = $validatedData['increment'] ?? null;
            $totalPaid = $quota->estimated_payment + $increment;

            // Registrar el pago
            $quota->update([
                'payment_method' => $validatedData['payment_method'],
                'payment_currency' => $validatedData['currency'],
                'interest_due' => $validatedData['interest'] ?? null,
                'payment_date' => now(),
                'increment_due' => $increment,
                'total_payment' => $totalPaid,
                'invoice_no' => $invoice_no,
                'status_payment' => 'Pagado',
                'updated_at' => now()
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pago registrado correctamente.');
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
        }
    }


    public function generateInvoiceNo()
    {
        // Obtener el último invoice_no de ambas tablas
        $lastOrderInvoice = Order::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        $lastQuotaInvoice = OrderquotasDetails::whereNotNull('invoice_no')
            ->orderBy('invoice_no', 'desc')
            ->limit(1)
            ->value('invoice_no');

        // Determinar cuál es el último invoice_no
        $lastInvoice = max($lastOrderInvoice, $lastQuotaInvoice);

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




    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request)
    {
        $order_id = $request->id;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['product_store' => DB::raw('product_store-' . $product->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'complete']);

        return Redirect::route('order.pendingOrders')->with('success', '¡La orden ha sido completada!');
    }

    public function invoiceDownload(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();

        // show data (only for debugging)
        return view('orders.invoice-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function pendingDue()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        $orders = Order::where('due', '>', '0')
            ->sortable()
            ->paginate($row);

        return view('orders.pending-due', [
            'orders' => $orders
        ]);
    }

    public function orderDueAjax(Int $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function updateDue(Request $request)
    {
        // Validaciones
        $rules = [
            'order_id' => 'required|numeric',
            'due' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        // Buscar la orden
        $order = Order::findOrFail($request->order_id);
        $mainPay = $order->pay;
        $mainDue = $order->due;

        // Calcular el nuevo valor de due y pay
        $paid_due = $mainDue - $validatedData['due'];
        $paid_pay = $mainPay + $validatedData['due'];

        // Verificar si el monto pendiente es 0, para actualizar el estado de la orden
        $order_status = $paid_due <= 0 ? 'complete' : $order->order_status;

        // Actualizar la orden
        $order->update([
            'due' => $paid_due,
            'pay' => $paid_pay,
            'order_status' => $order_status, // Cambiar el estado si es necesario
        ]);

        if ($order_status == 'complete') {
            return Redirect::route('order.completeOrders')->with('success', '¡Pago Completado con éxito!');
        }

        // Redirigir con un mensaje de éxito
        return Redirect::route('order.pendingDue')->with('success', '¡Importe adeudado actualizado correctamente!');
    }
}
