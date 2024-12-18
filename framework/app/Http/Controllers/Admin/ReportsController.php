<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\DriverPayments;
use App\Model\ExpCats;
// use App\Model\PartsModel;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceItemsModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\WorkOrders;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller {

	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Reports add', ['only' => ['create']]);
		$this->middleware('permission:Reports edit', ['only' => ['edit']]);
		$this->middleware('permission:Reports delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Reports list');
	}

	public function expense() {
		$years = collect(DB::select("select distinct year(date) as years from expense where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}

		$data['vehicle_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;

		$data['expense'] = Expense::whereIn('vehicle_id', $vehicle_ids)->whereYear("date", date("Y"))->whereMonth("date", date('m'))->get();
		return view('reports.expense', $data);
	}

	public function expense_post(Request $request) {

		$years = collect(DB::select("select distinct year(date) as years from expense where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});
		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}

		$data['vehicle_id'] = $request->vehicle_id;
		$data['year_select'] = $request->year;
		$data['month_select'] = $request->month;
		$data['years'] = $y;

		$records = Expense::whereIn('vehicle_id', $vehicle_ids)->whereYear("date", $request->year)->whereMonth("date", $request->month);
		if ($request->vehicle_id != null) {
			$data['expense'] = $records->where('vehicle_id', $request->vehicle_id)->get();
		} else {
			$data['expense'] = $records->get();
		}
		return view('reports.expense', $data);
	}

	public function expense_print(Request $request) {
		$data['vehicle_id'] = $request->vehicle_id;
		$data['year_select'] = $request->year;
		$data['month_select'] = $request->month;
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::get()->toArray();
		} else {

			$data['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->get()->toArray();
		}
		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}
		$records = Expense::whereIn('vehicle_id', $vehicle_ids)->whereYear("date", $request->year)->whereMonth("date", $request->month);
		if ($request->vehicle_id != null) {
			$data['expense'] = $records->where('vehicle_id', $request->vehicle_id)->get();
		} else {
			$data['expense'] = $records->get();
		}
		return view('reports.print_expense', $data);
	}

	public function income() {

		$years = collect(DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}

		$data['vehicle_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;

		$data['income'] = IncomeModel::whereIn('vehicle_id', $vehicle_ids)->whereYear("date", date("Y"))->whereMonth("date", date('m'))->get();
		return view('reports.income', $data);
	}

	public function income_post(Request $request) {
		$years = collect(DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}

		$data['vehicle_id'] = $request->vehicle_id;
		$data['year_select'] = $request->year;
		$data['month_select'] = $request->month;
		$data['years'] = $y;

		$records = IncomeModel::whereYear("date", $request->year)->whereMonth("date", $request->month);
		if ($request->vehicle_id != null) {
			$data['income'] = $records->where('vehicle_id', $request->vehicle_id)->get();
		} else {
			$data['income'] = $records->whereIn('vehicle_id', $vehicle_ids)->get();
		}
		return view('reports.income', $data);
	}

	public function income_print(Request $request) {
		$data['vehicle_id'] = $request->vehicle_id;
		$data['year_select'] = $request->year;
		$data['month_select'] = $request->month;
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$data['vehicles'] = VehicleModel::get()->toArray();
		} else {
			$data['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->get()->toArray();
		}
		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}

		$records = IncomeModel::whereYear("date", $request->year)->whereMonth("date", $request->month);
		if ($request->vehicle_id != null) {
			$data['income'] = $records->where('vehicle_id', $request->vehicle_id)->get();
		} else {
			$data['income'] = $records->whereIn('vehicle_id', $vehicle_ids)->get();
		}
		return view('reports.print_income', $data);
	}

	public function monthly() {

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		$c = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {

			$y[date('Y')] = date('Y');

		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['vehicle_select'] = null;
		$data['years'] = $y;
		$data['income'] = IncomeModel::select(DB::raw("SUM(amount) as income"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids)->get();
		$data['expenses'] = Expense::select(DB::raw("SUM(amount) as expense"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids)->get();
		$data['expense_by_cat'] = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->groupBy(['expense_type', 'type'])->whereIn('vehicle_id', $vehicle_ids)->get();

		$data['income_by_cat'] = IncomeModel::select("income_cat", DB::raw("sum(amount) as amount"))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->groupBy(['income_cat'])->whereIn('vehicle_id', $vehicle_ids)->get();

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
		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;
		$data['result'] = "";

		return view("reports.monthly", $data);
	}

	public function delinquent() {
		$years = collect(DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});


		$data['vehicle_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;

		return view("reports.delinquent", $data);
	}

	public function booking() {
		$years = collect(DB::select("select distinct year(pickup) as years from bookings where deleted_at is null and pickup is not null order by years desc"));

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		$data['vehicle_select'] = "";
		$data['customer_select'] = "";
		$data['customers'] = User::where('user_type', 'C')->get();
		$data['years'] = $y;
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['bookings'] = Bookings::whereYear("pickup", date("Y"))->whereMonth("pickup", date("n"))->whereIn('vehicle_id', $vehicle_ids)->get();

		return view("reports.booking", $data);
	}

	public function booking_post(Request $request) {
		$years = collect(DB::select("select distinct year(pickup) as years from bookings where deleted_at is null and pickup is not null order by years desc"));

		$y = array();
		$data['customers'] = User::where('user_type', 'C')->get();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		$data['vehicle_select'] = $request->get('vehicle_id');
		$data['customer_select'] = $request->get('customer_id');
		$data['years'] = $y;
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['bookings'] = Bookings::whereYear("pickup", $data['year_select'])->whereMonth("pickup", $data['month_select'])->whereIn('vehicle_id', $vehicle_ids);
		if ($request->get("vehicle_id") != "") {
			$data['bookings'] = $data['bookings']->where("vehicle_id", $request->get("vehicle_id"));
		}
		if ($request->get("customer_id") != "") {
			$data['bookings'] = $data['bookings']->where("customer_id", $request->get("customer_id"));
		}
		$data['bookings'] = $data['bookings']->get();

		return view("reports.booking", $data);
	}
	public function delinquent_post(Request $request) {

		$years = DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc");
		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		foreach ($data['vehicles'] as $row) {
			$data['v'][$row['id']] = $row;
		}

		$data['vehicle_id'] = $request->get("vehicle_id");

		$income = IncomeModel::select(['vehicle_id', 'income_cat', 'date', DB::raw('sum(amount) as Income2,dayname(date) as day')])->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy('date')->orderBy('date');
		if ($data['vehicle_id'] != "") {
			$data['data'] = $income->where('vehicle_id', $data['vehicle_id'])->get();
		} else {
			$data['data'] = $income->whereIn('vehicle_id', $vehicle_ids)->get();
		}

		$data['years'] = $y;
		$data['result'] = "";

		return view("reports.delinquent", $data);
	}

	/*
	    public function parts() {
	    $data['parts'] = PartsModel::get();

	    return view("reports.parts", $data);
	    }
	    public function parts_post(Request $request) {
	    $data['parts'] = PartsModel::get();
	    $data['parts2'] = TransactionModel::wherePart_id($request->get("part"))->get();

	    $data['result'] = "";
	    return view("reports.parts", $data);
	    }
*/

	public function monthly_post(Request $request) {

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		$b = array();
		$i = array();
		$c = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['vehicle_select'] = $request->get("vehicle_id");

		$income1 = IncomeModel::select(DB::raw("SUM(amount) as income"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids);
		$expense1 = Expense::select(DB::raw("SUM(amount) as expense"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids);
		$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids)->groupBy(['expense_type', 'type']);
		$income2 = IncomeModel::select("income_cat", DB::raw("sum(amount) as amount"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->whereIn('vehicle_id', $vehicle_ids)->groupBy(['income_cat']);
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
			$data['income_by_cat'] = $income2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->get();
			$data['expenses'] = $expense1->get();
			$data['expense_by_cat'] = $expense2->get();
			$data['income_by_cat'] = $income2->get();
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
		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;

		$data['years'] = $y;
		$data['result'] = "";
		return view("reports.monthly", $data);
	}

	public function fuel() {
		$years = collect(DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle['id'];
		}
		$data['fuel'] = FuelModel::whereIn('vehicle_id', $vehicle_ids)->get();
		$data['vehicle_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;
		return view('reports.fuel', $data);
	}

	public function fuel_post(Request $request) {

		$years = collect(DB::select("select distinct year(date) as years from income where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});


		$data['vehicle_id'] = $request->get('vehicle_id');
		$data['year_select'] = $request->get('year');
		$data['month_select'] = $request->get('month');
		$data['years'] = $y;
		$v = " and vehicle_id=" . $data['vehicle_id'];
		if ($request->get('month') == '0') {
			$data['fuel'] = FuelModel::whereYear('date', $data['year_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();

		} else {
			$data['fuel'] = FuelModel::whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();
		}

		$data['result'] = "";
		return view('reports.fuel', $data);
	}

	public function yearly() {
		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		$c = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {

			$y[date('Y')] = date('Y');

		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$data['year_select'] = date("Y");

		$data['vehicle_select'] = null;
		$data['years'] = $y;
		$data['income'] = IncomeModel::select(DB::raw('sum(amount) as income'))->whereYear('date', date('Y'))->whereIn('vehicle_id', $vehicle_ids)->get();
		$data['expenses'] = Expense::select(DB::raw('sum(amount) as expense'))->whereYear('date', date("Y"))->whereIn('vehicle_id', $vehicle_ids)->get();
		$data['expense_by_cat'] = Expense::select(['type', 'expense_type', DB::raw('sum(amount) as expense')])->whereYear('date', date('Y'))->whereIn('vehicle_id', $vehicle_ids)->groupBy(['expense_type', 'type'])->get();
		$data['income_by_cat'] = IncomeModel::select(['income_cat', DB::raw('sum(amount) as amount')])->whereYear('date', date('Y'))->whereIn('vehicle_id', $vehicle_ids)->groupBy('income_cat')->get();

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

		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;
		$data['result'] = "";
		return view('reports.yearly', $data);
	}

	public function yearly_post(Request $request) {

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		$b = array();
		$i = array();
		$c = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['year_select'] = $request->get("year");
		$data['vehicle_select'] = $request->get("vehicle_id");

		$income1 = IncomeModel::select(DB::raw("sum(amount) as income"))->whereYear('date', $data['year_select']);
		$expense1 = Expense::select(DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select']);
		$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->groupBy('expense_type', 'type');
		$income2 = IncomeModel::select('income_cat', DB::raw("sum(amount) as amount"))->whereYear('date', $data['year_select'])->groupBy('income_cat');
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
			$data['income_by_cat'] = $income2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expenses'] = $expense1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['income_by_cat'] = $income2->whereIn('vehicle_id', $vehicle_ids)->get();
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

		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;

		$data['years'] = $y;
		$data['result'] = "";
		return view('reports.yearly', $data);
	}

	public function drivers_payments() {
		$bookings = Bookings::whereNotNull('driver_id')->orderBy('bookings.id', 'desc')
			->meta()->where(function ($q) {
			$q->where('bookings_meta.key', 'receipt');
			$q->where('bookings_meta.value', 1);
		})
			->get();

		$index['driver_payments'] = DriverPayments::latest()->get()->toBase()->merge($bookings);
		$index['drivers'] = User::where('user_type', 'D')->has('bookings')->orderBy('name')->pluck('name', 'id')->toArray();

		$driver_bookings = Bookings::whereIn('driver_id', array_keys($index['drivers']))->get();

		$driver_remaining_amounts = User::where('user_type', 'D')->has('bookings')->get();

		$driver_amount = [];

		foreach ($driver_remaining_amounts as $dram) {
			$driver_amount[$dram->id]['data-remaining-amount'] = $dram->remaining_amount;
		}

		foreach ($driver_bookings as $am) {
			$amount = $am->driver_amount ?? $am->tax_total;

			if (!empty($driver_amount[$am->driver_id]['data-amount'])) {
				$driver_amount[$am->driver_id]['data-amount'] = $driver_amount[$am->driver_id]['data-amount'] + $amount;
			} else {
				$driver_amount[$am->driver_id]['data-amount'] = $amount;
			}
		}
		$index['driver_booking_amount'] = $driver_amount;
		// dd($index);
		return view('reports.driver_payments', $index);
	}

	public function drivers_payments_post(Request $request) {
		$this->validate($request, [
			'driver' => 'required',
			'amount' => 'required',
		]);
		DriverPayments::create([
			'user_id' => auth()->id(),
			'driver_id' => $request->driver,
			'amount' => $request->amount,
			'notes' => $request->notes,
		]);
		$driver = User::find($request->driver);
		$remainig_amount_after_saved = $request->remaining_amount_hidden - $request->amount;
		$driver->remaining_amount = $remainig_amount_after_saved;
		$driver->save();

		return back()->with('msg', trans('fleet.driverPaymentAdded'));
	}

	public function vendors() {

		// $data['details'] = DB::select("select vendor_id,sum(price) as total from work_orders where deleted_at is null group by vendor_id"));
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['details'] = WorkOrders::select(['vendor_id', DB::raw('sum(price) as total')])->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();
		// dd($data);

		$kk = WorkOrders::select('vendor_id')->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();
		$b = array();
		foreach ($kk as $k) {
			$b[$k->vendor_id] = $k->vendor->name;

		}
		$data['vendors'] = $b;
		$data['date1'] = null;
		$data['date2'] = null;
		return view('reports.vendor', $data);
	}

	public function vendors_post(Request $request) {

		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$start = date('Y-m-d H:i:s', strtotime($request->get('date1')));

		$end = date('Y-m-d H:i:s', strtotime($request->get('date2')));
		$data['details'] = WorkOrders::select(['vendor_id', DB::raw('sum(price) as total')])->whereBetween('created_at', [$start, $end])->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();

		$kk = WorkOrders::select('vendor_id')->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();
		$b = array();
		foreach ($kk as $k) {
			$b[$k->vendor_id] = $k->vendor->name;

		}
		$data['vendors'] = $b;
		$data['date1'] = $request->date1;
		$data['date2'] = $request->date2;
		return view('reports.vendor', $data);

	}

	public function drivers() {

		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		// data of current month and current year
		$drivers = Bookings::select(['id', 'driver_id', 'vehicle_id'])->where('status', 1)->whereIn('vehicle_id', $vehicle_ids)->groupBy('driver_id')->get();
		$drivers_by_year = array();
		foreach ($drivers as $d) {
			$drivers_by_year[$d->driver->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", date("Y"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}
		// dd($drivers_by_year);
		$data['drivers_by_year'] = $drivers_by_year;
		$drivers_by_month = array();
		foreach ($drivers as $d) {
			$drivers_by_month[$d->driver->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", date("Y"))->whereMonth("bookings.updated_at", date("n"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}
		$data['drivers_by_month'] = $drivers_by_month;
		// dd($drivers_by_month);
		$years = collect(DB::select("select distinct year(created_at) as years from bookings where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}

		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;
		return view('reports.driver', $data);

	}

	public function drivers_post(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
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
		// dd($drivers_by_year);
		$data['drivers_by_year'] = $drivers_by_year;

		$drivers_by_month = array();
		foreach ($drivers as $d) {
			$drivers_by_month[$d->driver->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", $request->get('year'))->whereMonth("bookings.updated_at", $request->get("month"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}

		// dd($drivers_by_month);
		$data['drivers_by_month'] = $drivers_by_month;

		$years = collect(DB::select("select distinct year(created_at) as years from bookings where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['years'] = $y;

		return view('reports.driver', $data);

	}

	public function customers() {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		// data of current month and current year
		$customers = Bookings::select(['id', 'customer_id', 'vehicle_id'])->where('status', 1)->whereIn('vehicle_id', $vehicle_ids)->groupBy('customer_id')->get();
		$customers_by_year = array();
		foreach ($customers as $d) {
			$customers_by_year[$d->customer->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", date("Y"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}

		$data['customers_by_year'] = $customers_by_year;
		arsort($customers_by_year);
		$data['top10'] = array_slice($customers_by_year, 0, 10);

		$customers_by_month = array();
		foreach ($customers as $d) {
			$customers_by_month[$d->customer->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", date("Y"))->whereMonth("bookings.updated_at", date("n"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}
		$data['customers_by_month'] = $customers_by_month;
		$years = collect(DB::select("select distinct year(created_at) as years from bookings where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}

		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;
		return view('reports.customer', $data);

	}

	public function customers_post(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
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
		$data['customers_by_year'] = $customers_by_year;

		arsort($customers_by_year);
		$data['top10'] = array_slice($customers_by_year, 0, 10);
		$customers_by_month = array();
		foreach ($customers as $d) {
			$customers_by_month[$d->customer->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", $request->get("year"))->whereMonth("bookings.updated_at", $request->get("month"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}
		$data['customers_by_month'] = $customers_by_month;

		$years = collect(DB::select("select distinct year(created_at) as years from bookings where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['years'] = $y;

		return view('reports.customer', $data);

	}

	public function print_deliquent(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		foreach ($data['vehicles'] as $row) {
			$data['v'][$row['id']] = $row;
		}

		$data['vehicle_id'] = $request->get("vehicle_id");
		$income = IncomeModel::select(['vehicle_id', 'income_cat', 'date', DB::raw('sum(amount) as Income2,dayname(date) as day')])->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy('date')->orderBy('date');
		if ($data['vehicle_id'] != "") {
			$data['data'] = $income->where('vehicle_id', $data['vehicle_id'])->get();
		} else {
			$data['data'] = $income->whereIn('vehicle_id', $vehicle_ids)->get();
		}

		$data['vehicle'] = VehicleModel::find($request->get('vehicle_id'));
		return view('reports.print_delinquent', $data);
	}

	public function print_monthly(Request $request) {
		$b = array();
		$i = array();
		$c = array();

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['vehicle_select'] = $request->get("vehicle_id");
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$income1 = IncomeModel::select(DB::raw("SUM(amount) as income"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
		$expense1 = Expense::select(DB::raw("SUM(amount) as expense"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
		$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy(['expense_type', 'type']);
		$income2 = IncomeModel::select("income_cat", DB::raw("sum(amount) as amount"))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy(['income_cat']);
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
			$data['income_by_cat'] = $income2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expenses'] = $expense1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['income_by_cat'] = $income2->whereIn('vehicle_id', $vehicle_ids)->get();
		}

		$kk = ExpCats::get();
		$ss = ServiceItemsModel::get();
		foreach ($ss as $s) {
			$c[$s->id] = $s->description;
		}

		foreach ($kk as $k) {
			$b[$k->id] = $k->name;

		}

		$hh = IncCats::get();

		foreach ($hh as $k) {
			$i[$k->id] = $k->name;

		}
		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;

		$data['vehicle'] = VehicleModel::find($request->get("vehicle_id"));

		return view('reports.print_monthly', $data);

	}

	public function print_booking(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['customers'] = User::where('user_type', 'C')->get();
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['bookings'] = Bookings::whereMonth("pickup", $data['month_select'])->whereMonth("pickup", $data['month_select'])->whereIn('vehicle_id', $vehicle_ids);
		if ($request->get("vehicle_id") != "") {
			$data['bookings'] = $data['bookings']->where("vehicle_id", $request->get("vehicle_id"));
		}
		if ($request->get("customer_id") != "") {
			$data['bookings'] = $data['bookings']->where("customer_id", $request->get("customer_id"));
		}
		$data['bookings'] = $data['bookings']->get();
		return view('reports.print_bookings', $data);
	}

	public function print_fuel(Request $request) {

		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});


		$data['vehicle_id'] = $request->get('vehicle_id');
		$data['year_select'] = $request->get('year');
		$data['month_select'] = $request->get('month');

		if ($request->get('month') == '0') {
			$data['fuel'] = FuelModel::whereYear('date', $data['year_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();

		} else {
			$data['fuel'] = FuelModel::whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();
		}
		$data['vehicle'] = VehicleModel::find($request->get('vehicle_id'));

		return view('reports.print_fuel', $data);
	}

	public function print_yearly(Request $request) {

		$b = array();
		$i = array();
		$c = array();

		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$data['year_select'] = $request->get("year");
		$data['vehicle_select'] = $request->get("vehicle_id");

		$income1 = IncomeModel::select(DB::raw("sum(amount) as income"))->whereYear('date', $data['year_select']);
		$expense1 = Expense::select(DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select']);
		$expense2 = Expense::select("type", "expense_type", DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->groupBy('expense_type', 'type');
		$income2 = IncomeModel::select('income_cat', DB::raw("sum(amount) as amount"))->whereYear('date', $data['year_select'])->groupBy('income_cat');
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
			$data['income_by_cat'] = $income2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expenses'] = $expense1->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $vehicle_ids)->get();
			$data['income_by_cat'] = $income2->whereIn('vehicle_id', $vehicle_ids)->get();
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

		$data['service'] = $c;
		$data['expense_cats'] = $b;
		$data['income_cats'] = $i;

		$data['vehicle'] = VehicleModel::find($request->get('vehicle_id'));

		return view('reports.print_yearly', $data);
	}

	public function print_driver(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$drivers = Bookings::select(['id', 'driver_id', 'vehicle_id'])->whereIn('vehicle_id', $vehicle_ids)->where('status', 1)->groupBy('driver_id')->get();

		$drivers_by_month = array();
		foreach ($drivers as $d) {
			$drivers_by_month[$d->driver->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", $request->get('year'))->whereMonth("bookings.updated_at", $request->get("month"))->where('driver_id', $d->driver_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}

		$data['drivers_by_month'] = $drivers_by_month;

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");

		return view('reports.print_driver', $data);
	}

	public function print_vendor() {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['details'] = WorkOrders::select(['vendor_id', DB::raw('sum(price) as total')])->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();

		$kk = WorkOrders::select('vendor_id')->whereIn('vehicle_id', $vehicle_ids)->groupBy('vendor_id')->get();
		$b = array();
		foreach ($kk as $k) {
			$b[$k->vendor_id] = $k->vendor->name;

		}
		$data['vendors'] = $b;

		return view('reports.print_vendor', $data);
	}

	public function print_customer(Request $request) {
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$customers = Bookings::select(['id', 'customer_id', 'vehicle_id'])->where('status', 1)->whereIn('vehicle_id', $vehicle_ids)->groupBy('customer_id')->get();
		$customers_by_year = array();
		foreach ($customers as $d) {
			$customers_by_year[$d->customer->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", $request->get("year"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}
		$data['customers_by_year'] = $customers_by_year;

		arsort($customers_by_year);
		$data['top10'] = array_slice($customers_by_year, 0, 10);

		$customers_by_month = array();
		foreach ($customers as $d) {
			$customers_by_month[$d->customer->name] = Bookings::meta()
				->where(function ($query) {
					$query->where('bookings_meta.key', '=', 'tax_total');

				})->whereYear("bookings.updated_at", $request->get("year"))->whereMonth("bookings.updated_at", $request->get("month"))->where('customer_id', $d->customer_id)->whereIn('vehicle_id', $vehicle_ids)->sum('value');

		}

		$data['customers_by_month'] = $customers_by_month;

		$years = collect(DB::select("select distinct year(created_at) as years from bookings where deleted_at is null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['years'] = $y;
		return view('reports.print_customer', $data);
	}

	public function users() {
		$years = collect(DB::select("select distinct year(pickup) as years from bookings where deleted_at is null and pickup is not null order by years desc"))->toArray();

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['users'] = User::where('user_type', 'O')->orWhere('user_type', 'S')->get();
		$data['user_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;
		return view('reports.users', $data);
	}

	public function users_post(Request $request) {
		$years = DB::select("select distinct year(pickup) as years from bookings where deleted_at is null and pickup is not null order by years desc");
		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['users'] = User::where('user_type', 'O')->orWhere('user_type', 'S')->get();
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");

		$data['user_id'] = $request->get("user_id");
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}

		$data['data'] = Bookings::whereYear('pickup', $data['year_select'])->whereMonth('pickup', $data['month_select'])->where('user_id', $request->get('user_id'))->whereIn('vehicle_id', $vehicle_ids)->get();

		$data['years'] = $y;
		$data['result'] = "";

		return view("reports.users", $data);
	}

	public function print_users(Request $request) {

		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		
		$data['vehicles'] = VehicleModel::when(Auth::user()->group_id == null || Auth::user()->user_type == "S", function ($query) {
			// Apply this query when the condition is true (role is false or null)
			return $query->get();
		}, function ($query) {
			// Apply this query when the condition is false (role is provided)
			return $query->where('group_id', Auth::user()->group_id)->get();
		});

		$vehicle_ids = array(0);
		foreach ($data['vehicles'] as $vehicle) {
			$vehicle_ids[] = $vehicle->id;
		}
		$data['data'] = Bookings::whereYear('pickup', $data['year_select'])->whereMonth('pickup', $data['month_select'])->where('user_id', $request->get('user_id'))->whereIn('vehicle_id', $vehicle_ids)->get();

		return view('reports.print_users', $data);
	}

	public function work_order() {
		$years = DB::select("select distinct year(required_by) as years from work_orders where deleted_at is null and required_by is not null order by years desc");

		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['vehicle'] = VehicleModel::all();
		$data['vehicle_id'] = "";
		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['years'] = $y;
		return view('reports.work_order', $data);
	}

	public function work_order_post(Request $request) {
		$years = DB::select("select distinct year(required_by) as years from work_orders where deleted_at is null and required_by is not null order by years desc");
		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['vehicle'] = VehicleModel::all();
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");

		$data['vehicle_id'] = $request->get("vehicle_id");

		$data['data'] = WorkOrders::whereYear('required_by', $data['year_select'])->whereMonth('required_by', $data['month_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();

		$data['years'] = $y;
		$data['result'] = "";

		return view("reports.work_order", $data);
	}

	public function print_workOrder_report(Request $request) {
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");

		$data['data'] = WorkOrders::whereYear('required_by', $data['year_select'])->whereMonth('required_by', $data['month_select'])->where('vehicle_id', $request->get('vehicle_id'))->get();
		return view("reports.print_work_order", $data);
	}
}
