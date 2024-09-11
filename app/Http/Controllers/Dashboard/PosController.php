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
        // $todayDate = Carbon::now();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
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
        // Validar los datos del producto, incluyendo el precio seleccionado
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric', // Aquí ya validamos que se envíe un precio
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
    
        // Agregar el producto al carrito (cantidad por defecto: 1)
        Cart::add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'], // Usar el precio seleccionado por el usuario
            'options' => ['size' => 'large']
        ]);
    
        return Redirect::back()->with('success', '¡El producto ha sido agregado al carrito!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Cart has been updated!');
    }
    
    
    
    

    public function deleteCart(String $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Cart has been deleted!');
    }

    public function createInvoice(Request $request)
    {
        // $rules = [
        //     'customer_id' => 'required'
        // ];

        // $validatedData = $request->validate($rules);
        // $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.create-invoice', [
            // 'customer' => $customer,
            'content' => $content
        ]);
    }

    public function printInvoice(Request $request)
    {
        $rules = [
            // 'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        // $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.print-invoice', [
            // 'customer' => $customer,
            'content' => $content
        ]);
    }
}