<?php

namespace App\Http\Controllers;

use App\Flat;
use App\Balance;
use Illuminate\Http\Request;
use App\Http\Resources\BalanceResource;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return BalanceResourceCollection
     */
    public function index(Request $request)
    {
        // Get balance for selected flat
        return BalanceResource::collection(Balance::whereIn(
            'flat_id',
            Flat::where('user_id', $request->auth->id)->pluck('id')->toArray()
        )->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return BalanceResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date'    => 'required|date|date_format:Y-m-d',
            'amount'  => 'required|numeric',
            'comment' => 'required|string',
            'flat_id' => 'required|numeric|exists:flats,id',
        ]);

        $balance = Balance::create([
            'date'    => $request->date,
            'amount'  => $request->amount,
            'comment' => $request->comment,
            'flat_id' => $request->flat_id,
        ]);

        return new BalanceResource($balance);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return BalanceResource
     */
    public function show(Request $request, $id)
    {
        $balance = Balance::findOrFail($id);
        $flat = Flat::where('id', $balance->flat_id)->first();
        // Check if currently authenticated user is the owner of the balance
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only view balance of your own flats.'], 403);
        }

        return new BalanceResource($balance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return BalanceResource
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date'    => 'date|date_format:Y-m-d',
            'amount'  => 'numeric',
            'comment' => 'string',
        ]);

        $balance = Balance::findOrFail($id);
        $flat = Flat::where('id', $balance->flat_id)->first();
        // Check if currently authenticated user is the owner of the lessee
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only update balance of your own flats.'], 403);
        }

        if ($request->has('date')) {
            $balance->date = $request->date;
        }

        if ($request->has('amount')) {
            $balance->amount = $request->amount;
        }

        if ($request->has('comment')) {
            $balance->comment = $request->comment;
        }

        $balance->save();

        return new BalanceResource($balance);
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
        $balance = Balance::findOrFail($id);
        $flat = Flat::where('id', $balance->flat_id)->first();
        // Check if currently authenticated user is the owner of the lessee
        if ($request->auth->id != $flat->user_id) {
            return response()->json(['error' => 'You can only delete balance of your own flats.'], 403);
        }

        $balance->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
        BalanceResource::withoutWrapping();
    }
}
