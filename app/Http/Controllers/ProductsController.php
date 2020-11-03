<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;
use App\Models\Tag;
use App\Libraries\StringHelper;

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

    public function get(Request $request)
    {
        // try {
        $products = Product::select('products.id','products.name','products.description','products.price', 'products.user_id', 'products.product_category_id')
            ->with('productCategory')
            ->with([
                'grower.addresses',
                'tags.tag',
                'images' => function ($query) {
                    $query->with('image')->orderBy('id', 'asc')->first();
                }
            ])
            ->where(function ($query) use ($request) {
                if ($request->get('user_id')) {
                    $query->where('products.user_id', $request->get('user_id'));
                }

                if ($request->get('search_string')) {
                    $searchWords = StringHelper::WordsExplode($request->get('search_string'));
                    $query->where(function ($query) use ($searchWords) {
                        foreach ($searchWords as $word) {
                            $word = $word[0];
                            $query->orWhere('products.name', 'like', "%$word%")
                                ->orWhereHas('tags', function ($query) use ($word) {
                                    $query->whereHas('tag', function ($query) use ($word) {
                                        $query->where('description', 'like', "%$word%");
                                    });
                                });
                        }
                    });
                }
            });

        if($request->get('unique')){
            $products = $products->leftJoin('users', function ($join) {
                $join->on('products.user_id', '=', 'users.id')
                    ->where('users.is_grower', TRUE);
            })->leftJoin('addresses', 'addresses.user_id', '=', 'users.id');
        }else{
            $products = $products->join('users', function ($join) {
                $join->on('products.user_id', '=', 'users.id')
                    ->where('users.is_grower', TRUE);
            })->join('addresses', 'addresses.user_id', '=', 'users.id');
        }

        if ($request->get('city')) {
            $products = $products->where('addresses.city', $request->get('city'));
        }
        if ($request->get('state')) {
            $products = $products->where('addresses.state', $request->get('state'));
        }

        if ($request->get('lat') && $request->get('long')) {
            $lat = $request->get('lat');
            $long = $request->get('long');

            $products = $products->addSelect(DB::raw("(
                            6371 *
                            acos(cos(radians($lat)) * 
                            cos(radians(addresses.lat)) * 
                            cos(radians(addresses.long) - 
                            radians($long)) + 
                            sin(radians($lat)) * 
                            sin(radians(addresses.lat)))
                        ) AS distance "));

            if ($request->get('order_by') == 'distance') {
                $products = $products->orderBy('distance', 'asc');
            }
        } else {
            $products = $products->addSelect(DB::raw("null AS distance"));
        }

        if ($request->get('order_by') == 'distance') {
            $products->addSelect("addresses.street", "addresses.number", "addresses.district", "addresses.city", "addresses.state", "addresses.complement", "addresses.number", "addresses.name as addr_name", "addresses.lat", "addresses.long");
            $products = $products->orderBy('products.name', 'desc');
        } else {
            $products = $products->orderBy('products.id', 'desc');
        }

        if($request->get('unique') == true){
            $products = $products->groupBy(
                'id',
                'distance',
                'name',
                'price',
                'product_category_id',
                'user_id',
                'description'
            );
        }

        $products = $products->paginate();

        return response()->json($products, 200);
        // } catch (\Exception $th) {
        //     response()->json(['error' => $th->getTrace()], 500);
        // }
    }

    public function show(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if ($request->get('with_tags') == true) {
                $product->load('tags.tag');
            }
            
            if ($request->get('with_category') == true) {
                $product->load('productCategory');
            }

            if ($request->get('with_images') == true) {
                $product->load('images.image');
            }

            if ($request->get('with_grower') == true) {
                $product->load('grower');
            }

            return response()->json(['product' => $product], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            $product->delete();

            return response()->json(['deleted' => true], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function create(Request $request)
    {
        $data = $this->validate($request, $this->rules);

        try {
            $created = Product::create($data);

            $created->tags()->createMany($this->mapTags($data['tags']));

            return response()->json(['created' => $created], 200);
        } catch (\Throwable $th) {
            response()->json(['error' => $th->getTrace()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request['id'] = $id;

        $this->rules['id'] = 'required|numeric|exists:products,id';

        $data = $this->validate($request, $this->rules);

        try {
            $product = Product::find($id);
        } catch (\Throwable $th) {
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

    private function mapTags($tags)
    {
        $newTags = array_map(function ($tag) {
            if (isset($tag['type']) && $tag['type'] == 'new') {
                $created = Tag::create([
                    'description' => $tag['description']
                ]);

                return ['tag_id' => $created['id']];
            }

            return ['tag_id' => $tag['id']];
        }, $tags);

        return $newTags;
    }

    public function attachImage(Request $request, $id)
    {
        $data = $this->validate($request, [
            'images' => 'required|array',
            'images.*' => 'required|numeric|exists:images,id'
        ]);

        try {
            $product = Product::find($id);
        } catch (\Throwable $th) {
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

    public function getImages(Request $request, $id)
    {
        try {
            $product = Product::find($id);
        } catch (\Throwable $th) {
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
