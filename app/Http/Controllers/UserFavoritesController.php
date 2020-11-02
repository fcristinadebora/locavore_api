<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\UserFavorite;

class UserFavoritesController extends Controller
{
    public function get(Request $request){
            $items = UserFavorite::where('user_id', $request->get('user_id'))->with([
                'favoriteUser' => function ($query) use ($request) {
                    if($request->get('with_address')){
                        $query->with('addresses');
                    }
        
                    if($request->get('with_categories')){
                        $query->with('productCategories.productCategory');
                    }
                }
            ]);

            if($request->get('paginated')){
                $items = $items->paginate();
            }else{
                $items = $items->get();
            }

            return response()->json($items, 200);
    }

    public function create(Request $request){
        $data = $this->validate($request, [
            'user_id' => 'required|numeric|exists:users,id',
            'favorite_user_id' => 'required|numeric|exists:users,id'
        ]);

        try {
            $created = UserFavorite::create($data);

            return response()->json(['created' => $created], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function delete(Request $request, $id){
        try {
            $item = UserFavorite::find($id);
            $item->delete();

            return response()->json(['item' => $item], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }
}
