<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    /**
     * Mostrar una lista de los recursos.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entero entre 1 y 100.');
        }

        return view('categories.index', [
            'categories' => Category::filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Almacenar un nuevo recurso en la base de datos.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:categories,name',
            'slug' => 'required|unique:categories,slug|alpha_dash',
        ];

        $validatedData = $request->validate($rules);

        Category::create($validatedData);

        return Redirect::route('categories.index')->with('success', '¡Categoría creada exitosamente!');
    }

    /**
     * Mostrar el recurso especificado.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Mostrar el formulario para editar el recurso especificado.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    /**
     * Actualizar el recurso especificado en la base de datos.
     */
    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => 'required|unique:categories,name,'.$category->id,
            'slug' => 'required|alpha_dash|unique:categories,slug,'.$category->id,
        ];

        $validatedData = $request->validate($rules);

        Category::where('slug', $category->slug)->update($validatedData);

        return Redirect::route('categories.index')->with('success', '¡Categoría actualizada exitosamente!');
    }

    /**
     * Eliminar el recurso especificado de la base de datos.
     */
    public function destroy(Category $category)
    {
        try {
            // Buscar la categoría por el slug y eliminarla
            $categoryToDelete = Category::where('slug', $category->slug)->first();
    
            if ($categoryToDelete) {
                $categoryToDelete->delete();
                return Redirect::route('categories.index')->with('success', '¡Categoría eliminada exitosamente!');
            } else {
                return Redirect::route('categories.index')->with('error', '¡Categoría no encontrada!');
            }
        } catch (\Throwable $th) {
        }
    }
    
}