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
        return view('pos.index', [
            'customers' => Customer::all()->sortBy('name'), // Obtener todos los clientes
            'productItem' => Cart::content(), // Obtener los productos en el carrito
            'products' => Product::with('brand')->get(), // Obtener todos los productos sin paginación
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
                // Aceptamos cualquier cadena y luego la normalizamos manualmente (reemplazo de coma/puntos, etc.)
                'interest_rate' => 'sometimes|nullable|string',
                'estimated_payment_date' => 'sometimes|nullable',
                'entrega' => 'sometimes|nullable|numeric|min:0',
                'monto_cuota' => 'sometimes|nullable|numeric|min:0',
                'payment_month' => 'sometimes|nullable|string',
            ];

            $validatedData = $request->validate($rules);


            $customer = Customer::find($validatedData['customer_id']);
            $content = Cart::content();
            $totalOriginal = Cart::total(); // Total sin modificaciones

            $quotas = $validatedData['quotas'] ?? null;
            $entrega = $validatedData['entrega'] ?? 0;

            // Si el usuario envió un monto de cuota, lo tomamos como fuente de verdad
            if ($quotas && !empty($validatedData['monto_cuota'])) {
                $montoCuota = $validatedData['monto_cuota'];
                $totalConInteres = ($montoCuota * $quotas) + $entrega;

                // Recalcular el porcentaje de interés a partir del total con interés
                if ($totalOriginal > 0) {
                    $interestRate = (($totalConInteres / $totalOriginal) - 1) * 100;
                } else {
                    $interestRate = 0;
                }
            } else {
                // Caso normal: usar el porcentaje de interés como fuente de verdad
                // Normalizar tasa de interés cuando viene escalada desde el formulario
                $interestRateRaw = $validatedData['interest_rate'] ?? 0; // Interés ingresado por el usuario
                $interestRate = $interestRateRaw;

                // Si la tasa viene en un formato claramente escalado (por ejemplo 71200),
                // la convertimos a porcentaje real dividiendo por 1000. Para valores dentro
                // de un rango razonable (0-1000%) la dejamos tal cual.
                if ($interestRateRaw > 1000) {
                    $interestRate = $interestRateRaw / 1000;
                }

                $totalConInteres = $totalOriginal;

                if ($quotas) {
                    // Aplicar interés si corresponde
                    if ($interestRate > 0) {
                        $totalConInteres = $totalOriginal * (1 + ($interestRate / 100));
                    }

                    // Calcular total a financiar restando la entrega
                    $totalAFinanciar = $totalConInteres - $entrega;
                    $montoCuota = $totalAFinanciar / $quotas;
                } else {
                    $montoCuota = 0;
                }
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
                'entrega' => $entrega,
                'payment_month' => $validatedData['payment_month'] ?? null,
            ]);
        } catch (\Throwable $th) {
            return Redirect::back()->with('error', 'Ocurrió un error al generar la factura preliminar.');
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
