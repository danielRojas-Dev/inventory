<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtén todas las categorías sin paginación ni filtrado
        $categories = Category::all();  // Obtiene todas las categorías

        // Devuelve la vista con todas las categorías
        return view('categories.index', [
            'categories' => $categories,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
            'slug' => 'required|unique:categories,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Category::create($validatedData);

        return Redirect::route('categories.index')->with('success', 'Categoria creada correctamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => 'required|unique:categories,name,' . $category->id,
            'slug' => 'required|alpha_dash|unique:categories,slug,' . $category->id,
        ];

        $validatedData = $request->validate($rules);

        Category::where('slug', $category->slug)->update($validatedData);

        return Redirect::route('categories.index')->with('success', 'Categoria modificada correctamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        try {
            if ($category->products()->exists()) {
                return Redirect::route('categories.index')->with('error', 'No se puede eliminar la categoria porque tiene productos asociados.');
            }

            Category::where('slug', $category->slug)->delete();

            return Redirect::route('categories.index')->with('success', 'Categoria eliminada con exito!');
        } catch (\Throwable $th) {
        }
    }
}