<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Resources\Fron\CaruselResource;
use App\Http\Resources\Fron\CategoryResource;
use App\Http\Resources\Fron\ProductCollectionResource;
use App\Models\Admin\Carusel;
use App\Models\Admin\Category;
use App\Models\Admin\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $carusels = Carusel::limit(4)->get();
        $categories = Category::with('children')->where('parent_id', null)->get();
        $newProd = Product::withoutGlobalScope('store')->where('type', 'new')->limit(8)->get();
        $topProd = Product::withoutGlobalScope('store')->where('type', 'top_rated')->limit(8)->get();
        $hotProd = Product::withoutGlobalScope('store')->where('type', 'hot')->limit(8)->get();
        $bestSellingProd = Product::withoutGlobalScope('store')->where('type', 'best_selling')->limit(8)->get();

        $data['carusels'] = CaruselResource::collection($carusels);
        $data['categories'] = CategoryResource::collection($categories);
        $data['newProd'] = ProductCollectionResource::collection($newProd);
        $data['topProd'] = ProductCollectionResource::collection($topProd);
        $data['hotProd'] = ProductCollectionResource::collection($hotProd);
        $data['bestSellingProd'] = ProductCollectionResource::collection($bestSellingProd);

        return Helper::sendSuccess('', $data, 200);
    }

    public function bunners() {
        $carusels = Carusel::limit(4)->get();

        return Helper::sendSuccess('', $carusels, 200);
    }
}
