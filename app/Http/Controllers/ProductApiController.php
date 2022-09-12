<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest('id')->get();
        // return response()->json($products);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        sleep(3);
        $request->validate([
            "name" => "required|min:3|max:50",
            "price" => "required|numeric|min:1",
            "stock" => "required|numeric|min:1",
            "photos" => 'required',
            "photos.*" => "file|mimes:jpeg,png|max:512"
        ]);
        $product = Product::create([
            "name" => $request->name,
            "price" => $request->price,
            "stock" => $request->stock,
            "user_id" => Auth::id(),
        ]);
        $savePhotos = [];
        foreach($request->photos as $key=>$photo){
            $newN = uniqid()."-photo-".$photo->getClientOriginalName();
            $photo->storeAs("public",$newN);
            $savePhotos[$key] = new Photo(["name"=>$newN]);
        }
        $product->photos()->saveMany($savePhotos);
        return response()->json([
            "message" => "success",
            "success" => true,
            "product" => new ProductResource($product)
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(["message" => "Product not found"], 404);
        }
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(["message" => "Product not found"], 404);
        }

        $request->validate([
            "name" => "nullable|min:3|max:50",
            "price" => "nullable|numeric|min:1",
            "stock" => "nullable|numeric|min:1"
        ]);
        if ($request->name) {
            $product->name = $request->name;
        }
        if ($request->price) {
            $product->price = $request->price;
        }
        if ($request->stock) {
            $product->stock = $request->stock;
        }
        // $product->name = $request->name;
        // $product->price = $request->price;
        // $product->stock = $request->stock;

        $product->update();
        return response()->json(["message" => "Update Successful"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(["message" => "Product not found"], 404);
        }
        $product->delete();
        return response()->json(["message" => "Product is deleted"], 204);
    }
}
