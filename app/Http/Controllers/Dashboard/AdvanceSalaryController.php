<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class AdvanceSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro de filas por página debe ser un número entero entre 1 y 100.');
        }

        if(request('search')){
            Employee::firstWhere('name', request('search'));
        }

        return view('advance-salary.index', [
            'advance_salaries' => AdvanceSalary::with(['employee'])
                ->orderByDesc('date')
                ->filter(request(['search']))
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
        return view('advance-salary.create', [
            'employees' => Employee::all()->sortBy('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'employee_id' => 'required',
            'date' => 'required|date_format:Y-m-d|max:10',
            'advance_salary' => 'numeric|nullable'
        ];

        if ($request->date) {
            // formatea la fecha para mostrar solo el año y mes
            $getYm = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m');
        } else {
            $validatedData = $request->validate($rules);
        }

        $advanced = AdvanceSalary::where('employee_id', $request->employee_id)
            ->whereDate('date', 'LIKE',  $getYm . '%')
            ->get();

        if ($advanced->isEmpty()) {
            $validatedData = $request->validate($rules);
            AdvanceSalary::create($validatedData);

            return Redirect::route('advance-salary.create')->with('success', '¡Salario adelantado pagado con éxito!');
        } else {
            return Redirect::route('advance-salary.create')->with('warning', '¡El salario adelantado ya ha sido pagado!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdvanceSalary $advanceSalary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdvanceSalary $advanceSalary)
    {
        return view('advance-salary.edit', [
            'employees' => Employee::all()->sortBy('name'),
            'advance_salary' => $advanceSalary,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdvanceSalary $advanceSalary)
    {
        $rules = [
            'employee_id' => 'required',
            'date' => 'required|date_format:Y-m-d|max:10|',
            'advance_salary' => 'required|numeric'
        ];

        // formatea la fecha para mostrar solo el año y mes (YM)
        $newYm = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y-m');
        $oldYm = Carbon::createFromFormat('Y-m-d', $advanceSalary->date)->format('Y-m');

        $advanced = AdvanceSalary::where('employee_id', $request->id)
            ->whereDate('date', 'LIKE',  $newYm . '%')
            ->first();

        if (!$advanced && $newYm == $oldYm) {
            $validatedData = $request->validate($rules);
            AdvanceSalary::where('id', $advanceSalary->id)->update($validatedData);

            return Redirect::route('advance-salary.edit', $advanceSalary->id)->with('success', '¡Salario adelantado actualizado con éxito!');
        } else {
            return Redirect::route('advance-salary.edit', $advanceSalary->id)->with('warning', '¡El salario adelantado ya ha sido pagado!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdvanceSalary $advanceSalary)
    {
        AdvanceSalary::destroy($advanceSalary->id);

        return Redirect::route('advance-salary.index')->with('success', '¡El salario adelantado ha sido eliminado!');
    }
}