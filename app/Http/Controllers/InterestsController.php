<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Tag;
use App\Models\TagUserInterested;
use App\Models\Product;
use App\Models\User;

class InterestsController extends Controller
{
  public function create(Request $request)
  {
    $data = $this->validate($request, [
      'user_id' => 'required|numeric|exists:users,id',
      'tag' => 'required|array'
    ]);

    try {
      $tag_id = $data['tag']['id'];
      if (isset($data['tag']['type']) && $data['tag']['type'] == 'new') {
        $tag = Tag::create([
          'description' => $data['tag']['description']
        ]);

        $tag_id = $tag->id;
      }

      $created = TagUserInterested::create([
        'user_id' => $data['user_id'],
        'tag_id' => $tag_id,
      ]);

      return response()->json(['created' => $created], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ], 500);
    }
  }

  public function delete(Request $request, $id)
  {
    try {
      $item = TagUserInterested::find($id);
      $item->delete();

      return response()->json(['deleted' => true], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ], 500);
    }
  }

  public function get(Request $request)
  {
    try {
      $items = TagUserInterested::with('tag');

      if ($request->get('user_id')) {
        $items = $items->where('user_id', $request->get('user_id'));
      }

      $items = $items->paginate();

      return response()->json($items, 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ], 500);
    }
  }

  public function getCompatible(Request $request)
  {
    $user = User::with(['addresses' => function ($query) use ($request) {
      $query->where('is_main', true);
    }])->find($request->get('user_id'));

      $interests = TagUserInterested::select('tag_id')
        ->where('user_id', $request->get('user_id'))
        ->with(['tag.products' => function ($query) use ($user) {
          $query->with('product.grower.addresses');
          $query->with('product.tags.tag');
          $query->with('product.images.image');

          $query->whereHas('product.grower');

          if(count($user->addresses)){
            $query->whereHas('product.grower.addresses', function ($query) use ($user){
              $query->where('city',$user->addresses[0]->city);
            });
          }

          $query->orderByRaw('RAND()')->limit(6);
        }])
        ->with(['tag.growers.grower' => function ($query) use ($user) {
          if(count($user->addresses)){
            $query->whereHas('addresses', function ($query) use ($user){
              $query->where('city',$user->addresses[0]->city);
            });
          }

          $query->with('identificationTags.tag');
          $query->with('addresses');
          $query->with('images.image');
          
          $query->orderByRaw('RAND()')->limit(6);
        }])
        ->get();     
      
      $data = [
        'products' => $interests->map(function ($item) {
          return $item->tag->products->map(function ($prod) {
            return $prod->product;
          });
        }),
        'growers' => $interests->map(function ($item) {
          return $item->tag->growers->map(function ($prod) {
            return $prod->grower;
          });
        }),
      ];

      return response()->json(['items' => $data], 200);
  }
}
