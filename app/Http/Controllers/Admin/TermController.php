<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    public function index()
    {
        $term = Term::first();
        return view('admin.terms.index', compact('term'));
    }

    public function edit($id)
    {
        $term = Term::find($id);
        return view('admin.terms.edit', compact('term'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $term = Term::find($id);

        $term->update([
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.terms.index', ['content' => $term->id])->with('flash_message', '利用規約を編集しました。');
    }
}
