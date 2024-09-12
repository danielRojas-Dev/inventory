<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Employee;
use App\Models\Attendence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AttendenceController extends Controller
{
    /**
     * Muestra una lista del recurso.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un entero entre 1 y 100.');
        }

        return view('attendence.index', [
            'attendences' => Attendence::sortable()
                ->select('date')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('attendence.create', [
            'employees' => Employee::all()->sortBy('name'),
        ]);
    }

    /**
     * Almacena un nuevo recurso en la base de datos.
     */
    public function store(Request $request)
    {
        $countEmployee = count($request->employee_id);
        $rules = [
            'date' => 'required|date_format:Y-m-d|max:10',
        ];

        $validatedData = $request->validate($rules);

        // Elimina si la fecha ya existe (esto es solo para actualizar la nueva asistencia). Si no, creará una nueva asistencia.
        Attendence::where('date', $validatedData['date'])->delete();

        for ($i=1; $i <= $countEmployee; $i++) {
            $status = 'status' . $i;
            $attend = new Attendence();

            $attend->date = $validatedData['date'];
            $attend->employee_id = $request->employee_id[$i];
            $attend->status = $request->$status;

            $attend->save();
        }

        return Redirect::route('attendence.index')->with('success', '¡Asistencia creada correctamente!');
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(Attendence $attendence)
    {
        return view('attendence.show', [
            'attendences' => Attendence::with(['employee'])->where('date', $attendence->date)->get(),
            'date' => $attendence->date
        ]);
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(Attendence $attendence)
    {
        return view('attendence.edit', [
            'attendences' => Attendence::with(['employee'])->where('date', $attendence->date)->get(),
            'date' => $attendence->date
        ]);
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     */
    public function update(Request $request, Attendence $attendence)
    {
        //
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     */
    public function destroy(Attendence $attendence)
    {
        //
    }
}