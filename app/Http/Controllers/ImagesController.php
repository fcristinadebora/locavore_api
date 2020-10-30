<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\File;

class ImagesController extends Controller
{
    public function create(Request $request){
        $data = $this->validate($request, [
            'file' => 'image|required'
        ]);
        
        try {
            $original_filename = $request->file('file')->getClientOriginalName();

            $original_filename_arr = explode('.', $original_filename);

            $file_ext = end($original_filename_arr);

            $destination_path = './uploads';
            $image = time() . '.' . $file_ext;

            $request->file('file')->move($destination_path, $image);

            $created = Image::create([
                'file_name' => $image,
                'file_path' => 'uploads/'
            ]);
                
            return response()->json(['image' => $created], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function createMultiple(Request $request){
        $data = $this->validate($request, [
            'file' => 'required',
            'file.*' => 'image|required'
        ]);
        
        try {
            $images = [];
            $i = 0;
            foreach($request->file('file') as $file){
                $original_filename = $file->getClientOriginalName();
    
                $original_filename_arr = explode('.', $original_filename);
    
                $file_ext = end($original_filename_arr);
    
                $destination_path = './uploads';
                $image = time() . $i++ . '.' . $file_ext;
    
                $file->move($destination_path, $image);
    
                $created = Image::create([
                    'file_name' => $image,
                    'file_path' => 'uploads/'
                ]);

                $images[] = $created;
            }
                
            return response()->json(['images' => $images], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function update(Request $request, $id){
        $data = $this->validate($request, [
            "title" => "nullable|string"
        ]);
        
        try {
            $item = Image::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        try {
            $item->title = isset($data['title']) && !empty($data['title']) ? $data['title'] : null;
            $item->save();

            return response()->json(['updated' => $item], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function delete(Request $request, $id){
        try {
            $item = Image::find($id);
            $item->delete();

            File::delete($item->file_path . $item->file_name);

            return response()->json(['deleted' => true], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }
}
