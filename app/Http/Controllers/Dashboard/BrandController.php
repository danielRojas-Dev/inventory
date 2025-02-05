<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Support\Facades\Redirect;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        return view('brands.index', [
            'brands' => Brand::filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:brands,name',
            'slug' => 'required|unique:brands,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Brand::create($validatedData);

        return Redirect::route('brands.index')->with('success', 'Marca creada correctamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', [
            'brand' => $brand
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'name' => 'required|unique:brands,name,' . $brand->id,
            'slug' => 'required|alpha_dash|unique:brands,slug,' . $brand->id,
        ];

        $validatedData = $request->validate($rules);

        Brand::where('slug', $brand->slug)->update($validatedData);

        return Redirect::route('brands.index')->with('success', 'Marca modificada correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {

        if ($brand->products()->exists()) {
            return Redirect::route('brands.index')->with('error', 'No se puede eliminar la marca porque tiene productos asociados.');
        }
        Brand::where('slug', $brand->slug)->delete();

        return Redirect::route('brands.index')->with('success', 'Marca eliminada con exito!');
    }
}
