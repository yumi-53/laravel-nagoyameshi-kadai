<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Doctrine\Inflector\Rules\Word;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $users = User::paginate(config('view.page'));

        if ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")
                        ->orWhere('kana', 'like', "%{$keyword}%")
                        ->paginate(config('view.page'));
        } else {
            $users = User::paginate(config('view.page'));
        }
        $total = $users->total();
        
        return view('admin.users.index', compact('users', 'total', 'keyword'));
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();
        return view('admin.users.show', compact('user'));
    }
}
