<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;

class PosController extends Controller
{
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        return view('pos.index', [
            'customers' => Customer::all()->sortBy('name'),
            'productItem' => Cart::content(),
            'products' => Product::filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function addCart(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', '¡Se ha añadido el producto!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', '¡Se ha actualizado el carrito!');
    }

    public function deleteCart(String $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Se ha vaciado el carrito.');
    }

    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required',
            'payment_method' => 'required',
            'quotas' => 'sometimes|nullable|in:6,9',
            'estimated_payment_date' => 'sometimes|nullable',
        ];


        $validatedData = $request->validate($rules);

        $customer = Customer::find($validatedData['customer_id']);
        $content = Cart::content();
        $totalOriginal = Cart::total(); // Total sin modificaciones

        $quotas = $validatedData['quotas'] ?? null;

        $totalConInteres = $totalOriginal; // Se descuenta la entrega

        if ($quotas) {
            // Aplicar interés según la cantidad de cuotas
            $interes = $quotas == 6 ? 0.35 : 0.70;
            $totalConInteres *= (1 + $interes);
            $montoCuota = $totalConInteres / $quotas;
        } else {
            $totalConInteres = $totalOriginal;
            $montoCuota = 0;
        }


        return view('pos.create-invoice', [
            'customer' => $customer,
            'content' => $content,
            'payment_method' => $validatedData['payment_method'],
            'quotas' => $quotas,
            'estimated_payment_date' => $validatedData['estimated_payment_date'] ?? null,
            'total_original' => $totalOriginal,
            'total_con_interes' => $totalConInteres,
            'monto_cuota' => $montoCuota,
        ]);
    }


    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.print-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }
}
