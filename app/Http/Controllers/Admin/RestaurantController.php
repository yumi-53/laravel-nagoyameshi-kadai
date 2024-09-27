<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $restaurants = Restaurant::paginate(config('view.page'));

        if (!empty($keyword)) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")
                        ->paginate(config('view.page'));
        } else {
            $restaurants = Restaurant::paginate(config('view.page'));
        }
        $total = $restaurants->total();
        
        return view('admin.restaurants.index', compact('restaurants', 'total', 'keyword'));
    }

    public function create()
    {
        $categories = Category::all();
        $regular_holidays = RegularHoliday::all();
        return view('admin.restaurants.create', compact('categories','regular_holidays'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price'=> 'required|numeric|min:0|lte:highest_price',
            'highest_price'=> 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address'=> 'required',
            'opening_time'=> 'required|before:closing_time',
            'closing_time'=> 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('restaurants');
        } else {
            $image = '';
        }

        $restaurant = Restaurant::create([
            'name' => $request->input('name'),
            'image' => basename($image),
            'description' => $request->input('description'),
            'lowest_price' => $request->input('lowest_price'),
            'highest_price' => $request->input('highest_price'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'opening_time' => $request->input('opening_time'),
            'closing_time' => $request->input('closing_time'),
            'seating_capacity' => $request->input('seating_capacity'),
        ]);
    
        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);

        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids', []));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');

    }
    
    public function show($id)
    {
        $restaurant = Restaurant::find($id);

        return view('admin/restaurants/show', compact('restaurant'));
    }
    
    public function edit($id)
    {
        $restaurant = Restaurant::find($id);
        $categories = Category::all();
        $category_ids = $restaurant->categories->pluck('id')->toArray();
        $regular_holidays = RegularHoliday::all();
        
        return view('admin/restaurants/edit', compact('restaurant','categories','category_ids','regular_holidays'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price'=> 'required|numeric|min:0|lte:highest_price',
            'highest_price'=> 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address'=> 'required',
            'opening_time'=> 'required|before:closing_time',
            'closing_time'=> 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        $restaurant = Restaurant::find($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('restaurants');
        } else {
            $image =  $restaurant->image;
        }
        
        $restaurant->update([  
            'name' => $request->input('name'),
            'image' => basename($image),
            'description' => $request->input('description'),
            'lowest_price'=> $request->input('lowest_price'),
            'highest_price'=> $request->input('highest_price'),
            'postal_code' => $request->input('postal_code'),
            'address'=> $request->input('address'),
            'opening_time'=> $request->input('opening_time'),
            'closing_time'=> $request->input('closing_time'),
            'seating_capacity' => $request->input('seating_capacity'),
        ]);

        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);

        $regular_holiday_ids = array_filter($request->input('regular_holiday_ids', []));
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.show', ['restaurant' => $restaurant->id])->with('flash_message', '店舗を編集しました。');


    }
    
    public function destroy($id)
    {
        $restaurant = Restaurant::find($id);
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
