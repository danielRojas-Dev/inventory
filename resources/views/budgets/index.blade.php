@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{!! session('success') !!}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert text-white bg-danger" role="alert">
                        <div class="iq-alert-text">{{ session('error') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                    <h4 class="mb-3 mb-md-0">Lista de Presupuestos</h4>
                    <a href="{{ route('pos.index') }}" class="btn btn-primary add-list"><i
                            class="fas fa-plus mr-3"></i>Crear Presupuesto</a>
                </div>
            </div>



            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>@sortablelink('budget_no', 'Nº Presupuesto')</th>
                                <th>Cliente</th>
                                <th>@sortablelink('budget_date', 'Fecha')</th>
                                <th>@sortablelink('total', 'Total')</th>
                                <th>Método</th>
                                <th>@sortablelink('valid_until', 'Válido Hasta')</th>
                                <th>@sortablelink('budget_status', 'Estado')</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($budgets as $budget)
                                <tr>
                                    <td>{{ $budget->budget_no }}</td>
                                    <td>{{ $budget->customer->name }}</td>
                                    <td>{{ $budget->budget_date_formatted }}</td>
                                    <td>${{ number_format($budget->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $budget->payment_method }}</span>
                                    </td>
                                    <td>
                                        <span class="@if ($budget->is_expired) text-danger @endif">
                                            {{ $budget->valid_until_formatted }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($budget->budget_status == 'Pendiente')
                                            <span class="badge badge-warning">{{ $budget->budget_status }}</span>
                                        @elseif($budget->budget_status == 'Convertido')
                                            <span class="badge badge-success">{{ $budget->budget_status }}</span>
                                        @elseif($budget->budget_status == 'Vencido')
                                            <span class="badge badge-danger">{{ $budget->budget_status }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $budget->budget_status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="View"
                                                href="{{ route('budgets.show', $budget->id) }}"><i
                                                    class="ri-eye-line mr-0"></i>
                                            </a>
                                            <a class="badge bg-secondary mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Print"
                                                href="{{ route('budgets.print', $budget->id) }}" target="_blank"><i
                                                    class="ri-printer-line mr-0"></i>
                                            </a>
                                            @if ($budget->can_convert)
                                                <form method="POST" action="{{ route('budgets.convert', $budget->id) }}"
                                                    style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="badge bg-success mr-2"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Convert to Sale"
                                                        onclick="return confirm('¿Está seguro de convertir este presupuesto a venta?')"
                                                        style="border: none;">
                                                        <i class="ri-exchange-line mr-0"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($budget->budget_status == 'Pendiente')
                                                <form method="POST" action="{{ route('budgets.cancel', $budget->id) }}"
                                                    style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="badge bg-danger mr-2"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Cancel"
                                                        onclick="return confirm('¿Está seguro de cancelar este presupuesto?')"
                                                        style="border: none;">
                                                        <i class="ri-close-line mr-0"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay presupuestos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="d-flex justify-content-center">
                    {{ $budgets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
