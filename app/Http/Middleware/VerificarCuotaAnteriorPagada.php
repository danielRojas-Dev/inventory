<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DetalleVenta;
use App\Models\OrderQuotasDetails;

class VerificarCuotaAnteriorPagada
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        $quota = $request->quota;

        $ultimaCuotaPagada = OrderQuotasDetails::where('order_id', $quota->order_id)->where('status_payment', 'pagado')->orderByRaw("CAST(number_quota AS UNSIGNED) DESC")->first();



        $cuotaActual = OrderQuotasDetails::where('id', $quota->id)->first();


        $primeraCuota = OrderQuotasDetails::where('id', $quota->id)->where('number_quota', '=', '1')->first();

        if ($primeraCuota?->status_payment == 'Pendiente') {
            return $next($request);
        }

        if ($ultimaCuotaPagada && $cuotaActual && $cuotaActual->number_quota == ($ultimaCuotaPagada->number_quota + 1)) {
            return $next($request);
        }



        return redirect()->route('order.quotas', $request?->quota?->order_id)->with('error', 'Debe pagar la cuota anterior!. Siga el orden de menor a mayor');
    }
}
