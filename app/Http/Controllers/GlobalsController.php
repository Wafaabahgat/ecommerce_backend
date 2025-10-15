<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Models\Admin\Product;
use App\Models\Admin\Category;
use App\Models\Admin\Store;
use Illuminate\Http\Request;

class GlobalsController extends Controller
{

    public function categories()
    {
        $categories = Category::active()->get();

        return Helper::sendSuccess('', $categories);
    }

    public function categoryProducts($slug)
    {
        $category = Category::where('slug', $slug)->first();
        $products = Product::where('category_id', $category->id)->get();

        return Helper::sendSuccess('', $products);
    }
    public function stores()
    {
        $stores = Store::active()->get();

        return Helper::sendSuccess('', $stores);
    }
}
