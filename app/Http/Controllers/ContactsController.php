<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Contact;

class ContactsController extends Controller
{
  private $rules = [
    'address_id' => 'required|numeric|exists:addresses,id',
    'value' => 'required|string',
    'type' => 'required|string|in:phone,email,whatsapp,other'
  ];

  public function create(Request $request)
  {
    $data = $this->validate($request, $this->rules);

    try {
      $created = Contact::create($data);

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
      $item = Contact::find($id);
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
      $items = Contact::select('*');

      if ($request->get('address_id')) {
        $items = $items->where('address_id', $request->get('address_id'));
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
      $item = Contact::find($id);
      
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
    $this->rules['id'] = 'required|numeric|exists:contacts,id';

    $data = $this->validate($request, $this->rules);

    try {
      $item = Contact::find($id);
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
