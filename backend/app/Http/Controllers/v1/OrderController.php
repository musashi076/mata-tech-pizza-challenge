<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'order_id');
        $descending = $request->query('descending', 'false') === 'true';
        $filter = $request->query('filter', '');

        $query = Order::query()
            ->select('orders.*')
            ->leftJoin('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->leftJoin('pizzas', 'order_details.pizza_id', '=', 'pizzas.pizza_id')
            ->leftJoin('pizza_types', 'pizzas.pizza_type_id', '=', 'pizza_types.pizza_type_id')
            ->distinct();

        if (!empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('orders.order_id', 'like', '%' . $filter . '%')
                  ->orWhere('orders.date', 'like', '%' . $filter . '%')
                  ->orWhere('orders.time', 'like', '%' . $filter . '%')
                  ->orWhere('pizza_types.name', 'like', '%' . $filter . '%')
                  ->orWhere('pizzas.size', 'like', '%' . $filter . '%');
            });
        }

        if ($sortBy) {
            if (in_array($sortBy, ['order_id', 'date', 'time'])) {
                $query->orderBy('orders.' . $sortBy, $descending ? 'desc' : 'asc');
            }
            
            else if ($sortBy === 'pizza_name') {
                $query->orderBy('pizza_types.name', $descending ? 'desc' : 'asc');
            }
            
            else if ($sortBy === 'pizza_size') {
                $query->orderBy('pizzas.size', $descending ? 'desc' : 'asc');
            }
        }

        $orders = $query->with('orderDetails.pizza.pizzaType')->paginate($perPage);

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load('orderDetails.pizza.pizzaType');

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
