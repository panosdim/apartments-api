<?php

namespace App\Http\Controllers;

use App\Flat;
use App\Http\Resources\FlatResource;
use Illuminate\Http\Request;

class FlatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return FlatResourceCollection
     */
    public function index(Request $request)
    {
        return FlatResource::collection(Flat::where("user_id", $request->auth->id)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return FlatResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required|string|unique:flats,name',
            'address' => 'required|string',
            'floor'   => 'required|numeric',
        ]);

        $flat = Flat::create([
            'user_id' => $request->auth->id,
            'name'    => $request->name,
            'address' => $request->address,
            'floor'   => $request->floor,

        ]);

        return new FlatResource($flat);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return FlatResource
     */
    public function show(Request $request, $id)
    {
        $flat = Flat::findOrFail($id);
        // Check if currently authenticated user is the owner of the flat
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only view your own flats.'], 403);
        }

        return new FlatResource($flat);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return FlatResource
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'    => 'string|unique:flats,name',
            'address' => 'string',
            'floor'   => 'numeric',
        ]);

        // Check if currently authenticated user is the owner of the flat
        $flat = Flat::findOrFail($id);
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only edit your own flats.'], 403);
        }

        if ($request->has("name")) {
            $flat->name = $request->name;
        }

        if ($request->has("address")) {
            $flat->address = $request->address;
        }

        if ($request->has("floor")) {
            $flat->floor = $request->floor;
        }

        $flat->save();

        return new FlatResource($flat);
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
        // Check if currently authenticated user is the owner of the flat
        $flat = Flat::findOrFail($id);
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only delete your own flat.'], 403);
        }

        $flat->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
        FlatResource::withoutWrapping();
    }
}
