<?php

namespace App\Http\Controllers;

use App\Flat;
use App\Http\Resources\LesseeResource;
use App\Lessee;
use Illuminate\Http\Request;

class LesseeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LesseeResourceCollection
     */
    public function index(Request $request)
    {
        return LesseeResource::collection(Lessee::whereIn(
            'flat_id',
            Flat::where('user_id', $request->auth->id)->pluck('id')->toArray()
        )->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return LesseeResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|unique:lessees,name',
            'address'     => 'required|string',
            'postal_code' => 'required|string',
            'from'        => 'required|date|date_format:Y-m-d',
            'until'       => 'required|date|date_format:Y-m-d',
            'flat_id'     => 'required|numeric|exists:flats,id',
            'rent'        => 'required|numeric',
            'tin'         => 'required|numeric',
        ]);

        $lessee = Lessee::create([
            'name'        => $request->name,
            'address'     => $request->address,
            'postal_code' => $request->postal_code,
            'from'        => $request->from,
            'until'       => $request->until,
            'flat_id'     => $request->flat_id,
            'rent'        => $request->rent,
            'tin'         => $request->tin,
        ]);

        return new LesseeResource($lessee);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return LesseeResource
     */
    public function show(Request $request, $id)
    {
        $lessee = Lessee::findOrFail($id);
        $flat = Flat::where('id', $lessee->flat_id)->first();
        // Check if currently authenticated user is the owner of the lessee
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only view lessees of your own flats.'], 403);
        }

        return new LesseeResource($lessee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return LesseeResource
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'        => 'string|unique:lessees,name,' . $id,
            'address'     => 'string',
            'postal_code' => 'string',
            'from'        => 'date|date_format:Y-m-d',
            'until'       => 'date|date_format:Y-m-d',
            'rent'        => 'numeric',
            'tin'         => 'numeric'
        ]);

        $lessee = Lessee::findOrFail($id);
        $flat = Flat::where('id', $lessee->flat_id)->first();
        // Check if currently authenticated user is the owner of the lessee
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only update lessees of your own flats.'], 403);
        }

        if ($request->has('name')) {
            $lessee->name = $request->name;
        }

        if ($request->has('address')) {
            $lessee->address = $request->address;
        }

        if ($request->has('postal_code')) {
            $lessee->postal_code = $request->postal_code;
        }

        if ($request->has('from')) {
            $lessee->from = $request->from;
        }

        if ($request->has('until')) {
            $lessee->until = $request->until;
        }

        if ($request->has('rent')) {
            $lessee->rent = $request->rent;
        }

        if ($request->has('tin')) {
            $lessee->tin = $request->tin;
        }

        $lessee->save();

        return new LesseeResource($lessee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $lessee = Lessee::findOrFail($id);
        $flat = Flat::where('id', $lessee->flat_id)->first();
        // Check if currently authenticated user is the owner of the lessee
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only delete lessees of your own flats.'], 403);
        }

        $lessee->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
        LesseeResource::withoutWrapping();
    }
}
