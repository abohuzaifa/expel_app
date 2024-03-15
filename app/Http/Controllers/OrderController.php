<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        
        $data['perPage'] = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $data['orders'] = Order::with(['seller', 'user', 'payment'])
                        ->orderBy('id', 'desc')
                        ->paginate($data['perPage'], ['*'], 'page', $page);
        // echo "<pre>";print_r($data['orders'][0]->seller); exit;
        return view('orders.index', $data);
    }

    public function show(Order $order)
    {
        // print_r($order->id); exit;
        $order = Order::where('id', $order->id)->with(['seller', 'user', 'orderItems.product'])->first();
        // echo "<pre>";print_r($order); exit;
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Assuming $orderId is the ID of the order you want to delete
        $order = Order::find($order->id);

        if ($order) {
            // Delete order items associated with the order
            $order->orderItems()->delete();

            // Delete the order itself
            $order->delete();

            // Additional actions if needed after deletion
        }
        return redirect()->back()->with('success', trans('lang.delete_message'));
    }
}
