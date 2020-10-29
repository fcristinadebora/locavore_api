<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Image;

class UsersController extends Controller
{
    public function profile(Request $request){
        return response()->json(['profile' => $request->auth], 200);
    }

    public function update(Request $request, $id){
        $request['id'] = $id;

        $data = $this->validate($request, [
            'id' => 'required|numeric|exists:users,id',
            'name' => 'required|string',
            'email' => "required|email|unique:users,email,$id",
            'is_grower' => "required|boolean",
            'profile_file_name' => 'nullable|string',
            'profile_file_path' => 'nullable|string',
            'image_id' => 'nullable|numeric|exists:images,id'
        ], [
            'email.unique' => 'O e-mail informado já está em uso!'
        ]);
        
        try {
            $growerUser = User::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Grower not found'], 404);
        }

        try {
            $growerUser->name = $data['name'];
            $growerUser->email = $data['email'];
            $growerUser->is_grower = $data['is_grower'];
            if(isset($data['image_id'])){
                $growerUser->profile_file_name = $data['profile_file_name'];
                $growerUser->profile_file_path = $data['profile_file_path'];
                Image::where('id', $data['image_id'])->delete();
            }
            $growerUser->save();

            return response()->json(['updated' => $growerUser], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }
}
