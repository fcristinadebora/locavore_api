<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GrowerUser;
use App\Models\Tag;
use App\Models\Image;

class GrowersController extends Controller
{
    public function show(Request $request, $id){
        try {
            $growerUser = GrowerUser::find($id);

            if($request->get('with_tags') == true){
                $growerUser->load('identificationTags.tag');
            }

            return response()->json(['grower' => $growerUser], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function update(Request $request, $id){
        $request['id'] = $id;

        $data = $this->validate($request, [
            'id' => 'required|numeric|exists:users,id',
            'name' => 'required|string',
            'email' => "required|email|unique:users,email,$id",
            'description' => 'nullable|string',
            'is_grower' => 'required|boolean',
            'tags' => 'nullable|array',
            'profile_file_name' => 'nullable|string',
            'profile_file_path' => 'nullable|string',
            'image_id' => 'nullable|numeric|exists:images,id'
        ], [
            'email.unique' => 'O e-mail informado já está em uso!'
        ]);
        
        try {
            $growerUser = GrowerUser::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Grower not found'], 404);
        }

        try {
            $growerUser->name = $data['name'];
            $growerUser->email = $data['email'];
            $growerUser->description = $data['description'];
            $growerUser->is_grower = $data['is_grower'];
            if(isset($data['image_id'])){
                $growerUser->profile_file_name = $data['profile_file_name'];
                $growerUser->profile_file_path = $data['profile_file_path'];
                Image::where('id', $data['image_id'])->delete();
            }
            $growerUser->save();

            $growerUser->identificationTags()->delete();
            $growerUser->identificationTags()->createMany($this->mapTags($data['tags']));

            return response()->json(['updated' => $growerUser], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    private function mapTags($tags){
        $newTags = array_map(function ($tag) {
            if(isset($tag['type']) && $tag['type'] == 'new'){
                $created = Tag::create([
                    'description' => $tag['description']
                ]);
                
                return ['tag_id' => $created['id']];
            }

            return ['tag_id' => $tag['id']];
        }, $tags);

        return $newTags;
    }
}
