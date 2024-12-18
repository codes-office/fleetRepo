<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartsRequest;
use App\Model\PartsCategoryModel;
use App\Model\PartsModel;
use App\Model\PartStock;
use App\Model\Vendor;
use Auth;
use Illuminate\Http\Request;

class PartsController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Parts add', ['only' => ['create']]);
		$this->middleware('permission:Parts edit', ['only' => ['edit']]);
		$this->middleware('permission:Parts delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Parts list');
	}

	public function index() {
		$index['data'] = PartsModel::orderBy('id', 'desc')->get();
		return view("parts.index", $index);
	}

	public function create() {
		$vendors = Vendor::get();
		$categories = PartsCategoryModel::get();
		return view("parts.create", compact('vendors', 'categories'));
	}

	public function destroy(Request $request) {

		$part = PartsModel::find($request->get('id'));
		if (file_exists('./uploads/' . $part->image) && !is_dir('./uploads/' . $part->image)) {
			unlink('./uploads/' . $part->image);
		}
		$part->delete();

		return redirect()->route('parts.index');
	}

	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();
		$fileName1 = uniqid() . '.' . $extension;

		$file->move($destinationPath, $fileName1);
		$x = PartsModel::find($id)->update([$field => $fileName1]);

	}

	public function edit($id) {
		$index['data'] = PartsModel::whereId($id)->first();
		$index['vendors'] = Vendor::get();
		$index['categories'] = PartsCategoryModel::get();

		$index['udfs'] = unserialize($index['data']->udf);
		return view("parts.edit", $index);
	}

	public function stock($id) {
		$data['data'] = PartStock::wherePart_id($id)->get();
		return view("parts.stocks", $data);
	}

	public function update(PartsRequest $request) {

		$part = PartsModel::whereId($request->get("id"))->first();
		if ($request->file('image') && $request->file('image')->isValid()) {
			if (file_exists('./uploads/' . $part->image) && !is_dir('./uploads/' . $part->image)) {
				unlink('./uploads/' . $part->image);
			}
			$this->upload_file($request->file('image'), "image", $part->id);
		}
		$part->barcode = $request->get('barcode');
		$part->number = $request->get('number');
		$part->description = $request->get('description');
		$part->unit_cost = $request->get('unit_cost');
		$part->vendor_id = $request->get('vendor_id');
		$part->manufacturer = $request->get('manufacturer');
		$part->note = $request->get('note');
		$part->stock = $request->get('stock');
		$part->udf = serialize($request->get('udf'));
		$part->category_id = $request->get('category_id');
		$part->status = $request->get('status');
		$part->availability = $request->get('availability');
		$part->title = $request->get('title');
		$part->year = $request->get('year');
		$part->model = $request->get('model');
		$part->save();

		return redirect()->route("parts.index");
	}
	public function store(PartsRequest $request) {

		$id = PartsModel::create([
			'user_id' => Auth::user()->id,
			'barcode' => $request->get('barcode'),
			'number' => $request->get('number'),
			'description' => $request->get('description'),
			'unit_cost' => $request->get('unit_cost'),
			'vendor_id' => $request->get('vendor_id'),
			'manufacturer' => $request->get('manufacturer'),
			'note' => $request->get('note'),
			'stock' => $request->get('stock'),
			'udf' => serialize($request->get('udf')),
			'category_id' => $request->get('category_id'),
			'status' => $request->get('status'),
			'availability' => $request->get('availability'),
			'title' => $request->get('title'),
			'year' => $request->get('year'),
			'model' => $request->get('model'),
		])->id;

		if ($request->file('image') && $request->file('image')->isValid()) {
			$this->upload_file($request->file('image'), "image", $id);
		}

		return redirect()->route("parts.index");
	}

	public function bulk_delete(Request $request) {
		$parts = PartsModel::whereIn('id', $request->ids)->get();
		foreach ($parts as $part) {
			if (file_exists('./uploads/' . $part->image) && !is_dir('./uploads/' . $part->image)) {
				unlink('./uploads/' . $part->image);
			}
			$part->delete();
		}
		return back();
	}

	public function add_stock(Request $request) {
		$part = PartsModel::find($request->part_id);
		$part->stock = $part->stock + $request->stock;
		$part->save();
		return back();
	}
}
