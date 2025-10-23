<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;


class RouteController extends Controller
{
    public function index()
    {
        return Route::select('id', 'name', 'description', 'total_distance_km')->with(['checkpoints:id,route_id,name,order_no,radius_m'])->paginate(20);
    }
    public function show(Route $route)
    {
        $route->load('checkpoints');
        return $route;
    }
}
