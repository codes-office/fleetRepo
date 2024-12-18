<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceItemsModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\WorkOrders;
use Auth;
use DB;
use Exporter;
use Hyvikk;
use Illuminate\Http\Request;
use Validator;

class ReportsApiController extends Controller {

	public function export() {
		$yourCollection = DB::table('users')->select('id', 'name', 'email');
		$yourCollection = $collection = collect([
			(object) [
				'website' => 'twitter',
				'url' => 'twitter.com',
			],
			(object) [
				'website' => 'google',
				'url' => 'google.com',
			],
		]);
		$excel = Exporter::make('Excel');
		$excel->load($yourCollection);
		$excel->save('exports/test.xlsx');
		return url('exports/test.xlsx');
	}

	public function reports_dropdown() {

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$vehicle_ids = VehicleModel::pluck('id')->toArray();
		} else {
			$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
		}

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
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
		$customers = array();
		$all_customers = User::where('user_type', 'C')->get();
		foreach ($all_customers as $row) {
			$customers[] = array(
				'id' => $row->id,
				'name' => $row->name,
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'vehicles' => $vehicle_list,
			'years' => $y,
			'customers' => $customers,
		);
		return $data;
	}

	public function yearly(Request $request) {
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
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

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
			$all_income_by_cats = array();
			$total_income_by_cat = 0;
			$income_by_category = array();
			$income_labels = array();
			$income_values = array();
			foreach ($income_by_cat as $exp) {
				$all_income_by_cats[$income_cats[$exp->income_cat]] = $exp->amount;
				$income_by_category[] = array(
					'name' => $income_cats[$exp->income_cat],
					'value' => $exp->amount,
				);
				$total_income_by_cat += $exp->amount;
				$income_labels[] = $income_cats[$exp->income_cat];
				$income_values[] = $exp->amount;
			}
			// dd($all_income_by_cats);
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
				$expense_by_category[] = array('name' => $typename, 'value' => $exp->expense);
				$total_expense_by_cat += $exp->expense;
				$expense_labels[] = $typename;
				$expense_values[] = $exp->expense;
			}

			$records = array(
				'currency' => Hyvikk::get("currency"),
				'income' => array(
					'profit_loss' => ($income_amt - $expense_amt),
					'income' => $income_amt,
					'expenses' => $expense_amt,
					'labels' => array('Profit/Loss', 'Income', 'Expenses'),
					'values' => array($income_amt - $expense_amt, $income_amt, $expense_amt),
				),
				'income_by_category' => array(
					'total_amount' => $total_income_by_cat,
					// 'records' => $all_income_by_cats,
					'records' => $income_by_category,
					'income_labels' => $income_labels,
					'income_values' => $income_values,
				),
				'expense_by_category' => array(
					'total_amount' => $total_expense_by_cat,
					// 'records' => $all_expense_by_cats,
					'records' => $expense_by_category,
					'expense_labels' => $expense_labels,
					'expense_values' => $expense_values,
				),
			);

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function vendor(Request $request) {
		$records = array();
		if (isset($request->start_date) && isset($request->end_date)) {
			$start = date('Y-m-d H:i:s', strtotime($request->start_date));
			$end = date('Y-m-d H:i:s', strtotime($request->end_date . "+1 day"));
			$results = DB::select("select vendor_id,sum(price) as total from work_orders where deleted_at is null and created_at between '" . $start . "' and '" . $end . "' group by vendor_id");
		} else {
			$results = DB::select("select vendor_id,sum(price) as total from work_orders where deleted_at is null group by vendor_id");
		}
		$vv = WorkOrders::select('vendor_id')->groupBy('vendor_id')->get();

		foreach ($vv as $v) {
			$b[$v->vendor_id] = $v->vendor['name'];
		}
		$vendors = $b;
		$table = array();
		$labels = array();
		$values = array();
		foreach ($results as $d) {
			$records[] = array(
				'vendor' => $vendors[$d->vendor_id],
				'currency' => Hyvikk::get('currency'),
				'total' => $d->total,
			);
			$table[] = array(
				'name' => $vendors[$d->vendor_id],
				'value' => $d->total,
			);
			$labels[] = $vendors[$d->vendor_id];
			$values[] = $d->total;
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = array(
			'records' => $records,
			'table' => $table,
			'labels' => $labels,
			'values' => $values,
			'currency' => Hyvikk::get('currency'),
		);
		return $data;
	}

	public function customer(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}
			// data of selected month and year
			$customers = Bookings::select(['id', 'customer_id', 'vehicle_id'])->where('status', 1)->whereIn('vehicle_id', $vehicle_ids)->groupBy('customer_id')->get();
			$customers_by_year = array();
			foreach ($customers as $d) {
				$customers_by_year[$d->customer->name] = Bookings::meta()
					->where(function ($query) {
						$query->where('bookings_meta.key', '=', 'tax_total');

					})->whereYear("bookings.updated_at", $request->get("year"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

			}

			arsort($customers_by_year);

			$customers_by_month = array();
			foreach ($customers as $d) {
				$customers_by_month[$d->customer->name] = Bookings::meta()
					->where(function ($query) {
						$query->where('bookings_meta.key', '=', 'tax_total');

					})->whereYear("bookings.updated_at", $request->get("year"))->whereMonth("bookings.updated_at", $request->get("month"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

			}

			$table = array();
			$labels = array();
			$values = array();
			foreach ($customers_by_month as $key => $val) {

				$labels[] = $key;
				$values[] = $val;
			}
			$top10 = array_slice($customers_by_year, 0, 10);
			foreach ($top10 as $key => $val) {
				$table[] = array(
					'name' => $key,
					'value' => $val,
				);
			}
			$yearly_table = array();
			$yearly_labels = array();
			$yearly_values = array();
			foreach ($customers_by_year as $key => $val) {
				$yearly_table[] = array(
					'name' => $key,
					'value' => $val,
				);
				$yearly_labels[] = $key;
				$yearly_values[] = $val;
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array(
				'customers_by_month' => $customers_by_month,
				'customers_by_year' => $customers_by_year,
				'top10' => array_slice($customers_by_year, 0, 10),
				'table' => $table,
				'labels' => $labels,
				'values' => $values,
				'yearly_chart' => array(
					'yearly_table' => $yearly_table,
					'yearly_labels' => $yearly_labels,
					'yearly_values' => $yearly_values,
				),
			);
		}
		return $data;

	}

	public function driver(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			// data of selected month and year
			$drivers = Bookings::select(['id', 'driver_id', 'vehicle_id'])->where('status', 1)->whereIn('vehicle_id', $vehicle_ids)->groupBy('driver_id')->get();
			$drivers_by_year = array();
			foreach ($drivers as $d) {
				$drivers_by_year[$d->driver->name] = Bookings::meta()
					->where(function ($query) {
						$query->where('bookings_meta.key', '=', 'tax_total');

					})->whereYear("bookings.updated_at", $request->get("year"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

			}

			$drivers_by_month = array();
			foreach ($drivers as $d) {
				$drivers_by_month[$d->driver->name] = Bookings::meta()
					->where(function ($query) {
						$query->where('bookings_meta.key', '=', 'tax_total');

					})->whereYear("bookings.updated_at", $request->get('year'))->whereMonth("bookings.updated_at", $request->get("month"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

			}
			$table = array();
			$labels = array();
			$values = array();
			foreach ($drivers_by_month as $key => $val) {
				$table[] = array(
					'name' => $key,
					'value' => $val,
				);
				$labels[] = $key;
				$values[] = $val;
			}

			$yearly_table = array();
			$yearly_labels = array();
			$yearly_values = array();
			foreach ($drivers_by_year as $key => $val) {
				$yearly_table[] = array(
					'name' => $key,
					'value' => $val,
				);
				$yearly_labels[] = $key;
				$yearly_values[] = $val;
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array(
				'drivers_by_month' => $drivers_by_month,
				'drivers_by_year' => $drivers_by_year,
				'table' => $table,
				'labels' => $labels,
				'values' => $values,
				'yearly_chart' => array(
					'yearly_table' => $yearly_table,
					'yearly_labels' => $yearly_labels,
					'yearly_values' => $yearly_values,
				),
			);
		}
		return $data;

	}

	public function fuel(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
			'vehicle_id' => 'required|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();
			$vehicle_id = $request->vehicle_id;
			$year_select = $request->year;
			$month_select = $request->month;
			if ($request->month == '0') {
				$fuel = FuelModel::whereYear('date', $year_select)->where('vehicle_id', $request->vehicle_id)->get();
			} else {
				$fuel = FuelModel::whereYear('date', $year_select)->whereMonth('date', $month_select)->where('vehicle_id', $request->vehicle_id)->get();
			}
			foreach ($fuel as $row) {
				if (Hyvikk::get('dis_format') == "km") {
					if (Hyvikk::get('fuel_unit') == "gallon") {
						$unit = "KMPG";
					} else {
						$unit = "KMPL";
					}
				} else {
					if (Hyvikk::get('fuel_unit') == "gallon") {
						$unit = "MPG";
					} else {
						$unit = "MPL";
					}
				}

				$records[] = array(
					'date' => date('Y-m-d', strtotime($row->date)),
					'vehicle' => $row->vehicle_data->make_name . "-" . $row->vehicle_data->model_name . "-" . $row->vehicle_data->license_plate,
					'start_meter' => $row->start_meter . " " . Hyvikk::get('dis_format'),
					'end_meter' => $row->end_meter . " " . Hyvikk::get('dis_format'),
					'consumption' => $row->consumption . " " . $unit,
					'cost' => Hyvikk::get('currency') . " " . $row->qty * $row->cost_per_unit,
				);
			}
			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function user(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
			'user_id' => 'required|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();

			$year_select = $request->year;
			$month_select = $request->month;
			$user_id = $request->user_id;
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			$bookings = Bookings::whereYear('pickup', $year_select)->whereMonth('pickup', $month_select)->where('user_id', $request->user_id)->whereIn('vehicle_id', $vehicle_ids)->get();

			foreach ($bookings as $row) {
				$records[] = array(
					'booked_by' => $row->user->name,
					'pickup_address' => $row->pickup_addr,
					'dropoff_address' => $row->dest_addr,
					'pickup_datetime' => date('Y-m-d H:i:s', strtotime($row->pickup)),
					'dropoff_datetime' => date('Y-m-d H:i:s', strtotime($row->dropoff)),
					'journey_status' => ($row->status == 1) ? "Completed" : "Not Completed",
					'amount' => ($row->receipt == 1)?Hyvikk::get('currency') . " " . round($row->tax_total, 2): null,
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function booking(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
			'vehicle_id' => 'nullable|integer',
			'customer_id' => 'nullable|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();

			$vehicle_select = $request->vehicle_id;
			$customer_select = $request->customer_id;
			$year_select = $request->year;
			$month_select = $request->month;
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			$bookings = Bookings::whereYear("pickup", $year_select)->whereMonth("pickup", $month_select)->whereIn('vehicle_id', $vehicle_ids);
			if ($request->vehicle_id) {
				$bookings = $bookings->where("vehicle_id", $request->vehicle_id);
			}
			if ($request->customer_id) {
				$bookings = $bookings->where("customer_id", $request->customer_id);
			}
			$bookings = $bookings->get();

			foreach ($bookings as $row) {
				$records[] = array(
					'customer' => $row->customer->name,
					'vehicle' => ($row->vehicle_id) ? $row->vehicle->make_name . "-" . $row->vehicle->model_name . "-" . $row->vehicle->license_plate : "",
					'pickup_address' => $row->pickup_addr,
					'dropoff_address' => $row->dest_addr,
					'from_datetime' => date('Y-m-d H:i:s', strtotime($row->pickup)),
					'to_datetime' => date('Y-m-d H:i:s', strtotime($row->dropoff)),
					'passengers' => $row->travellers,
					'status' => ($row->status == 0) ? "Journey not ended yet" : "Journey ended",
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = array('booking_count' => $bookings->count(), 'records' => $records);
		}
		return $data;
	}

	public function monthly(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|integer',
			'month' => 'required|integer',
			'vehicle_id' => 'nullable|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();

			$y = array();
			$b = array();
			$i = array();
			$c = array();

			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

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
			$all_income_by_cats = array();
			$total_income_by_cat = 0;
			$income_by_category = array();
			$income_labels = array();
			$income_values = array();
			foreach ($income_by_cat as $exp) {
				$all_income_by_cats[$income_cats[$exp->income_cat]] = $exp->amount;
				$income_by_category[] = array(
					'name' => $income_cats[$exp->income_cat],
					'value' => $exp->amount,
				);
				$total_income_by_cat += $exp->amount;
				$income_labels[] = $income_cats[$exp->income_cat];
				$income_values[] = $exp->amount;
			}
			// dd($all_income_by_cats);
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
				$expense_by_category[] = array('name' => $typename, 'value' => $exp->expense);
				$total_expense_by_cat += $exp->expense;
				$expense_labels[] = $typename;
				$expense_values[] = $exp->expense;
			}

			$records = array(
				'currency' => Hyvikk::get("currency"),
				'income' => array(
					'profit_loss' => ($income_amt - $expense_amt),
					'income' => $income_amt,
					'expenses' => $expense_amt,
					'labels' => array('Profit/Loss', 'Income', 'Expenses'),
					'values' => array($income_amt - $expense_amt, $income_amt, $expense_amt),
				),
				'income_by_category' => array(
					'total_amount' => $total_income_by_cat,
					// 'records' => $all_income_by_cats,
					'records' => $income_by_category,
					'income_labels' => $income_labels,
					'income_values' => $income_values,
				),
				'expense_by_category' => array(
					'total_amount' => $total_expense_by_cat,
					// 'records' => $all_expense_by_cats,
					'records' => $expense_by_category,
					'expense_labels' => $expense_labels,
					'expense_values' => $expense_values,
				),
			);
			// dd($records);
			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function delinquent(Request $request) {
		$validation = Validator::make($request->all(), [
			'year' => 'required|numeric',
			'month' => 'required|numeric',
			'vehicle_id' => 'required|integer',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";

		} else {
			$records = array();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			$year_select = $request->year;
			$month_select = $request->month;
			$vehicle_id = $request->vehicle_id;

			$income = IncomeModel::select(['vehicle_id', 'income_cat', 'date', DB::raw('sum(amount) as Income2,dayname(date) as day')])->whereYear('date', $year_select)->whereMonth('date', $month_select)->groupBy('date')->orderBy('date');
			if ($vehicle_id) {
				$income = $income->where('vehicle_id', $vehicle_id)->get();
			} else {
				$income = $income->whereIn('vehicle_id', $vehicle_ids)->get();
			}
			foreach ($income as $row) {
				$records[] = array(
					'day' => $row->day,
					'date' => date('Y-m-d', strtotime($row->date)),
					'income' => Hyvikk::get('currency') . " " . $row->Income2,
					'vehicle' => $row->vehicle->make_name . '-' . $row->vehicle->model_name . '-' . $row->vehicle->license_plate,
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $records;
		}
		return $data;
	}

	public function expense(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'nullable|integer',
			'year' => 'required|integer',
			'month' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$expense_records = array();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			$records = Expense::whereIn('vehicle_id', $vehicle_ids)->whereYear("date", $request->year)->whereMonth("date", $request->month);
			if ($request->vehicle_id != null) {
				$expense = $records->where('vehicle_id', $request->vehicle_id)->get();
			} else {
				$expense = $records->get();
			}
			foreach ($expense as $row) {
				if ($row->type == "s") {
					$category = $row->service->description;
				} else {
					$category = $row->category->name;
				}

				$expense_records[] = array(
					'vehicle_make' => $row->vehicle->make_name,
					'vehicle_model' => $row->vehicle->model_name,
					'license_plate' => $row->vehicle->license_plate,
					'expense_type' => $category,
					'date' => date('Y-m-d', strtotime($row->date)),
					'amount' => Hyvikk::get('currency') . " " . $row->amount,
					'note' => $row->comment,
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $expense_records;
		}
		return $data;
	}

	public function income(Request $request) {
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'nullable|integer',
			'year' => 'required|integer',
			'month' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$income_records = array();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
			}

			$records = IncomeModel::whereYear("date", $request->year)->whereMonth("date", $request->month);
			if ($request->vehicle_id) {
				$income = $records->where('vehicle_id', $request->vehicle_id)->get();
			} else {
				$income = $records->whereIn('vehicle_id', $vehicle_ids)->get();
			}
			foreach ($income as $row) {
				$income_records[] = array(
					'vehicle_make' => $row->vehicle->make_name,
					'vehicle_model' => $row->vehicle->model_name,
					'license_plate' => $row->vehicle->license_plate,
					'income_type' => $row->category->name,
					'date' => date('Y-m-d', strtotime($row->date)),
					'amount' => Hyvikk::get('currency') . " " . $row->amount,
					'mileage' => $row->mileage,
				);
			}

			$data['success'] = "1";
			$data['message'] = "Data fetched!";
			$data['data'] = $income_records;
		}
		return $data;
	}

}
