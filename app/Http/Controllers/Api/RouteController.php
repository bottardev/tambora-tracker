<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;


class RouteController extends Controller
{
    public function index()
    {
        return Route::select('id', 'name', 'description', 'total_distance_km')
            ->with(['checkpoints' => function ($query) {
                $query->selectRaw(
                    'id, route_id, name, order_no, radius_m, ST_Y(location) as lat, ST_X(location) as lng'
                );
            }])
            ->paginate(20);
    }
    public function show(Route $route)
    {
        $route->load(['checkpoints' => function ($query) {
            $query->selectRaw(
                'id, route_id, name, order_no, radius_m, ST_Y(location) as lat, ST_X(location) as lng'
            );
        }]);
        return $route;
    }
}
