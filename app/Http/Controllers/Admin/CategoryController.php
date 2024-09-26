<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $categories = Category::paginate(config('view.page'));

        if (!empty($keyword)) {
            $categories = Category::where('name', 'like', "%{$keyword}%")
                        ->paginate(config('view.page'));
        } else {
            $categories = Category::paginate(config('view.page'));
        }
        $total = $categories->total();

        return view('admin.categories.index', compact('categories', 'total', 'keyword'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Category::create([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category = Category::find($id); 
        
        $category->update([  
            'name' => $request->input('name'),
        ]);
        return redirect()->route('admin.categories.index', ['categories' => $category->id])->with('flash_message', 'カテゴリを編集しました。');

    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
