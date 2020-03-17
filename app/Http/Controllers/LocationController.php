<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        return Location::with('media')->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'facility_name' => $q->facility_name,
                'name' => $q->name,
                'image' => ($q->media->last()) ? $q->media->last()->getUrl() : ''
            ];
        });
    }

    /**
     * Create a new Location
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $location = Location::create([
            'customer_id' => Customer::current()->id,
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
        $location->update($request->except('image'));
        $location->associateMedia('image', $request->image, ['png', 'jpg'], [], 'public');
    }

    public function delete(Location $location)
    {
        // check if a location has units:
        if ($location->units->count() !== 0) {
            return ["success" => false, "error" => "location_has_units"];
        }
        $location->delete();
        return ["success" => true];
    }
}
