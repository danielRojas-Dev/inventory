<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        $orders = Order::where('order_status', 'Pendiente')->where('due', '0')->sortable()->paginate($row);

        return view('orders.pending-orders', [
            'orders' => $orders
        ]);
    }

    public function completeOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        $orders = Order::where('order_status', 'complete')->orderBy('order_date', 'desc')->sortable()->paginate($row);

        return view('orders.complete-orders', [
            'orders' => $orders
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

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrder(Request $request)
    {
        try {
            $rules = [
                // 'customer_id' => 'required|numeric',
                'payment_status' => 'required|string',
                // 'pay' => 'numeric|nullable',
                'due' => 'numeric|nullable',
            ];

            $invoice_no = IdGenerator::generate([
                'table' => 'orders',
                'field' => 'invoice_no',
                'length' => 10,
                'prefix' => 'INV-'
            ]);
            $validatedData = $request->validate($rules);
            $validatedData['order_date'] = Carbon::now()->format('Y-m-d H:i:s');
            $validatedData['order_status'] = 'Pendiente';
            $validatedData['total_products'] = Cart::count();
            $validatedData['sub_total'] = Cart::subtotal();
            $validatedData['vat'] = Cart::tax();
            $validatedData['invoice_no'] = $invoice_no;
            $validatedData['total'] = Cart::total();
            $validatedData['due'] = Cart::total();
            $validatedData['pay'] = 0;
            // $validatedData['due'] = Cart::total() - $validatedData['pay'];
            $validatedData['created_at'] = Carbon::now();
            $validatedData['created_at'] = Carbon::now();
            $validatedData['user_id'] = auth()->id(); // ID del usuario logueado

            $order_id = Order::insertGetId($validatedData);

            // Create Order Details
            $contents = Cart::content();
            $oDetails = array();

            foreach ($contents as $content) {
                $oDetails['order_id'] = $order_id;
                $oDetails['product_id'] = $content->id;
                $oDetails['quantity'] = $content->qty;
                $oDetails['unitcost'] = $content->price;
                $oDetails['total'] = $content->total;
                $oDetails['created_at'] = Carbon::now();

                OrderDetails::insert($oDetails);
            }

            // Delete Cart Sopping History
            Cart::destroy();

            return Redirect::route('dashboard')->with('success', '¡Se ha creado la Venta!');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function orderDetails(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
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