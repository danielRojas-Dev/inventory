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
                        <h4 class="mb-3">Lista de Asistencia</h4>
                    </div>
                    <div>
                        <a href="{{ route('attendence.create') }}" class="btn btn-primary add-list">Crear Asistencia</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('attendence.index') }}" method="get">
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

                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No.</th>
                                <th>@sortablelink('date')</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($attendences as $attendence)
                                <tr>
                                    <td>{{ $attendences->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>{{ $attendence->date }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Ver"
                                                href="{{ route('attendence.show', $attendence->date) }}"><i
                                                    class="ri-eye-line mr-0"></i>
                                            </a>
                                            <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Edit"
                                                href="{{ route('attendence.edit', $attendence->date) }}"><i
                                                    class="ri-pencil-line mr-0"></i>
                                            </a>
                                            {{-- <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="View"
                                                href="{{ route('attendence.show', $attendence->date) }}"><i
                                                    class="ri-eye-line mr-0"></i>
                                            </a> --}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Datos no encontrados.</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $attendences->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
