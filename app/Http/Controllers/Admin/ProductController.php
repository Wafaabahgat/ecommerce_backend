<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\ProductRequest;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\Store;
use App\Models\Admin\Tag;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('tags')->filter($request->query())->paginate(10);

        return Helper::sendSuccess('', $products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $slug = Str::slug($request->post('name'));
        $image = $this->uploadImg($request, 'products');

        $product = Product::create([
            'name' => $request->post('name'),
            'slug' => $slug,
            'image' => $image,
            'disc' => $request->post('disc'),
            'price' => $request->post('price'),
            'compare_price' => $request->post('compare_price'),
            'rating' => $request->post('rating'),
            'options' => $request->post('options'),
            'type' => $request->post('type'),
            'store_id' => $request->post('store_id'),
            'category_id' => $request->post('category_id'),
        ]);

        $tags = explode(',', $request->post('tags'));
        $tag_ids = [];

        foreach ($tags as $item) {
            $slug = Str::slug($item);
            $tag = Tag::where('slug', $slug)->first();
            if (!$tag) {
                $tag = Tag::create([
                    'name' => $item,
                    'slug' => $slug,
                ]);
            }
            $tag_ids[] = $tag->id;
        }

        $product->tags()->sync($tag_ids);

        return Helper::sendSuccess('Product created successfully', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($product)
    {
        $product = Product::with(['category', 'store', 'tags'])->find($product);
        if (!$product) {
            return Helper::sendError('No product with this #ID', [], 404);
        }
        $categories  = Category::all();
        $stores  = Store::all();
        $product['categories'] = $categories;
        $product['stores'] = $stores;
        return Helper::sendSuccess('', $product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $product)
    {
        $product = Product::find($product);
        if (!$product) {
            return Helper::sendError('No product with this #ID', [], 404);
        }

        $slug = Str::slug($request->post('name'));
        $image = $this->uploadImg($request, 'products');
        $old_path = $product->image;

        $product->update([
            'name' => $request->post('name') ?? $product->name,
            'slug' => $slug ?? $product->slug,
            'image' => $image ?? $product->image,
            'disc' => $request->post('disc') ?? $product->disc,
            'price' => $request->post('price') ?? $product->price,
            'compare_price' => $request->post('compare_price') ?? $product->compare_price,
            'rating' => $request->post('rating') ?? $product->rating,
            'options' => $request->post('options') ?? $product->options,
            'type' => $request->post('type') ?? $product->type,
            'status' => $request->post('status') ?? $product->status,
            'store_id' => $request->post('store_id') ?? $product->store_id,
            'category_id' => $request->post('category_id') ?? $product->category_id,
        ]);

        $tags = explode(',', $request->post('tags'));
        $tag_ids = [];

        foreach ($tags as $key => $item) {
            $slug = Str::slug($item);
            $tag = Tag::where('slug', $slug)->first();
            if (!$tag) {
                $tag = Tag::create([
                    'name' => $item,
                    'slug' => $slug,
                ]);
            }
            $tag_ids[] = $tag->id;
        }

        $product->tags()->sync($tag_ids);

        if ($old_path && isset($image)) {
            Storage::disk('public')->delete($old_path);
        }

        return Helper::sendSuccess('Product updated successfully', [], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($product)
    {
        $product = Product::find($product);
        if (!$product) {
            return Helper::sendError('No product with this #ID', [], 404);
        }

        $product->forceDelete();

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return Helper::sendSuccess('Product deleted successfully', [], 200);
    }
}
