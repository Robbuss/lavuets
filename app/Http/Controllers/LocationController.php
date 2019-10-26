<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Location::all();
    }

    /**
     * Create a new Location
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $location = Location::create([
            'name' => $request->name,
            'facility_name' => $request->facility_name,
        ]);
        return ['id' => $location->id];
    }

    public function read(Location $location)
    {
        return $location;
    }

    public function update(Location $location, Request $request)
    {
        $location->update($request->all());
    }

    public function delete(Location $location)
    {
        $location->delete();
    }
}
