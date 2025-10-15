<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Resources\Fron\SingleProductResource;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::withoutGlobalScope('store')->filter($request->query())->paginate(15);


        return Helper::sendSuccess('', $products);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $product = Product::withoutGlobalScope('store')->with(['category', 'store', 'tags'])->where('slug', $slug)->first();
        if (!$product) {
            return Helper::sendError('No product with this Name', [], 404);
        }
        $product['sameProds'] = Product::where('category_id', $product->category_id)->limit(8)->get();
        return Helper::sendSuccess('', new SingleProductResource($product), 200);
    }
}
