<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\ProductCategory;

class ProductCategoriesController extends Controller
{
    public function get(Request $request){
        try {
            $categories = ProductCategory::get();

            return response()->json(['categories' => $categories], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }
}
