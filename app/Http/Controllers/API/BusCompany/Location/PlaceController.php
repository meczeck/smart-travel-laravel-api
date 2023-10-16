<?php

namespace App\Http\Controllers\API\BusCompany\Location;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Region;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function getAllRegions()
    {
        $regions = Region::orderBy('name', 'asc')->get();
        return response()->json(['data' => $regions], 200);
    }

    public function getDistrictsByRegion($id)
    {
        $districts = District::where('region_id', $id)->orderBy('name', 'asc')->get();
        return response()->json(['data' => $districts], 200);
    }
}