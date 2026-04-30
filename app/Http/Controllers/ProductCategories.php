<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategories extends Controller
{
    // GET /api/product-categories
    public function index()
    {
        return response()->json(ProductCategory::all());
    }

    // GET /api/product-categories/{id}
    public function show($id)
    {
        $productCategory = ProductCategory::find($id);

        if (!$productCategory) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($productCategory);
    }

    //POST /api/product-categories
    public function store(Request $request)
    {
        $productCategory = ProductCategory::create($request->all());
        return response()->json(['message' => 'Created Successfully!']);
    }

    //PUT /api/product-categories/{id}
    public function update(Request $request, $id)
    {
        $productCategory = ProductCategory::find($id);

        if (!$productCategory) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $productCategory->update($request->all());

        return response()->json(['message' => 'Updated Successfully!']);
    }

    //DELETE /api/product-categories/{id}
    public function destroy($id)
    {
        $productCategory = ProductCategory::find($id);

        if (!$productCategory) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $productCategory->delete();

        return response()->json(['message' => 'Deleted!']);
    }
}
