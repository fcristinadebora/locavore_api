<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Rating;
use App\Models\ProductRating;
use App\Models\RatingUser;
use Illuminate\Support\Facades\DB;

class RatingsController extends Controller
{
  public function create(Request $request){
    $rules = [
      'user_id' => 'nullable|numeric|exists:users,id',
      'rated_by' => 'nullable|numeric|exists:users,id',
      'product_id' => 'nullable|numeric|exists:products,id',
      'rating' => 'required|numeric',
      'description' => 'nullable|string'
    ];

    if(!$request->input('product_id')){
      $rules['user_id'] = 'nullable|numeric|exists:users,id';
    }else if(!$request->input('user_id')){
      $rules['product_id'] = 'nullable|numeric|exists:products,id';
    }

    $data = $this->validate($request, $rules);

    $created = Rating::create([
      'rating' => $data['rating'],
      'rated_by' => $data['rated_by'],
      'description' => $data['description']
    ]);

    if(isset($data['product_id'])){
      $created->productRating()->create([
        'product_id' => $data['product_id']
      ]);
    }else if(isset($data['user_id'])){
      $created->ratingUser()->create([
        'user_id' => $data['user_id']
      ]);
    }

    return response()->json(['created' => $created], 200);
  }

  public function get(Request $request) {
    if($request->get('product_id')){
      $items = ProductRating::where('product_id', $request->get('product_id'));
    }

    if($request->get('user_id')){
      $items = RatingUser::where('user_id', $request->get('user_id'));
    }

    $per_page = 15;
    if($request->get('per_page')){
      $per_page = $request->get('per_page');
    }

    $items = $items->with('rating.rater')->orderBy('id', 'desc')->paginate($per_page);

    return response()->json($items, 200);
  }

  public function avg(Request $request) {
    if($request->get('product_id')){
      $items = ProductRating::where('product_id', $request->get('product_id'))
        ->join('ratings', 'ratings.id', '=', 'product_rating.rating_id');
    }

    if($request->get('user_id')){
      $items = RatingUser::where('user_id', $request->get('user_id'))
        ->join('ratings', 'ratings.id', '=', 'rating_user.rating_id');
    }

    $items = $items->select(DB::raw('avg(rating) as avg'))->first();

    return response()->json($items, 200);
  }
}
