<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Tag;

class TagsController extends Controller
{
    public function get(Request $request){
        try {
            $tags = Tag::get();

            return response()->json(['tags' => $tags], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }
}
