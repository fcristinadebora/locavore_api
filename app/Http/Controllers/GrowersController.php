<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Libraries\StringHelper;
use App\Models\GrowerUser;
use App\Models\Tag;
use App\Models\Image;

class GrowersController extends Controller
{
    public function get(Request $request)
    {
        // try {
        $growers = GrowerUser::select('users.id','users.name','users.profile_file_name','users.profile_file_path')
            ->with(['productCategories.productCategory', 'identificationTags.tag'])
            ->where('is_grower', true)
            ->with([
                'addresses',
                'images' => function ($query) {
                    $query->with('image')->first();
                }
            ])
            ->where(function ($query) use ($request) {
                if ($request->get('search_string')) {
                    $searchWords = StringHelper::WordsExplode($request->get('search_string'));
                    $query->where(function ($query) use ($searchWords) {
                        foreach ($searchWords as $word) {
                            $word = $word[0];
                            $query->orWhere('users.name', 'like', "%$word%")
                                ->orWhereHas('identificationTags', function ($query) use ($word) {
                                    $query->whereHas('tag', function ($query) use ($word) {
                                        $query->where('description', 'like', "%$word%");
                                    });
                                });
                        }
                    });
                }
            })->join('addresses', 'addresses.user_id', '=', 'users.id');

        if ($request->get('city')) {
            $growers = $growers->where('addresses.city', $request->get('city'));
        }
        if ($request->get('state')) {
            $growers = $growers->where('addresses.state', $request->get('state'));
        }

        if ($request->get('lat') && $request->get('long')) {
            $lat = $request->get('lat');
            $long = $request->get('long');

            $growers = $growers->addSelect(DB::raw("(
                            6371 *
                            acos(cos(radians($lat)) * 
                            cos(radians(addresses.lat)) * 
                            cos(radians(addresses.long) - 
                            radians($long)) + 
                            sin(radians($lat)) * 
                            sin(radians(addresses.lat)))
                        ) AS distance "));

            if ($request->get('order_by') == 'distance') {
                $growers = $growers->orderBy('distance', 'asc');
            }
        } else {
            $growers = $growers->addSelect(DB::raw("null AS distance"));
        }

        if ($request->get('order_by') == 'distance') {
            $growers->addSelect("addresses.street", "addresses.number", "addresses.district", "addresses.number", "addresses.name as addr_name", "addresses.lat", "addresses.long");
            $growers = $growers->orderBy('users.name', 'desc');
        } else {
            $growers = $growers->orderBy('users.id', 'desc');
        }

        $growers = $growers->paginate();

        return response()->json($growers, 200);
        // } catch (\Exception $th) {
        //     response()->json(['error' => $th->getTrace()], 500);
        // }
    }

    public function show(Request $request, $id){
        $growerUser = GrowerUser::find($id);

        if($request->get('with_tags') == true){
            $growerUser->load('identificationTags.tag');
        }

        if($request->get('with_images') == true){
            $growerUser->load('images.image');
        }

        if($request->get('with_categories') == true){
            $growerUser->load('productCategories.productCategory');
        }

        if($request->get('with_address') == true){
            $growerUser->load('addresses.contacts');
        }

        return response()->json(['grower' => $growerUser], 200);
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
            'categories' => 'nullable|array',
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

            $growerUser->productCategories()->delete();
            $growerUser->productCategories()->createMany($this->mapCategories($data['categories']));

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

    private function mapCategories($categories){
        $newCategories = array_map(function ($category) {
            return ['product_category_id' => $category];
        }, $categories);

        return $newCategories;
    }

    public function attachImage(Request $request, $id){
        $data = $this->validate($request, [
            'images' => 'required|array',
            'images.*' => 'required|numeric|exists:images,id'
        ]);

        try {
            $product = GrowerUser::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'GrowerUser not found'], 404);
        }

        try {
            $images_create = [];
            foreach ($data['images'] as $value) {
                $images_create[] = [
                    'product_id' => $id,
                    'image_id' => $value,
                ];
            }
            $product->images()->createMany($images_create);

            return response()->json(['success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'trace' => $th->getTrace()
            ], 500);
        }
    }

    public function getImages(Request $request, $id){
        try {
            $product = GrowerUser::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'GrowerUser not found'], 404);
        }

        try {
            $product->load('images.image');

            return response()->json(['images' => $product->images], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'trace' => $th->getTrace()
            ], 500);
        }
    }
}
