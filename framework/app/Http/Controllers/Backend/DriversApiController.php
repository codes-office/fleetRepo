<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Imports\DriverImport;
use App\Model\Bookings;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceItemsModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Rules\UniqueContractNumber;
use App\Rules\UniqueEId;
use App\Rules\UniqueLicenceNumber;
use Auth;
use DB;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class DriversApiController extends Controller {

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

				Excel::import(new DriverImport, 'assets/samples/' . $fileName);
				// $excel = Importer::make('Excel');
				// $excel->load('assets/samples/' . $fileName);
				// $collection = $excel->getCollection()->toArray();
				// array_shift($collection);
				// // dd($collection);
				// foreach ($collection as $driver) {
				//     if ($driver[4] != null) {
				//         $id = User::create([
				//             "name" => $driver[0] . " " . $driver[2],
				//             "email" => $driver[4],
				//             "password" => bcrypt($driver[15]),
				//             "user_type" => "D",
				//             'api_token' => str_random(60),
				//         ])->id;
				//         $user = User::find($id);

				//         $user->is_active = 1;
				//         $user->is_available = 0;
				//         $user->first_name = $driver[0];
				//         $user->middle_name = $driver[1];
				//         $user->last_name = $driver[2];
				//         $user->address = $driver[3];
				//         $user->phone = $driver[5];
				//         $user->phone_code = "+" . $driver[6];
				//         $user->emp_id = $driver[7];
				//         $user->contract_number = $driver[8];
				//         $user->license_number = $driver[9];
				//         if ($driver[10] != null) {
				//             $user->issue_date = date('Y-m-d', strtotime($driver[10]));
				//         }

				//         if ($driver[11] != null) {
				//             $user->exp_date = date('Y-m-d', strtotime($driver[11]));
				//         }

				//         if ($driver[12] != null) {
				//             $user->start_date = date('Y-m-d', strtotime($driver[12]));
				//         }

				//         if ($driver[13] != null) {
				//             $user->end_date = date('Y-m-d', strtotime($driver[13]));
				//         }

				//         $user->gender = (($driver[14] == 'female') ? 0 : 1);
				//         $user->econtact = $driver[15];
				//         $user->save();
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

	public function yearly_report(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'vehicle_id' => 'nullable|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();
			$b = array();
			$i = array();
			$c = array();
			$vehicle_ids = Bookings::where('driver_id', Auth::id())->pluck('vehicle_id')->toArray();

			$year_select = $request->year;
			$vehicle_select = $request->vehicle_id;

			$income1 = IncomeModel::select(DB::raw("sum(amount) as income"))->whereYear('date', $year_select);
			$expense1 = Expense::select(DB::raw("sum(amount) as expense"))->whereYear('date', $year_select);
			$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $year_select)->groupBy('expense_type', 'type');
			$income2 = IncomeModel::select('income_cat', DB::raw("sum(amount) as amount"))->whereYear('date', $year_select)->groupBy('income_cat');
			if ($vehicle_select) {
				$income = $income1->where('vehicle_id', $vehicle_select)->get();
				$expenses = $expense1->where('vehicle_id', $vehicle_select)->get();
				$expense_by_cat = $expense2->where('vehicle_id', $vehicle_select)->get();
				$income_by_cat = $income2->where('vehicle_id', $vehicle_select)->get();
			} else {
				$income = $income1->whereIn('vehicle_id', $vehicle_ids)->get();
				$expenses = $expense1->whereIn('vehicle_id', $vehicle_ids)->get();
				$expense_by_cat = $expense2->whereIn('vehicle_id', $vehicle_ids)->get();
				$income_by_cat = $income2->whereIn('vehicle_id', $vehicle_ids)->get();
			}

			$ss = ServiceItemsModel::get();
			foreach ($ss as $s) {
				$c[$s->id] = $s->description;
			}

			$kk = ExpCats::get();

			foreach ($kk as $k) {
				$b[$k->id] = $k->name;
			}
			$hh = IncCats::get();

			foreach ($hh as $k) {
				$i[$k->id] = $k->name;
			}

			$service = $c;
			$expense_cats = $b;
			$income_cats = $i;

			$income_amt = (is_null($income[0]->income) ? 0 : $income[0]->income);
			$expense_amt = (is_null($expenses[0]->expense) ? 0 : $expenses[0]->expense);
			// dd($income_amt, $expense_amt);

			$all_expense_by_cats = array();
			$total_expense_by_cat = 0;
			$expense_by_category = array();
			$expense_labels = array();
			$expense_values = array();
			foreach ($expense_by_cat as $exp) {
				if ($exp->type == "s") {
					$typename = $service[$exp->expense_type];
				} else {
					$typename = $expense_cats[$exp->expense_type];
				}

				$all_expense_by_cats[$typename] = $exp->expense;
				$total_expense_by_cat += $exp->expense;
				$expense_by_category[] = array('name' => $typename, 'value' => $exp->expense);
				$expense_labels[] = $typename;
				$expense_values[] = $exp->expense;
			}

			$records = array(
				'currency' => Hyvikk::get("currency"),
				'income' => array(
					'profit_loss' => ($income_amt - $expense_amt),
					'income' => $income_amt,
					'expenses' => $expense_amt,
					'labels' => array('Income', 'Expenses'),
					'values' => array($income_amt, $expense_amt),
				),

				'expense_by_category' => array(
					'total_amount' => $total_expense_by_cat,
					// 'records' => $all_expense_by_cats,
					'records' => $expense_by_category,
					'expense_labels' => $expense_labels,
					'expense_values' => $expense_values,
				),
				'yearly_chart' => array(
					'yearly_income' => $this->yearly_income(),
					'yearly_expense' => $this->yearly_expense(),
				),
			);

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function monthly_report(Request $request) {
		$validation = Validator::make($request->all(), [
			'month' => 'required|integer',
			'year' => 'required|integer',
			'vehicle_id' => 'nullable|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$records = array();

			$y = array();
			$b = array();
			$i = array();
			$c = array();
			$vehicle_ids = Bookings::where('driver_id', Auth::id())->pluck('vehicle_id')->toArray();
			$year_select = $request->year;
			$month_select = $request->month;
			$vehicle_select = $request->vehicle_id;

			$income1 = IncomeModel::select(DB::raw("SUM(amount) as income"))->whereYear('date', $year_select)->whereMonth('date', $month_select)->whereIn('vehicle_id', $vehicle_ids);
			$expense1 = Expense::select(DB::raw("SUM(amount) as expense"))->whereYear('date', $year_select)->whereMonth('date', $month_select)->whereIn('vehicle_id', $vehicle_ids);
			$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $year_select)->whereMonth('date', $month_select)->whereIn('vehicle_id', $vehicle_ids)->groupBy(['expense_type', 'type']);
			$income2 = IncomeModel::select("income_cat", DB::raw("sum(amount) as amount"))->whereYear('date', $year_select)->whereMonth('date', $month_select)->whereIn('vehicle_id', $vehicle_ids)->groupBy(['income_cat']);
			if ($vehicle_select) {
				$income = $income1->where('vehicle_id', $vehicle_select)->get();
				$expenses = $expense1->where('vehicle_id', $vehicle_select)->get();
				$expense_by_cat = $expense2->where('vehicle_id', $vehicle_select)->get();
				$income_by_cat = $income2->where('vehicle_id', $vehicle_select)->get();
			} else {
				$income = $income1->get();
				$expenses = $expense1->get();
				$expense_by_cat = $expense2->get();
				$income_by_cat = $income2->get();
			}

			$ss = ServiceItemsModel::get();
			foreach ($ss as $s) {
				$c[$s->id] = $s->description;
			}

			$kk = ExpCats::get();

			foreach ($kk as $k) {
				$b[$k->id] = $k->name;

			}
			$hh = IncCats::get();

			foreach ($hh as $k) {
				$i[$k->id] = $k->name;

			}
			$service = $c;
			$expense_cats = $b;
			$income_cats = $i;
			$income_amt = (is_null($income[0]->income) ? 0 : $income[0]->income);
			$expense_amt = (is_null($expenses[0]->expense) ? 0 : $expenses[0]->expense);
			// dd($income_amt, $expense_amt);

			$all_expense_by_cats = array();
			$total_expense_by_cat = 0;
			$expense_labels = array();
			$expense_values = array();
			$expense_by_category = array();
			foreach ($expense_by_cat as $exp) {
				if ($exp->type == "s") {
					$typename = $service[$exp->expense_type];
				} else {
					$typename = $expense_cats[$exp->expense_type];
				}

				$all_expense_by_cats[$typename] = $exp->expense;
				$total_expense_by_cat += $exp->expense;
				$expense_labels[] = $typename;
				$expense_values[] = $exp->expense;
				$expense_by_category[] = array('name' => $typename, 'value' => $exp->expense);
			}

			$records = array(
				'currency' => Hyvikk::get("currency"),
				'income' => array(
					'profit_loss' => ($income_amt - $expense_amt),
					'income' => $income_amt,
					'expenses' => $expense_amt,
					'labels' => array('Income', 'Expenses'),
					'values' => array($income_amt, $expense_amt),
				),

				'expense_by_category' => array(
					'total_amount' => $total_expense_by_cat,
					// 'records' => $all_expense_by_cats,
					'records' => $expense_by_category,
					'expense_labels' => $expense_labels,
					'expense_values' => $expense_values,
				),
				'yearly_chart' => array(
					'yearly_income' => $this->yearly_income(),
					'yearly_expense' => $this->yearly_expense(),
				),
			);
			// dd($records);
			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	private function yearly_income() {

		$d_id = Auth::user()->id;
		$bookings = Bookings::where('driver_id', $d_id)->get();
		$v_id = array('0');
		foreach ($bookings as $key) {
			if ($key->vehicle_id != null) {
				$v_id[] = $key->vehicle_id;
			}

		}

		$in = join(",", $v_id);
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from income where year(date)=? and  deleted_at is null and vehicle_id in(' . $in . ') group by month(date)', [date("Y")]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		$labels = array();
		$values = array();
		foreach ($yr as $key => $val) {
			$labels[] = $key;
			$values[] = $val;
		}
		// return array('labels' => $labels, 'values' => $values);
		return $yr;
	}
	private function yearly_expense() {
		$d_id = Auth::user()->id;
		$bookings = Bookings::where('driver_id', $d_id)->get();
		$v_id = array('0');
		foreach ($bookings as $key) {
			if ($key->vehicle_id != null) {
				$v_id[] = $key->vehicle_id;
			}

		}

		$in = join(",", $v_id);
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from expense where year(date)=? and  deleted_at is null and vehicle_id in(' . $in . ') group by month(date)', [date("Y")]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		return $yr;
		$labels = array();
		$values = array();
		foreach ($yr as $key => $val) {
			$labels[] = $key;
			$values[] = $val;
		}
		// return array('labels' => $labels, 'values' => $values);

	}

	public function driver_reports_dropdown() {

		$vehicle_ids = Bookings::where('driver_id', Auth::id())->pluck('vehicle_id')->toArray();

		$years = DB::select(DB::raw("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc"));
		$y = array();
		foreach ($years as $year) {
			$y[] = $year->years;
		}

		if ($years == null) {
			$y[] = date('Y');
		}
		$vehicles = VehicleModel::whereIn('id', $vehicle_ids)->get();
		foreach ($vehicles as $row) {
			$vehicle_list[] = array(
				'id' => $row->id,
				'vehicle' => $row->make_name . "-" . $row->model_name . "-" . $row->license_plate,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'vehicles' => $vehicle_list,
			'years' => $y,
		);
		return $data;
	}

	public function my_bookings() {
		$details = array();
		$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
		$bookings = Bookings::orderBy('id', 'desc')->whereDriver_id(Auth::id())->get();
		foreach ($bookings as $row) {
			$details[] = array(
				'customer' => $row->customer->name,
				'vehicle' => $row->vehicle->make_name . "-" . $row->vehicle->model->model . "-" . $row->vehicle->license_plate,
				'pickup_datetime' => date($date_format_setting . ' g:i A', strtotime($row->pickup)),
				'dropoff_datetime' => date($date_format_setting . ' g:i A', strtotime($row->dropoff)),
				'pickup_address' => $row->pickup_addr,
				'dropoff_address' => $row->dest_addr,
				'travellers' => $row->travellers,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function profile() {
		$completed = array();
		$upcoming = array();
		$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
		$middlename = (Auth::user()->middle_name)?Auth::user()->middle_name . " ":"";
		$bookings = Bookings::orderBy('id', 'desc')->where('driver_id', Auth::id())->get();
		foreach ($bookings as $row) {
			if ($row->ride_status == "Completed" || $row->status == 1) {
				$completed[] = array(
					'customer' => $row->customer->name,
					'vehicle' => $row->vehicle->make_name . "-" . $row->vehicle->model_name . "-" . $row->vehicle->license_plate,
					'pickup_datetime' => date($date_format_setting . ' g:i A', strtotime($row->pickup)),
					'dropoff_datetime' => date($date_format_setting . ' g:i A', strtotime($row->dropoff)),
					'pickup_address' => $row->pickup_addr,
					'dropoff_address' => $row->dest_addr,
					'travellers' => $row->travellers,
				);
			}
			if ($row->ride_status == "Upcoming" || $row->status != 1) {
				$upcoming[] = array(
					'customer' => $row->customer->name,
					'vehicle' => $row->vehicle->make_name . "-" . $row->vehicle->model_name . "-" . $row->vehicle->license_plate,
					'pickup_datetime' => date($date_format_setting . ' g:i A', strtotime($row->pickup)),
					'dropoff_datetime' => date($date_format_setting . ' g:i A', strtotime($row->dropoff)),
					'pickup_address' => $row->pickup_addr,
					'dropoff_address' => $row->dest_addr,
					'travellers' => $row->travellers,
				);
			}
		}
		if (Auth::user()->getMeta('driver_image') != null) {
			if (starts_with(Auth::user()->getMeta('driver_image'), 'http')) {
				$src = Auth::user()->getMeta('driver_image');
			} else {
				$src = asset('uploads/' . Auth::user()->getMeta('driver_image'));
			}
		} else {
			$src = asset("assets/images/no-user.jpg");
		}
		$user = Auth::user();
		if ($user->language != null) {
			$language = $user->language;
			// $language = explode('-', $user->language);
		} else {
			$language = Hyvikk::get("language");
			// $language = explode('-', Hyvikk::get("language"));
		}
		$details = array(
			'driver_info' => array(
				'id' => Auth::id(),
				'name' => Auth::user()->name,
				'full_name' => Auth::user()->first_name . " " . $middlename . Auth::user()->last_name,
				'total_bookings' => Bookings::whereDriver_id(Auth::id())->get()->count(),
				'phone' => Auth::user()->phone,
				'email' => Auth::user()->email,
				'address' => Auth::user()->address,
				'license_number' => Auth::user()->license_number,
				'issue_date' => Auth::user()->issue_date,
				'exp_date' => Auth::user()->exp_date,
				'emp_id' => Auth::user()->emp_id,
				'contract_number' => Auth::user()->contract_number,
				'license_image' => (Auth::user()->license_image)?asset('uploads/' . Auth::user()->license_image): "",
				'documents' => (Auth::user()->documents)?asset('uploads/' . Auth::user()->documents): "",
				'driver_image' => $src,
				"language" => $language,
			),
			'activity' => $completed,
			'upcoming_rides' => $upcoming,
		);

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;

	}

	public function bulk_delete(Request $request) {
		$validation = Validator::make($request->all(), [
			'ids' => 'required|array',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$drivers = User::whereIn('id', $request->ids)->get();
			foreach ($drivers as $driver) {
				if ($driver->vehicle_id) {
					$vehicle = VehicleModel::find($driver->vehicle_id);
					if ($vehicle != null) {
						$vehicle->driver_id = null;
						$vehicle->save();
					}
				}
				if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
					unlink('./uploads/' . $driver->driver_image);
				}
				if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
					unlink('./uploads/' . $driver->license_image);
				}
				if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
					unlink('./uploads/' . $driver->documents);
				}
			}
			DriverVehicleModel::whereIn('driver_id', $request->ids)->delete();
			User::whereIn('id', $request->ids)->delete();
			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	private function upload_file($file, $field, $id) {
		$destinationPath = './uploads'; // upload path
		$extension = $file->getClientOriginalExtension();

		$fileName1 = Str::uuid() . '.' . $extension;
		$file->move($destinationPath, $fileName1);

		$user = User::find($id);
		$user->setMeta([$field => $fileName1]);
		$user->save();

	}

	public function upload_documents(Request $request, $id) {
		$validation = Validator::make($request->all(), [
			// 'id' => 'required|integer',
			'driver_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'license_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'documents' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			// $file = $request->driver_image;
			// $destinationPath = './uploads'; // upload path
			// $extension = $file->getClientOriginalExtension();

			// $fileName1 = Str::uuid() . '.' . $extension;

			// $file->move($destinationPath, $fileName1);
			// dd($fileName1);
			// $user = User::find($id);
			// $user->setMeta([$field => $fileName1]);
			// $user->save();
			$user = User::find($id);
			if ($request->file('driver_image') && $request->file('driver_image')->isValid()) {
				if (file_exists('./uploads/' . $user->driver_image) && !is_dir('./uploads/' . $user->driver_image)) {
					unlink('./uploads/' . $user->driver_image);
				}
				$this->upload_file($request->file('driver_image'), "driver_image", $id);
			}
			// dd($request->file('driver_image'), $user->driver_image);
			if ($request->file('license_image') && $request->file('license_image')->isValid()) {
				if (file_exists('./uploads/' . $user->license_image) && !is_dir('./uploads/' . $user->license_image)) {
					unlink('./uploads/' . $user->license_image);
				}
				$this->upload_file($request->file('license_image'), "license_image", $id);
				$user->id_proof_type = "License";
				$user->save();
			}
			if ($request->file('documents')) {
				if (file_exists('./uploads/' . $user->documents) && !is_dir('./uploads/' . $user->documents)) {
					unlink('./uploads/' . $user->documents);
				}
				$this->upload_file($request->file('documents'), "documents", $id);
			}
			$data['success'] = "1";
			$data['message'] = "Documents uploaded successfully!";
			$data['data'] = "";
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
			$driver = User::find($request->id);
			if ($driver->vehicle_id) {
				$vehicle = VehicleModel::find($driver->vehicle_id);
				if ($vehicle != null) {
					$vehicle->driver_id = null;
					$vehicle->save();
				}

			}
			if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
				unlink('./uploads/' . $driver->driver_image);
			}
			if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
				unlink('./uploads/' . $driver->license_image);
			}
			if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
				unlink('./uploads/' . $driver->documents);
			}
			DriverVehicleModel::where('driver_id', $request->id)->delete();
			User::find($request->get('id'))->user_data()->delete();
			User::find($request->get('id'))->delete();
			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'emp_id' => ['required', new UniqueEId],
			'license_number' => ['required', new UniqueLicenceNumber],
			'contract_number' => ['required', new UniqueContractNumber],
			// 'emp_id' => 'required',
			// 'license_number' => 'required',
			// 'contract_number' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
			'address' => 'required',
			'phone' => 'required|numeric',
			'email' => 'required|email|unique:users,email,' . \Request::get("id"),
			'exp_date' => 'required|date|after:tomorrow',
			'start_date' => 'date',
			'issue_date' => 'date',
			'end_date' => 'nullable|date',
			'driver_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'license_image' => 'nullable|image|mimes:jpg,png,jpeg',
			'documents' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
			'gender' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'id' => 'required|integer',
			'edit' => 'required|integer|min:1|max:1',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			// dd($request->all());

			$id = $request->id;
			$user = User::find($id);
			if ($user->vehicle_id != $request->vehicle_id) {

				$old_vehicle = VehicleModel::find($user->vehicle_id);
				$old_vehicle->driver_id = null;
				$old_vehicle->save();

				$vehicle = VehicleModel::find($request->vehicle_id);
				$vehicle->driver_id = $user->id;
				$vehicle->save();
				DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $request->vehicle_id, 'date' => date('Y-m-d H:i:s')]);
				DriverVehicleModel::updateOrCreate(['driver_id' => $user->id], ['vehicle_id' => $request->vehicle_id, 'driver_id' => $user->id]);
			}

			if ($request->file('driver_image') && $request->file('driver_image')->isValid()) {
				if (file_exists('./uploads/' . $user->driver_image) && !is_dir('./uploads/' . $user->driver_image)) {
					unlink('./uploads/' . $user->driver_image);
				}
				$this->upload_file($request->file('driver_image'), "driver_image", $id);
			}

			if ($request->file('license_image') && $request->file('license_image')->isValid()) {
				if (file_exists('./uploads/' . $user->license_image) && !is_dir('./uploads/' . $user->license_image)) {
					unlink('./uploads/' . $user->license_image);
				}
				$this->upload_file($request->file('license_image'), "license_image", $id);
				$user->id_proof_type = "License";
				$user->save();
			}
			if ($request->file('documents')) {
				if (file_exists('./uploads/' . $user->documents) && !is_dir('./uploads/' . $user->documents)) {
					unlink('./uploads/' . $user->documents);
				}
				$this->upload_file($request->file('documents'), "documents", $id);

			}
			// dd($request->all());

			$user->name = $request->first_name . " " . $request->last_name;
			$user->email = $request->email;
			$user->save();
			$form_data = $request->all();
			unset($form_data['driver_image']);
			unset($form_data['documents']);
			unset($form_data['license_image']);
			unset($form_data['edit']);
			unset($form_data['id']);

			$user->setMeta($form_data);
			$user->exp_date = date('Y-m-d', strtotime($request->exp_date));
			$user->start_date = date('Y-m-d', strtotime($request->start_date));
			$user->issue_date = date('Y-m-d', strtotime($request->issue_date));
			if ($request->end_date) {
				$user->end_date = date('Y-m-d', strtotime($request->end_date));
			}
			$user->save();
			$data['success'] = "1";
			$data['message'] = "Driver updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function drivers() {
		$drivers = User::whereUser_type("D")->orderBy('id', 'desc')->get();
		$details = array();

		foreach ($drivers as $row) {
			$details[] = array(
				'name' => $row->name,
				'email' => $row->email,
				'phone' => $row->phone,
				'image' => ($row->driver_image)?url('uploads/' . $row->driver_image): url('assets/images/user-noimage.png'),
				'startDate' => date("Y/m/d", strtotime($row->start_date)),
				'active' => ($row->is_active)?true: false,
				'action' => '',
				// other info
				'id' => $row->id,
				'first_name' => $row->first_name,
				'middle_name' => $row->middle_name,
				'last_name' => $row->last_name,
				'vehicle_id' => $row->vehicle_id,
				'address' => $row->address,
				'phone_code' => $row->phone_code,
				'emp_id' => $row->emp_id,
				'contract_number' => $row->contract_number,
				'license_number' => $row->license_number,
				'issue_date' => date("Y/m/d", strtotime($row->issue_date)),
				'exp_date' => date("Y/m/d", strtotime($row->exp_date)),
				'start_date' => date("Y/m/d", strtotime($row->start_date)),
				'end_date' => date("Y/m/d", strtotime($row->end_date)),
				'gender_text' => ($row->gender == 1) ? "Male" : "Female",
				'gender' => $row->gender,
				'license_image' => ($row->license_image)?url('uploads/' . $row->license_image): null,
				'documents' => ($row->documents)?url('uploads/' . $row->documents): null,
				'econtact' => $row->econtact,
				'created' => date("Y-m-d", strtotime($row->created_at)),

			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function store(Request $request) {
		$validation = Validator::make($request->all(), [
			'emp_id' => ['required', new UniqueEId],
			'license_number' => ['required', new UniqueLicenceNumber],
			'contract_number' => ['required', new UniqueContractNumber],
			'first_name' => 'required',
			'last_name' => 'required',
			'address' => 'required',
			'phone' => 'required|numeric',
			'email' => 'required|email|unique:users,email',
			'exp_date' => 'required|date|after:tomorrow',
			'start_date' => 'date',
			'issue_date' => 'date',
			'end_date' => 'nullable|date',
			// 'driver_image' => 'nullable|image|mimes:jpg,png,jpeg',
			// 'license_image' => 'nullable|image|mimes:jpg,png,jpeg',
			// 'documents' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
			'password' => 'required',
			'gender' => 'required|integer',
			'vehicle_id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$id = User::create([
				"name" => $request->first_name . " " . $request->last_name,
				"email" => $request->email,
				"password" => bcrypt($request->password),
				"user_type" => "D",
				'api_token' => str_random(60),
			])->id;
			$user = User::find($id);

			if ($request->file('driver_image') && $request->file('driver_image')->isValid()) {

				$this->upload_file($request->file('driver_image'), "driver_image", $id);
			}

			if ($request->file('license_image') && $request->file('license_image')->isValid()) {
				$this->upload_file($request->file('license_image'), "license_image", $id);
				$user->id_proof_type = "License";
				$user->save();
			}
			if ($request->file('documents')) {
				$this->upload_file($request->file('documents'), "documents", $id);

			}

			$form_data = $request->all();
			unset($form_data['driver_image']);
			unset($form_data['documents']);
			unset($form_data['license_image']);
			$user->setMeta($form_data);
			$user->is_active = 0;
			$user->is_available = 0;
			$user->exp_date = date('Y-m-d', strtotime($request->exp_date));
			$user->start_date = date('Y-m-d', strtotime($request->start_date));
			$user->issue_date = date('Y-m-d', strtotime($request->issue_date));
			if ($request->end_date) {
				$user->end_date = date('Y-m-d', strtotime($request->end_date));
			}

			$user->save();

			$vehicle = VehicleModel::find($request->vehicle_id);
			$vehicle->driver_id = $user->id;
			$vehicle->save();
			DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $request->vehicle_id, 'date' => date('Y-m-d H:i:s')]);
			DriverVehicleModel::updateOrCreate(['vehicle_id' => $request->vehicle_id], ['vehicle_id' => $request->vehicle_id, 'driver_id' => $user->id]);
			$data['success'] = "1";
			$data['message'] = "Driver added successfully!";
			$data['data'] = array('id' => $user->id);

		}
		return $data;

	}

	public function phone_codes() {
		$data = array('+93',
			'+358',
			'+355',
			'+213',
			'+1 684',
			'+376',
			'+244',
			'+1 264',
			'+672',
			'+1268',
			'+54',
			'+374',
			'+297',
			'+61',
			'+43',
			'+994',
			'+1 242',
			'+973',
			'+880',
			'+1 246',
			'+375',
			'+32',
			'+501',
			'+229',
			'+1 441',
			'+975',
			'+591',
			'+387',
			'+267',
			'+55',
			'+246',
			'+673',
			'+359',
			'+226',
			'+257',
			'+855',
			'+237',
			'+1',
			'+238',
			'+ 345',
			'+236',
			'+235',
			'+56',
			'+86',
			'+61',
			'+61',
			'+57',
			'+269',
			'+242',
			'+243',
			'+682',
			'+506',
			'+225',
			'+385',
			'+53',
			'+357',
			'+420',
			'+45',
			'+253',
			'+1 767',
			'+1 849',
			'+593',
			'+20',
			'+503',
			'+240',
			'+291',
			'+372',
			'+251',
			'+500',
			'+298',
			'+679',
			'+358',
			'+33',
			'+594',
			'+689',
			'+241',
			'+220',
			'+995',
			'+49',
			'+233',
			'+350',
			'+30',
			'+299',
			'+1 473',
			'+590',
			'+1 671',
			'+502',
			'+44',
			'+224',
			'+245',
			'+595',
			'+509',
			'+379',
			'+504',
			'+852',
			'+36',
			'+354',
			'+91',
			'+62',
			'+98',
			'+964',
			'+353',
			'+44',
			'+972',
			'+39',
			'+1 876',
			'+81',
			'+44',
			'+962',
			'+7 7',
			'+254',
			'+686',
			'+850',
			'+82',
			'+965',
			'+996',
			'+856',
			'+371',
			'+961',
			'+266',
			'+231',
			'+218',
			'+423',
			'+370',
			'+352',
			'+853',
			'+389',
			'+261',
			'+265',
			'+60',
			'+960',
			'+223',
			'+356',
			'+692',
			'+596',
			'+222',
			'+230',
			'+262',
			'+52',
			'+691',
			'+373',
			'+377',
			'+976',
			'+382',
			'+1664',
			'+212',
			'+258',
			'+95',
			'+264',
			'+674',
			'+977',
			'+31',
			'+599',
			'+687',
			'+64',
			'+505',
			'+227',
			'+234',
			'+683',
			'+672',
			'+1 670',
			'+47',
			'+968',
			'+92',
			'+680',
			'+970',
			'+507',
			'+675',
			'+595',
			'+51',
			'+63',
			'+872',
			'+48',
			'+351',
			'+1 939',
			'+974',
			'+40',
			'+7',
			'+250',
			'+262',
			'+590',
			'+290',
			'+1 869',
			'+1 758',
			'+590',
			'+508',
			'+1 784',
			'+685',
			'+378',
			'+239',
			'+966',
			'+221',
			'+381',
			'+248',
			'+232',
			'+65',
			'+421',
			'+386',
			'+677',
			'+252',
			'+27',
			'+500',
			'+34',
			'+94',
			'+249',
			'+597',
			'+47',
			'+268',
			'+46',
			'+41',
			'+963',
			'+886',
			'+992',
			'+255',
			'+66',
			'+670',
			'+228',
			'+690',
			'+676',
			'+1 868',
			'+216',
			'+90',
			'+993',
			'+1 649',
			'+688',
			'+256',
			'+380',
			'+971',
			'+44',
			'+1',
			'+598',
			'+998',
			'+678',
			'+58',
			'+84',
			'+1 284',
			'+1 340',
			'+681',
			'+967',
			'+260',
			'+263',
			'+1 809',
			'+1 829',

		);

		return $data;

	}

	public function vehicles($vid) {
		if ($vid == 0) {
			$exclude = DriverVehicleModel::select('vehicle_id')->get('vehicle_id')->pluck('vehicle_id')->toArray();
		} else {
			$exclude = DriverVehicleModel::select('vehicle_id')->where('vehicle_id', '!=', $vid)->get('vehicle_id')->pluck('vehicle_id')->toArray();
		}

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicles = VehicleModel::whereNotIn('id', $exclude)->get();
		} else {
			$vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->whereNotIn('id', $exclude)->get();
		}
		$details = array();
		foreach ($vehicles as $row) {
			$details[] = array(
				'id' => $row->id,
				'vehicle' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}
		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}

	public function enable_disable(Request $request) {
		$validation = Validator::make($request->all(), [
			'is_active' => 'required|integer',
			'id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {
			$driver = User::find($request->id);
			$driver->is_active = $request->is_active;
			$driver->save();
			$data['success'] = "1";
			$data['message'] = "Driver's active status changed successfully!";
			$data['data'] = "";
		}
		return $data;
	}
}
