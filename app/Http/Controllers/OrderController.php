<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use GuzzleHttp\Client;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Order::with(['customer'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = Order::create($request->all());

        return response()->json($order, 201);
    }

    public function addProduct(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->paid == 1) {
            return response()->json(['error' => 'The order is already paid and cannot be modified'], 400);
        }
        $product = Product::findOrFail($request->input('product_id'));

        $timestamps = [
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ];

        $order->products()->attach($product, $timestamps);
        return response()->json(['message' => 'Product added to the order successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $order;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order->update($request->all());

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order->delete();

        return response()->json(null, 204);
    }

    public function payOrder(Request $request, $id)
    {
        $order = Order::with(['customer'])->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->paid == 1) {
            return response()->json(['error' => 'Order already paid'], 400);
        }

        $get_order_total = $this->calculateProductAmount($order->id);
        

        $paymentDetails = [
            'order_id' => $order->id,
            'customer_email' => $order['customer']['email'],
            'value' => $get_order_total,
        ];

        // print_r($paymentDetails);die;

        $client = new Client();
        try {
            $response = $client->post('https://superpay.view.agentur-loop.com/pay', [
                'json' => $paymentDetails
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Payment provider unavailable'], 500);
        }

        $responseData = json_decode($response->getBody()->getContents(), true);
        if ($responseData['message'] == 'Payment Successful') {
            $order->paid = 1;
            $order->save();

            return response()->json(['message' => 'Payment Successful'], 200);
        } else {
            return response()->json(['error' => 'Payment Failed'], 400);
        }
    }

    public function calculateProductAmount($order_id)
    {
        $order = Order::with('orderProducts.product')->find($order_id);
        $orderProducts = $order->orderProducts;
        $totalAmount = 0;

        foreach ($orderProducts as $orderProduct) {
            $totalAmount += $orderProduct['product']['price'];
        }

        return $totalAmount;
    }
}
