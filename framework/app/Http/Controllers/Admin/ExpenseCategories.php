<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseCatRequest;
use App\Http\Requests\ImportRequest;
use App\Imports\ExpenseCategoriesImport;
use App\Model\ExpCats;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseCategories extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Settings list');
	}

	public function importExpense(ImportRequest $request) {
		$file = $request->excel;
		$destinationPath = './assets/samples/'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName = Str::uuid() . '.' . $extension;
		$file->move($destinationPath, $fileName);

		Excel::import(new ExpenseCategoriesImport, 'assets/samples/' . $fileName);

		// $excel = Importer::make('Excel');
		// $excel->load('assets/samples/' . $fileName);
		// $collection = $excel->getCollection()->toArray();
		// array_shift($collection);
		// // dd($collection);
		// foreach ($collection as $expense) {
		//     if ($expense[0] != null) {
		//         ExpCats::create([
		//             "name" => $expense[0],
		//             "user_id" => Auth::id(),
		//             "type" => "u",

		//         ]);
		//     }
		// }
		return back();
	}

	public function index() {
		$data['data'] = ExpCats::get();

		return view("expense.cats", $data);
	}
	public function create() {

		return view("expense.catadd");
	}

	public function destroy(Request $request) {
		ExpCats::find($request->get('id'))->expense()->delete();
		ExpCats::find($request->get('id'))->delete();

		return redirect()->route('expensecategories.index');
	}

	public function store(ExpenseCatRequest $request) {

		ExpCats::create([
			"name" => $request->get("name"),
			"user_id" => Auth::id(),
			"type" => "u",

		]);

		return redirect()->route("expensecategories.index");

	}

	public function edit(ExpCats $expensecategory) {

		return view("expense.catedit", compact("expensecategory"));
	}

	public function update(ExpenseCatRequest $request) {

		$user = ExpCats::whereId($request->get("id"))->first();
		$user->name = $request->get("name");
		$user->user_id = Auth::id();
		$user->save();

		return redirect()->route("expensecategories.index");
	}

}
