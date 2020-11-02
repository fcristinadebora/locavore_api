<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Tag;
use App\Models\TagUserInterested;
use App\Models\Product;

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
}
