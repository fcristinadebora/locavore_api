<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Address;

class AddressesController extends Controller
{
  private $rules = [
    'street' => 'required|string',
    'number' => 'nullable|string',
    'district' => 'required|string',
    'city' => 'required|string',
    'state' => 'required|string',
    'country' => 'required|string',
    'complement' => 'nullable|string',
    'lat' => 'required|numeric',
    'long' => 'required|numeric',
    'name' => 'required|string',
    'postal_code' => 'required|string|max:9',
    'user_id' => 'required|numeric|exists:users,id'
  ];

  public function create(Request $request)
  {
    $data = $this->validate($request, $this->rules);

    try {
      $created = Address::create($data);

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
      $item = Address::find($id);
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
      $items = Address::select('*');

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

  public function show(Request $request, $id)
  {
    try {
      $item = Address::find($id);
      
      return response()->json($item, 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ], 500);
    }
  }

  public function update(Request $request, $id)
  {
    $this->rules['id'] = 'required|numeric|exists:addresses,id';

    $data = $this->validate($request, $this->rules);

    try {
      $item = Address::find($id);
      $item->update($data);
      
      return response()->json(['updated' => $item], 200);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ], 500);
    }
  }
}
