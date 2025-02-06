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
            'products' => Product::with('brand')->filter(request(['search']))
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

        // Buscar el producto en la base de datos
        $product = Product::find($validatedData['id']);

        if (!$product) {
            return Redirect::back()->with('error', 'El producto no existe.');
        }

        // Obtener la cantidad actual del producto en el carrito
        $cartItem = Cart::content()->where('id', $product->id)->first();
        $currentQtyInCart = $cartItem ? $cartItem->qty : 0;


        // Verificar si al agregar el producto excederíamos el stock disponible
        if ($currentQtyInCart + 1 > $product->product_store) {
            return Redirect::back()->with('error', 'No puedes agregar más productos. El stock disponible es: ' . $product->product_store);
        }


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
            'qty' => 'required|numeric|min:1',
        ];

        $validatedData = $request->validate($rules);

        // Obtener el item del carrito
        $cartItem = Cart::get($rowId);

        if (!$cartItem) {
            return Redirect::back()->with('error', 'El producto no existe en el carrito.');
        }

        // Buscar el producto en la base de datos
        $product = Product::find($cartItem->id);

        if (!$product) {
            return Redirect::back()->with('error', 'El producto no existe en la base de datos.');
        }

        // Verificar si la cantidad solicitada excede el stock disponible
        if ($validatedData['qty'] > $product->product_store) {
            return Redirect::back()->with('error', 'No puedes actualizar la cantidad. Stock disponible: ' . $product->product_store);
        }

        // Actualizar la cantidad en el carrito
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
        try {
            $rules = [
                'customer_id' => 'required',
                'payment_method' => 'required',
                'quotas' => 'sometimes|nullable|integer|min:1',
                'interest_rate' => 'sometimes|nullable|numeric|min:0',
                'estimated_payment_date' => 'sometimes|nullable',
            ];

            $validatedData = $request->validate($rules);


            $customer = Customer::find($validatedData['customer_id']);
            $content = Cart::content();
            $totalOriginal = Cart::total(); // Total sin modificaciones

            $quotas = $validatedData['quotas'] ?? null;
            $interestRate = $validatedData['interest_rate'] ?? 0; // Interés ingresado por el usuario

            $totalConInteres = $totalOriginal;

            if ($quotas && $interestRate > 0) {
                $totalConInteres *= (1 + ($interestRate / 100)); // Aplicar el interés ingresado
                $montoCuota = $totalConInteres / $quotas;
            } else {
                $montoCuota = $quotas ? ($totalOriginal / $quotas) : 0;
            }

            return view('pos.create-invoice', [
                'customer' => $customer,
                'content' => $content,
                'payment_method' => $validatedData['payment_method'],
                'quotas' => $quotas,
                'interest_rate' => $interestRate,
                'estimated_payment_date' => $validatedData['estimated_payment_date'] ?? null,
                'total_original' => $totalOriginal,
                'total_con_interes' => $totalConInteres,
                'monto_cuota' => $montoCuota,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
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
