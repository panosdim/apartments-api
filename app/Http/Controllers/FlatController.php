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
            'name'    => 'required|string',
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
     * @return CategoryResource
     */
    public function show(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        // Check if currently authenticated user is the owner of the category
        if ($request->auth->id != $category->user_id) {
            return response()->json(['error' => 'You can only view your own categories.'], 403);
        }

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  $id
     * @return CategoryResource
     */
    public function update(Request $request, $id)
    {
        // Check if currently authenticated user is the owner of the category
        $category = Category::findOrFail($id);
        if ($request->auth->id != $category->user_id) {
            return response()->json(['error' => 'You can only edit your own categories.'], 403);
        }

        if ($request->has("category")) {
            $exist = Category::where("category", $request->category)->where("user_id", $request->auth->id)->first();
            if (!empty($exist)) {
                return response()->json(['error' => 'Category with same name already exist'], 422);
            } else {
                $category->category = $request->category;
            }
        }

        if ($request->has("count")) {
            $category->count = $request->count;
        }

        $category->save();

        return new CategoryResource($category);
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
        // Check if currently authenticated user is the owner of the category
        $category = Category::findOrFail($id);
        if ($request->auth->id != $category->user_id) {
            return response()->json(['error' => 'You can only delete your own category.'], 403);
        }

        // Check if category is connected with expenses
        $expense = Expense::where("category", $id)->where("user_id", $request->auth->id)->first();
        if (!empty($expense)) {
            return response()->json([
                'error' => "Category is connected with one or more Expense and can't be deleted.",
            ], 409);
        }

        $category->delete();

        return response()->json(null, 204);
    }

    public function __construct()
    {
    }
}
