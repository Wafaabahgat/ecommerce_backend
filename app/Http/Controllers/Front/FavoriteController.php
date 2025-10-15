<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Admin\Address;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Product;
use App\Models\Favorite;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    public function  addToFav($id)
    {
        $user = auth()->user();
        $product_id = $id;
        $product = Product::find($product_id);

        if (!$product) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        $fav_prod = Favorite::where('product_id', $product_id)->where('user_id', $user->id)->first();

        if ($fav_prod) {
            return Helper::sendError('Product already in your favorite', [], 404);
        }

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
        ]);

        return Helper::sendSuccess('Product added to favorite successfully.', null, 200);
    }

    public function getUserFav()
    {
        $user = auth()->user();

        $user_favs = DB::table('favorites')->where('user_id', $user->id)->get();

        $prods =[];
        if ($user_favs) {
            # code...
            foreach ($user_favs as $key => $value) {
                # code...
                $pd = Product::find($value->product_id);
                $prods[] = $pd->toArray();
            }
        }

        return Helper::sendSuccess('success', $prods, 200);
    }

    public function removeFromFav($id)
    {
        $user = auth()->user();

        $fav_prod = Favorite::where('product_id', $id)->where('user_id', $user->id)->first();

        if (!$fav_prod) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        $fav_prod->delete();

        return Helper::sendSuccess('Product removed from favorite successfully.', null, 200);
    }
}
