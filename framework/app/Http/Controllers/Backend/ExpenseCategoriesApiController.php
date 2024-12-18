<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\ExpenseCategoriesImport;
use App\Model\ExpCats;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ExpenseCategoriesApiController extends Controller {

	public function import_records(Request $request) {
		$validation = Validator::make($request->all(), [
			'excel' => 'required|mimes:xlsx,xls',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			try {
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
				$data['success'] = "1";
				$data['message'] = "Records imported successfully!";
				$data['data'] = "";
			} catch (Exception $e) {
				$data['success'] = "0";
				$data['message'] = "Unable to import records.";
				$data['data'] = "";
			}
		}
		return $data;
	}

	public function delete(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$expense = ExpCats::find($request->id);
			if ($expense->type == 'u') {
				$expense->expense()->delete();
				$expense->delete();

				$data['success'] = "1";
				$data['message'] = "Record deleted successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Unable to delete expense category, please try again later!";
				$data['data'] = "";
			}
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'name' => 'required|unique:expense_cat,name,' . \Request::get("id") . ',id,deleted_at,NULL',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$cat = ExpCats::find($request->id);
			if ($cat->type == "u") {
				$cat->name = $request->name;
				$cat->user_id = Auth::id();
				$cat->save();
				$data['success'] = "1";
				$data['message'] = "Expense Category updated successfully!";
				$data['data'] = "";
			} else {
				$data['success'] = "0";
				$data['message'] = "Unable to update expense category, please try again later!";
				$data['data'] = "";
			}

		}
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'name' => 'required|unique:expense_cat,name',
		]);

		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			ExpCats::create([
				"name" => $request->name,
				"user_id" => Auth::id(),
				"type" => "u",
			]);
			$data['success'] = "1";
			$data['message'] = "Expense Category added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function categories() {
		$categories = ExpCats::get();
		$details = array();
		foreach ($categories as $cat) {
			$details[] = array(
				'id' => $cat->id,
				'name' => $cat->name,
				'type' => ($cat->type == 'd') ? "Default" : "User Defined",
				'created' => date('Y-m-d', strtotime($cat->created_at)),
				'cat_type' => $cat->type,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
