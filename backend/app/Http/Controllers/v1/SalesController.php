<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function getDailyOrdersWithDetails(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:m-d-Y',
        ]);

        $date = $request->query('date');

        $date = $request->query('date');
        $carbonDate = \Carbon\Carbon::createFromFormat('m-d-Y', $date);

        $totalSalesForDay = OrderDetail::join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->join('pizzas', 'order_details.pizza_id', '=', 'pizzas.pizza_id')
            ->whereDate('orders.order_date', $carbonDate->toDateString())
            ->sum(DB::raw('order_details.quantity * pizzas.price'));

        $orders = Order::select(
                'orders.order_id',
                'orders.order_date',
                DB::raw('SUM(order_details.quantity * pizzas.price) as order_total_sales')
            )
            ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->join('pizzas', 'order_details.pizza_id', '=', 'pizzas.pizza_id')
            ->whereDate('orders.order_date', $carbonDate->toDateString())
            ->groupBy('orders.order_id', 'orders.order_date')
            ->orderBy('orders.order_date', 'asc')
            ->get();

        return response()->json([
            'date' => $date,
            'total_sales' => round($totalSalesForDay, 2),
            'orders' => $orders->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'order_date' => $order->order_date,
                    'order_total_sales' => round($order->order_total_sales, 2),
                ];
            }),
        ]);
        
    }

    public function getMonthlySales(): JsonResponse
    {
        $monthlySales = Order::select(
                DB::raw('DATE_FORMAT(orders.order_date, "%Y-%m") as sale_month'),
                DB::raw('SUM(order_details.quantity * pizzas.price) as monthly_revenue'),
                DB::raw('SUM(order_details.quantity) as monthly_quantity_sold'),
                DB::raw('COUNT(DISTINCT orders.order_id) as total_orders_in_month')
            )
            ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->join('pizzas', 'order_details.pizza_id', '=', 'pizzas.pizza_id')
            ->groupBy('sale_month')
            ->orderBy('sale_month', 'asc')
            ->get();

        return response()->json($monthlySales);
    }
}
