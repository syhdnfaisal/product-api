<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */

    // GET /api/products
    public function index()
    {
        return Product::with('category:id,name,is_active')->get();
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return Product::with('category:id,name,is_active')->find($id);
    }

    //POST /api/products
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'barcode' => 'required',
            'is_active' => 'required|boolean',
            'category_id' => 'nullable|exists:product_categories,id'
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Created Successfully!',
            'data' => $product
        ]);
    }

    //PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $product->update($request->all());

        return response()->json(['message' => 'Updated Successfully!']);
    }

    //DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted!']);
    }
}
