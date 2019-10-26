<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
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

        return Unit::all()->map(function ($q) use ($occupied) {
            return [
                "id" => $q->id,
                "name" => $q->name,
                "size" => $q->size,
                "active" => $q->active,
                "price" => $q->price,
                "free" => $occupied->contains($q) ? false : true,
            ];
        });
    }

    public function read(Unit $unit)
    {
        return ["unit" => $unit, "contracts" => $unit->contracts()->with('tenant')->get()];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Unit::create($request->all());
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
        $unit->update($request->all());
        return ["success" => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Unit $unit)
    {
        $unit->delete();
        return ["success" => true];
    }
}
