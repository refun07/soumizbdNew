<?php

namespace App\Http\Middleware;

use App\Models\Warehouse;
use App\Models\WarehouseZone;
use Closure;
use Illuminate\Http\Request;
use Schema;
use Symfony\Component\HttpFoundation\Response;

class ZoneMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (request()->hasHeader('Zone-Id')) {
            $ids = WarehouseZone::where('zone_id', $request->header('Zone-Id'))->pluck('warehouse_id');
            if (count($ids) < 1) {
                $ids = [0];
            }
            session()->put('WarehouseIds', $ids);
        } else {
            if (Schema::hasTable('warehouses')) {
                $ids = Warehouse::where('id', '!=', null)->pluck('id');
                session()->put('WarehouseIds', $ids);
            }
        }
        return $next($request);
    }
}
