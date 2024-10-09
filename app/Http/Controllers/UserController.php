<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UserRequest;


class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user.index', compact('user'));
    }

    public function edit($id)
    {
        $auth_id = Auth::id();
        if ($auth_id == $id) {
            $user = User::find($id);
            return view('user.edit', compact('user'));
        } else {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }
    }

    public function update(UserRequest $request, $id): RedirectResponse
    {
        $auth_id = Auth::id();
        if ($auth_id == $id) {
            User::updateOrCreate(
                ['id' => $id],[
                'name' => $request->input('name'),
                'kana' =>  $request->input('kana'),
                'email' => $request->input('email'),
                'postal_code'=> $request->input('postal_code'),
                'address'=> $request->input('address'),
                'phone_number' => $request->input('phone_number'),
                'birthday'=> $request->input('birthday'),
                'occupation'=> $request->input('occupation'),
                ],
            );
            return redirect()->route('user.index')->with('flash_message', '会員情報を編集しました。');
        } else {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }
    }
}
