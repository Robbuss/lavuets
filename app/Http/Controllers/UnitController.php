<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Customer;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $occupied = Unit::has('contracts')->get();

        return Unit::with(['location', 'media'])->get()->map(function ($q) use ($occupied) {
            return [
                "id" => $q->id,
                "facility_name" => $q->location->facility_name,
                "location_id" => $q->location->id,
                "name" => $q->name,
                "description" => $q->description,
                "size_m3" => $q->size_m3,
                "size_m2" => $q->size_m2,
                "active" => $q->active,
                "price" => $q->price,
                "vat_percentage" => $q->vat_percentage,
                "show_frontend" => $q->show_frontend,
                "should_tax" => $q->should_tax,
                "vat_percentage" => $q->vat_percentage,
                "free" => $occupied->contains($q) ? false : true,
                "image" => $q->thumb
            ];
        });
    }

    public function read(Unit $unit)
    {
        return $unit->load(['contracts.tenant']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $unit = Unit::create(
            array_merge(
                $request->except('image'),
                ['customer_id' => Customer::current()->id]
            ),
        );
        $unit->associateMedia('image', $request->image, ['png', 'jpg'], [], 'public');

        return ["success" => true];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Unit $unit, Request $request)
    {
        $unit->update($request->except('thumb'));
        $unit->associateMedia('thumb', $request->thumb, ['png', 'jpg'], [], 'public');

        return ["success" => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Unit $unit)
    {
        // check if a unit has a contract:
        if ($unit->activeContract->count() !== 0) {
            return ["success" => false, "error" => "still_has_contract"];
        }
        $unit->delete();
        return ["success" => true];
    }
}
