<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Pizza;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'pizza_id');
        $descending = $request->query('descending', 'false') === 'true';
        $filter = $request->query('filter', '');

        $query = Pizza::with('pizzaType');

        if (!empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('pizza_id', 'like', '%' . $filter . '%')
                  ->orWhereHas('pizzaType', function ($sq) use ($filter) {
                      $sq->where('name', 'like', '%' . $filter . '%');
                  })
                  ->orWhere('size', 'like', '%' . $filter . '%')
                  ->orWhereHas('pizzaType', function ($sq) use ($filter) {
                      $sq->where('category', 'like', '%' . $filter . '%');
                  })
                  ->orWhereHas('pizzaType', function ($sq) use ($filter) {
                      $sq->where('ingredients', 'like', '%' . $filter . '%');
                  });
            });
        }

        if ($sortBy) {
            if (in_array($sortBy, ['pizza_id', 'size', 'price'])) {
                $query->orderBy($sortBy, $descending ? 'desc' : 'asc');
            }
            else if ($sortBy === 'pizza_type_name') {
                $query->join('pizza_types', 'pizzas.pizza_type_id', '=', 'pizza_types.pizza_type_id')
                      ->orderBy('pizza_types.name', $descending ? 'desc' : 'asc')
                      ->select('pizzas.*');
            }
            else if ($sortBy === 'category') {
                $query->join('pizza_types', 'pizzas.pizza_type_id', '=', 'pizza_types.pizza_type_id')
                      ->orderBy('pizza_types.category', $descending ? 'desc' : 'asc')
                      ->select('pizzas.*');
            }
        }

        $pizzas = $query->paginate($perPage);

        return response()->json($pizzas);
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
    public function show($pizza_id): JsonResponse
    {
        $pizza = Pizza::with('pizzaType')->find($pizza_id);

        return response()->json($pizza);
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
