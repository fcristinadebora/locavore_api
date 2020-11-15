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
                ])
                ->whereHas('favoriteUser.addresses', function ($query) use ($request) {
                    if ($request->get('lat') && $request->get('long') && $request->get('max_distance')) {
                        $lat = $request->get('lat');
                        $long = $request->get('long');

                        $distanceStatement = "(6371 *
                            acos(cos(radians($lat)) * 
                            cos(radians(addresses.lat)) * 
                            cos(radians(addresses.long) - 
                            radians($long)) + 
                            sin(radians($lat)) * 
                            sin(radians(addresses.lat))))";
                        
                        $query->whereRaw("$distanceStatement <= " . $request->get('max_distance'));
                    }
                });

            if ($request->get('ignorable_ids') && count($request->get('ignorable_ids')) > 0) {
                $items = $items->whereNotIn('id', $request->get('ignorable_ids'));
            }

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
