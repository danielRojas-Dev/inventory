@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Lista de Sueldos</h4>
                    </div>
                    <div>
                        <a class=" btn btn-primary add-list-" href="{{ route('advance-salary.create') }}">
                            <span>Pagar Sueldo</span>
                        </a>
                    </div>

                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('advance-salary.index') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="form-group row">
                            <label for="row" class="col-sm-3 align-self-center">Filas:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="row">
                                    <option value="10" @if (request('row') == '10') selected="selected" @endif>10
                                    </option>
                                    <option value="25" @if (request('row') == '25') selected="selected" @endif>25
                                    </option>
                                    <option value="50" @if (request('row') == '50') selected="selected" @endif>50
                                    </option>
                                    <option value="100" @if (request('row') == '100') selected="selected" @endif>100
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center" for="search">Buscar:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                        placeholder="Buscar Empleado" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary">
                                            <i class="fas fa-search font-size-20"></i>
                                        </button>
                                        <a href="{{ route('advance-salary.index') }}" class="input-group-text bg-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No.</th>
                                <th>Foto</th>
                                <th>@sortablelink('employee.name', 'Nombre')</th>
                                <th>@sortablelink('date', 'Fecha')</th>
                                <th>@sortablelink('employee.salary', 'Salario')</th>
                                <th>@sortablelink('advance_salary', 'Salario Adelantado')</th>
                                <th>Deuda</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($advanceSalaries as $advanceSalary)
                                <tr>
                                    <td>{{ $advanceSalaries->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $advanceSalary->employee->photo ? asset('storage/employees/' . $advanceSalary->employee->photo) : asset('assets/images/user/1.png') }}">
                                    </td>
                                    <td>{{ $advanceSalary->employee->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($advanceSalary->date)->format('M/Y') }}</td>
                                    <td>${{ $advanceSalary->employee->salary }}</td>
                                    <td>{{ $advanceSalary->advance_salary ? '$' . $advanceSalary->advance_salary : 'Sin Adelanto' }}
                                    </td>
                                    <td>${{ $advanceSalary->employee->salary - $advanceSalary->advance_salary }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Pagar Ahora"
                                                href="{{ route('pay-salary.paySalary', $advanceSalary->id) }}">Pagar
                                                Ahora</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Datos no encontrados.</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $advanceSalaries->links() }}
            </div>

        </div>
        <!-- Page end  -->
    </div>
@endsection
