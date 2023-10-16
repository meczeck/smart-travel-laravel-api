<?php

namespace App\Http\Controllers\API\BusCompany;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /** 
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Route::join('regions', 'routes.origin', '=', 'regions.id')
            ->select('routes.id as route_id', 'regions.id as region_id', 'routes.bus_company_id', 'routes.origin', 'routes.destination', 'routes.pathway')
            ->get();

        // $query= Route::all();

        return response()->json(['data' => $query], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    public function getCompanyRoutes($id)
    {
        $routes = Route::where('bus_company_id', $id)->get();
        return response()->json(['data' => $routes], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}