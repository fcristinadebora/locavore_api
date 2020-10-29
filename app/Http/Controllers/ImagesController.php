<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Image;

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
}
