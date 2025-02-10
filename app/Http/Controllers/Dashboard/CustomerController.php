<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtén todos los clientes sin paginación ni filtrado
        $customers = Customer::all();  // Obtiene todos los clientes

        // Devuelve la vista con todos los clientes
        return view('customers.index', [
            'customers' => $customers,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:15|unique:customers,phone',
            'city' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'dni' => 'required|string|max:8|unique:customers,dni,',
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/customers/';

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Customer::create($validatedData);

        return Redirect::route('customers.index')->with('success', 'Cliente creado correctamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $rules = [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:15|unique:customers,phone,' . $customer->id,
            'city' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'dni' => 'required|string|max:8|unique:customers,dni,' . $customer->id,
        ];

        $validatedData = $request->validate($rules);

        /**
         * Handle upload image with Storage.
         */
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/customers/';

            /**
             * Delete photo if exists.
             */
            if ($customer->photo) {
                Storage::delete($path . $customer->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        Customer::where('id', $customer->id)->update($validatedData);

        return Redirect::route('customers.index')->with('success', 'Cliente modificado correctamente!');
    }

    /**
     * Elimina el cliente si no tiene órdenes asociadas.
     */
    public function destroy(Customer $customer)
    {
        // Verificar si el cliente tiene órdenes asociadas
        if ($customer->orders()->exists()) {
            return Redirect::route('customers.index')->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas.');
        }

        // Eliminar la foto si existe
        if ($customer->photo) {
            Storage::delete('public/customers/' . $customer->photo);
        }

        // Eliminar el cliente
        Customer::destroy($customer->id);

        return Redirect::route('customers.index')->with('success', '¡El cliente ha sido eliminado correctamente!');
    }
}