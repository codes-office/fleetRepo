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
use App\Model\Expense;
// use App\Model\PartsModel;
use App\Model\IncomeModel;
use App\Model\ReviewModel;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Model\Vendor;
use Auth;
use DB;
use Hyvikk;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller {

	public function export_calendar() {
		$bookings = Bookings::where('pickup', '!=', null)->where('dropoff', '!=', null)->get();
		$vCalendar = new \Eluceo\iCal\Component\Calendar ("Fleet manager");
		foreach ($bookings as $booking) {
			$vehicle = null;
			if ($booking->vehicle_id != null) {
				$vehicle = $booking->vehicle->make_name . " -" . $booking->vehicle->model_name . "-" . $booking->vehicle->license_plate;
			}
			$vEvent = new \Eluceo\iCal\Component\Event ();
			$vEvent
				->setDtStart(new \DateTime ($booking->pickup))
				->setDtEnd(new \DateTime ($booking->dropoff))
				->setNoTime(true)
				->setSummary($booking->customer->name)
				->setDescription("Customer: " . $booking->customer->name . "\nVehicle: " . $vehicle . "\nTravellers: " . $booking->travellers . "\nNote: " . $booking->note . "\nPickup Date & Time: " . date('d/m/Y g:i A', strtotime($booking->pickup)) . "\nDropoff Date & Time: " . date('d/m/Y g:i A', strtotime($booking->dropoff)) . "\nPickup Address: " . $booking->pickup_addr . "\nDestination Address: " . $booking->dest_addr);

			$vCalendar->addComponent($vEvent);
		}

		$reminders = ServiceReminderModel::get();
		foreach ($reminders as $r) {
			$interval = substr($r->services->overdue_unit, 0, -3);
			$int = $r->services->overdue_time . $interval;
			if ($r->last_meter == 0) {
				$next_due = $r->vehicle->int_mileage + $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
			} else {
				$next_due = $r->last_meter + $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
			}
			$interval = $r->services->overdue_time . " " . $r->services->overdue_unit;
			if ($r->services->overdue_meter != null) {
				$interval .= $r->services->overdue_meter . " " . Hyvikk::get('dis_format');
			}

			$date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
			$vEvent = new \Eluceo\iCal\Component\Event ();
			$vEvent
				->setDtStart(new \DateTime ($date))
				->setDtEnd(new \DateTime ($date))
				->setNoTime(true)
				->setSummary($r->services->description)
				->setDescription("Vehicle: " . $r->vehicle->make_name . "-" . $r->vehicle->model_name . "-" . $r->vehicle->license_plate . "\n Service Item: " . $r->services->description . "\n Next due(meter):" . $next_due . "\n Next due(date): " . $date . "\n Last performed: Date:" . $r->last_date . ", meter: $r->last_meter" . "\n Interval: " . $interval);
			// ->setDescriptionHTML("<b>html text</b>");
			$vCalendar->addComponent($vEvent);
		}
		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="calendar.ics"');
		echo $vCalendar->render();
	}

	public function cal() {
		$vCalendar = new \Eluceo\iCal\Component\Calendar ('www.example.com');
		$vEvent = new \Eluceo\iCal\Component\Event ();
		$vEvent
			->setDtStart(new \DateTime ('2020-02-05'))
			->setDtEnd(new \DateTime ('2020-02-05'))
			->setNoTime(true)
			->setSummary('testing1')
		;
		$vEvent1 = new \Eluceo\iCal\Component\Event ();

		$vEvent1
			->setDtStart(new \DateTime ('2020-02-09'))
			->setDtEnd(new \DateTime ('2020-02-09'))
			->setNoTime(true)
			->setSummary('testing2')
		;
		$vCalendar->addComponent($vEvent);
		$vCalendar->addComponent($vEvent1);
		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="cal.ics"');
		echo $vCalendar->render();
	}

	public function index() {
		if (Auth::user()->user_type == "D") {
			// $drivers = User::where('user_type', 'D')->get();
			// foreach ($drivers as $driver) {
			//     $d = User::find($driver->id);
			//     $d->givePermissionTo(['Notes add','Notes edit','Notes delete','Notes list','Drivers list']);
			// }
			$index['data'] = User::whereId(Auth::user()->id)->first();
			$index['bookings'] = Bookings::orderBy('id', 'desc')->where('driver_id', Auth::user()->id)->get();

			$all_rides = Bookings::whereDriver_id(Auth::user()->id)->get()->count();
			$cancel_rides = Bookings::whereDriver_id(Auth::user()->id)->whereMeta('ride_status','Cancelled')->get()->count();
			// $index['total'] = Bookings::whereDriver_id(Auth::user()->id)->whereMeta('ride_status','!=','Cancelled')->get()->count();
			$index['total'] = $all_rides - $cancel_rides;
			// $index['vehicle'] = VehicleModel::where('driver_id', Auth::user()->id)->first();
			return view("drivers.profile", $index);

		} elseif (Auth::user()->user_type == "C") {
			if (Hyvikk::frontend('enable') == 1) {
				return redirect('/');
			}
			$data['total_kms'] = IncomeModel::select(DB::raw('sum(mileage) as total_kms'))->where('user_id', Auth::id())->get();
			$data['income'] = IncomeModel::select(DB::raw('sum(amount) as income'))->where('user_id', Auth::id())->get();

			$data['time'] = 0;
			$data['travel_time'] = 0;
			$bookings = Bookings::where('customer_id', Auth::user()->id)->get();
			foreach ($bookings as $b) {
				if ($b->status == 1) {
					$data['time'] += $b->getMeta('waiting_time');
					$times = explode(" ", $b->getMeta('driving_time'));
					if (sizeof($times) == 2) {
						if (starts_with($times[1], 'hour')) {
							$data['travel_time'] += $times[0] * 60;
						}

						if (starts_with($times[1], 'min')) {
							$data['travel_time'] += $times[0];
						}
						if (starts_with($times[1], 'day')) {
							$data['travel_time'] += $times[0] * 24 * 60;
						}
					}

					if (sizeof($times) == 4) {
						if (starts_with($times[1], 'hour')) {
							$data['travel_time'] += $times[0] * 60;
						}

						if (starts_with($times[1], 'day')) {
							$data['travel_time'] += $times[0] * 24 * 60;
						}

						if (starts_with($times[3], 'hour')) {
							$data['travel_time'] += $times[2] * 60;
						}

						if (starts_with($times[3], 'min')) {
							$data['travel_time'] += $times[2];
						}
					}

					if (sizeof($times) == 6) {
						if (starts_with($times[1], 'day')) {
							$data['travel_time'] += $times[0] * 24 * 60;
						}

						if (starts_with($times[3], 'hour')) {
							$data['travel_time'] += $times[2] * 60;
						}

						if (starts_with($times[5], 'min')) {
							$data['travel_time'] += $times[4];
						}
					}
				}

			}
			return view('customers.home', $data);
		} else {
			if (isset($_GET['year'])) {
				$pass_year = $_GET['year'];
			} else {
				$pass_year = date("Y");
			}
			$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
			$y = array();
			foreach ($years as $year) {
				$y[$year->years] = $year->years;
			}

			if ($years == null) {

				$y[date('Y')] = date('Y');

			}
			$index['year_select'] = $pass_year;
			$index['years'] = $y;
			$index['drivers'] = User::whereUser_type("D")->get()->count();
			$index['reviews'] = ReviewModel::all()->count();
			$index['customers'] = User::whereUser_type("C")->get()->count();
			$index['users'] = User::whereUser_type("O")->get()->count();
			$vehicle_ids = array(0);
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$index['vehicles'] = VehicleModel::all()->count();
				$index['bookings'] = Bookings::all()->count();
				$vehicle_ids = VehicleModel::pluck('id')->toArray();
				if ($vehicle_ids == null) {
					$vehicle_ids = array(0);
				}
			} else {
				$index['vehicles'] = VehicleModel::where('group_id', Auth::user()->group_id)->count();
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
				if ($vehicle_ids == null) {
					$vehicle_ids = array(0);
				}
				$index['bookings'] = Bookings::whereIn('vehicle_id', $vehicle_ids)->count();
			}
			$index['vendors'] = Vendor::all()->count();
			// $index['parts'] = PartsModel::all()->count();
			$index['customers'] = User::whereUser_type("C")->get()->count();

			$index['yearly_income'] = $this->yearly_income($pass_year);
			$index['yearly_expense'] = $this->yearly_expense($pass_year);

			$vv = array();
			if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$all_vehicles = VehicleModel::get();
			} else {
				$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
			}

			foreach ($all_vehicles as $key) {
				$vv[$key->id] = $key->make_name . "-" . $key->model_name . "-" . $key->license_plate;
			}
			$index['vehicle_name'] = $vv;

			$index['expenses'] = Expense::select('vehicle_id', DB::raw('sum(amount) as expense'))->whereIn('vehicle_id', $vehicle_ids)->whereYear('date', date('Y'))->whereMonth('date', date('n'))->groupBy('vehicle_id')->get();
			$index['income'] = IncomeModel::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");
			// dd($vehicle_ids);
			$index['expense'] = Expense::whereRaw('year(date) = ? and month(date)=?', [date("Y"), date("n")])->whereIn('vehicle_id', $vehicle_ids)->sum("amount");

			$exp = DB::select('select date,sum(amount) as tot from expense where deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date');
			$inc = DB::select('select date,sum(amount) as tot from income where deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by date');
			$date1 = IncomeModel::pluck('date')->toArray();
			$date2 = Expense::pluck('date')->toArray();
			$all_dates = array_unique(array_merge($date1, $date2));

			$dates = array_count_values($all_dates);
			ksort($dates);
			$dates = array_slice($dates, -12, 12);
			$index['dates'] = $dates;
			$temp = array();
			foreach ($all_dates as $key) {
				$temp[$key] = 0;
			}
			$income2 = array();
			foreach ($inc as $income) {
				$income2[$income->date] = $income->tot;
			}
			$inc_data = array_merge($temp, $income2);
			ksort($inc_data);
			$index['incomes'] = implode(",", array_slice($inc_data, -12, 12));

			$expense2 = array();
			foreach ($exp as $e) {
				$expense2[$e->date] = $e->tot;
			}
			$expenses = array_merge($temp, $expense2);
			ksort($expenses);
			$index['expenses1'] = implode(",", array_slice($expenses, -12, 12));

			return view('home', $index);
		}
	}

	private function yearly_income($year) {

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$all_vehicles = VehicleModel::get();
		} else {
			$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
		}
		$vehicle_ids = array(0);
		foreach ($all_vehicles as $key) {
			$vehicle_ids[] = $key->id;
		}
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from income where year(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by month(date)', [$year]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		return implode(",", $yr);
	}
	private function yearly_expense($year) {
		if (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
			$all_vehicles = VehicleModel::get();
		} else {
			$all_vehicles = VehicleModel::where('group_id', Auth::user()->group_id)->get();
		}
		$vehicle_ids = array(0);
		foreach ($all_vehicles as $key) {
			$vehicle_ids[] = $key->id;
		}
		$incomes = DB::select('select monthname(date) as mnth,sum(amount) as tot from expense where year(date)=? and  deleted_at is null and vehicle_id in (' . join(",", $vehicle_ids) . ') group by month(date)', [$year]);
		$months = ["January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0, "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0];
		$income2 = array();

		foreach ($incomes as $income) {

			$income2[$income->mnth] = $income->tot;

		}
		$yr = array_merge($months, $income2);
		return implode(",", $yr);

	}

	public function test() {
		$start = '2019-09-05';
		$end = '2019-09-30';
		$exp = DB::select('select date,sum(amount) as tot from expense where  deleted_at is null and date between "' . $start . '" and "' . $end . '" group by date');
		$inc = DB::select('select date,sum(amount) as tot from income where  deleted_at is null and date between "' . $start . '" and "' . $end . '" group by date');
		$date1 = IncomeModel::whereBetween('date', [$start, $end])->pluck('date')->toArray();
		$date2 = Expense::whereBetween('date', [$start, $end])->pluck('date')->toArray();

		$all_dates = array_unique(array_merge($date1, $date2));
		$dates = array_count_values($all_dates);
		ksort($dates);
		$dates = array_slice($dates, -12, 12);
		$index['dates'] = $dates;
		$temp = array();
		foreach ($all_dates as $key) {
			$temp[$key] = 0;
		}
		$income2 = array();
		foreach ($inc as $income) {
			$income2[$income->date] = $income->tot;
		}
		$inc_data = array_merge($temp, $income2);
		ksort($inc_data);
		$index['incomes'] = implode(",", array_slice($inc_data, -12, 12));
		$expense2 = array();
		foreach ($exp as $e) {
			$expense2[$e->date] = $e->tot;
		}
		$expenses = array_merge($temp, $expense2);
		ksort($expenses);
		$index['expenses1'] = implode(",", array_slice($expenses, -12, 12));

		dd($expenses, $inc_data, $dates);
		return view('chart', $index);

	}

	public function logout() {
		Auth::logout();
		return redirect('admin/');
	}
}
