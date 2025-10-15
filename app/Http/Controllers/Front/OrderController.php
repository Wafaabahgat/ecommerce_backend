<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Models\Admin\Address;
use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Admin\Product;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function makeOrder(Request $request)
    {
        try {
            //code...
            $user = auth()->user();

            $user_cart = DB::table('carts')
                ->where('user_id', $user->id)
                ->get();
            // Extracting nested address data
            $firstName = $request->first_name;
            $lastName = $request->last_name;
            $phone = $request->phone;
            $addressLine = $request->address;
            $country = $request->country;

            $total = 0;
            foreach ($user_cart as $key => $value) {
                $productId = $value->product_id;
                $quantity = $value->quantity;
                $prod = Product::where('id', $productId)->select('price')->first();
                $total += $prod->price * $quantity;
            }

            // dd($total);
            DB::beginTransaction();
            $order = Order::create([
                'user_id' => auth()->user()->id,
                'total_price' => $total,
            ]);

            foreach ($user_cart as $key => $value) {
                $productId = $value->product_id;
                $quantity = $value->quantity;
                $prod = Product::where('id', $productId)->select('price')->first();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $prod->price,
                    'total_price' => $prod->price * $quantity,
                ]);
            }

            Address::create([
                'order_id' => $order->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'address' => $addressLine,
                'country' => $country,
            ]);
            DB::commit();

            return Helper::sendSuccess('Order make successfully', [], 200);
        } catch (Exception $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                dd('validation', $e);
                return Helper::sendError($e->getMessage(), $e->errors(), 422);
            }
            if ($e instanceof GuzzleException) {
                dd('exception', $e);
                return Helper::sendError($e->getMessage(), [], $e->getCode());
            }
            // dd('none', $e);
            return Helper::sendError($e->getMessage(), [], $e->getCode());
        }
    }

    public function userOrders()
    {
        $user = auth()->user();

        $orders = Order::with([
            'orderItems:id,order_id,product_id,quantity,total_price',
            'orderItems.product:id,name,slug,image,price',
            'addresse',
        ])->select('id', 'total_price', 'status')->where('user_id', $user->id)->get();

        return Helper::sendSuccess('done', $orders, 200);
    }

    public function userOrder($id)
    {
        $user = auth()->user();

        $order = Order::where('id', $id)->with([
            'orderItems:id,order_id,product_id,quantity,total_price',
            'orderItems.product:id,name,slug,image,price',
            'addresse',
        ])->select('id', 'total_price', 'status')->where('user_id', $user->id)->first();

        return Helper::sendSuccess('done', $order, 200);
    }

    /**
     * Display the specified resource.
     */
    public function getValues($value)
    {
        // Remove "{" and "}" characters
        $data = str_replace(['{', '}'], '', $value);

        // Explode the string by ","
        $parts = explode(',', $data);
        $result = [];

        foreach ($parts as $part) {
            // Explode each part by ":"
            $keyValue = explode(':', $part);
            // Trim whitespace from the key and value
            $key = trim($keyValue[0]);
            $value = trim($keyValue[1]);
            // Store key-value pair in the result array
            $result[$key] = $value;
        }
        return $result;
    }
}
