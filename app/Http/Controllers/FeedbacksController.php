<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Feedback;

class FeedbacksController extends Controller
{
    public function create(Request $request){
        $data = $this->validate($request, [
          'user_id' => 'nullable|numeric|exists:users,id',
          'name' => 'nullable|string',
          'email' => 'nullable|string',
          'description' => 'required|string'
        ]);

            $created = Feedback::create($data);

            return response()->json(['created' => $created], 200);

    }
}
