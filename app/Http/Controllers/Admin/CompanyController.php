<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        return view('admin.company.index', compact('company'));
    }

    public function edit($id)
    {
        $company = Company::find($id);
        return view('admin.company.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'representative' => 'required',
            'establishment_date' => 'required',
            'capital' => 'required',
            'business' => 'required',
            'number_of_employees' => 'required',
        ]);

        $company = Company::find($id);

        $company->update([
            'name' => $request->input('name'),
            'postal_code' => $request->input('postal_code'),
            'address' =>  $request->input('address'),
            'representative' =>  $request->input('representative'),
            'establishment_date' =>  $request->input('establishment_date'),
            'capital' =>  $request->input('capital'),
            'business' =>  $request->input('business'),
            'number_of_employees' =>  $request->input('number_of_employees'),
        ]);

        return redirect()->route('admin.company.index', ['company' => $company->id])->with('flash_message', '会社概要を編集しました。');
    }
}
