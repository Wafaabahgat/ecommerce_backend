<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Admin\Product;
use App\Models\Cart;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function  addToCart(Request $request, $id)
    {
        $user = auth()->user();
        $product_id = $id;
        $quantity = $request->quantity??1;
        $product = Product::find($product_id);

        if (!$product) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        $cart_prod = Cart::where('product_id', $product_id)->where('user_id', $user->id)->first();

        if ($cart_prod) {
            return Helper::sendError('Product already in your Cart', [], 404);
        }

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'total_price' => $quantity * $product->price,
        ]);

        return Helper::sendSuccess('Product added to Cart successfully.', null, 200);
    }

    public function getUserCart()
    {
        $user = auth()->user();

        $user_cart = DB::table('carts')
        ->where('user_id', $user->id)
        ->get();

        $prods =[];
        if ($user_cart) {
            # code...
            foreach ($user_cart as $key => $value) {
                # code...
                $pd = Product::select(['id','name','slug', 'image', 'price'])->find($value->product_id);
                $pd['quantity'] = $value->quantity;
                $prods[] = $pd->toArray();
            }
        }

        return Helper::sendSuccess('success', $prods, 200);
    }

    public function removeFromCart($id)
    {
        $user = auth()->user();

        $cart_prod = Cart::where('product_id', $id)->where('user_id', $user->id)->first();

        if (!$cart_prod) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        $cart_prod->delete();

        return Helper::sendSuccess('Product removed from cart successfully.', null, 200);
    }

    public function updateQtyAtCart(Request $request, $id)
    {
        $user = auth()->user();

        $cart_prod = Cart::where('product_id', $id)->where('user_id', $user->id)->first();
        $product = Product::find($id);

        if (!$product) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        if (!$cart_prod) {
            return Helper::sendError('Product Not Fount', [], 404);
        }

        $cart_prod->update([
            'quantity' => $request->quantity??1,
            'total_price' => ($request->quantity??1) * $product->price,
        ]);

        return Helper::sendSuccess('Product removed from cart successfully.', null, 200);
    }
}
