<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $category_id = $request->category_id;        
        $price = $request->price;
        $categories = Category::all();
        
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
        ];
        $sort_query = [];
        $sorted = "created_at desc";

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        if (!empty($keyword)) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")
                                            ->orWhere('address', 'like', "%{$keyword}%")
                                            ->orWhereHas('categories', function ($query) use ($keyword) {
                                                $query->where('categories.name', 'like', "%{$keyword}%");
                                            })
                                            ->sortable($sort_query)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(config('view.page'));
        } elseif (!empty($category_id)) {
            $restaurants = Restaurant::WhereHas('categories', function ($query) use ($category_id) {
                                                $query->where('categories.id', 'like', $category_id);
                                            })
                                            ->sortable($sort_query)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(config('view.page'));        
        } elseif (!empty($price)) {
            $restaurants = Restaurant::where('lowest_price', '<=', $price)
                                            ->sortable($sort_query)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(config('view.page'));
        } else {
            $restaurants = Restaurant::sortable($sort_query)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(config('view.page'));
        }
        $total = $restaurants->total();

        $returnItem = ['keyword','category_id','price','sorts','sorted','restaurants','categories','total'];
        return view('restaurants.index', compact($returnItem));
    }

    public function show($id)
    {
        $restaurant = Restaurant::find($id);

        return view('restaurants/show', compact('restaurant'));
    }
}
