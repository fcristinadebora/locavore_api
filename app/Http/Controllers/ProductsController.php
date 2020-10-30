<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Tag;

class ProductsController extends Controller
{
    private $rules = [
        'name' => 'required|string',
        'description' => 'nullable|string',
        'price' => "nullable|numeric",
        'user_id' => 'required|numeric|exists:users,id',
        'product_category_id' => 'required|numeric|exists:product_categories,id',
        'tags' => 'nullable|array'
    ];

    public function get(Request $request){
        try {
            $products = Product::where(function ($query) use ($request) {
                if($request->get('user_id')){
                    $query->where('user_id', $request->get('user_id'));
                }
            })->orderBy('id', 'desc')->paginate();

            return response()->json($products, 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function show(Request $request, $id){
        try {
            $product = Product::find($id);

            if($request->get('with_tags') == true){
                $product->load('tags.tag');
            }

            return response()->json(['product' => $product], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function delete(Request $request, $id){
        try {
            $product = Product::find($id);
            $product->delete();

            return response()->json(['deleted' => true], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function create(Request $request){
        $data = $this->validate($request, $this->rules);
        
        try {
            $created = Product::create($data);

            $created->tags()->createMany($this->mapTags($data['tags']));

            return response()->json(['created' => $created], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function update(Request $request, $id){
        $request['id'] = $id;

        $this->rules['id'] = 'required|numeric|exists:products,id';

        $data = $this->validate($request, $this->rules);
        
        try {
            $product = Product::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        try {
            $product->name = $data['name'];
            $product->description = isset($data['description']) && !empty($data['description']) ? $data['description'] : null;
            $product->price = isset($data['price']) && !empty($data['price']) ? $data['price'] : null;
            $product->product_category_id = $data['product_category_id'];
            $product->save();

            $product->tags()->delete();
            $product->tags()->createMany($this->mapTags($data['tags']));

            return response()->json(['updated' => $product], 200);
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

    public function attachImage(Request $request, $id){
        $data = $this->validate($request, [
            'images' => 'required|array',
            'images.*' => 'required|numeric|exists:images,id'
        ]);

        try {
            $product = Product::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Product not found'], 404);
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
            $product = Product::find($id);
        }catch (\Throwable $th) {
            return response()->json(['message' => 'Product not found'], 404);
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
