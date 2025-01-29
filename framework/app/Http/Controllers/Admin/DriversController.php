<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\ImportRequest;
use App\Imports\DriverImport;
use App\Model\Bookings;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\ExpCats;
use App\Model\Expense;
use App\Model\Hyvikk;
use App\Model\IncCats;
use App\Model\IncomeModel;
use App\Model\ServiceItemsModel;
use App\Model\User;
use App\Model\Driver;
use App\Model\Vendor;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Maatwebsite\Excel\Facades\Excel;
use Redirect;

class DriversController extends Controller {
	public function __construct() {
		// $this->middleware(['role:Admin']);
		$this->middleware('permission:Drivers add', ['only' => ['create']]);
		$this->middleware('permission:Drivers edit', ['only' => ['edit']]);
		$this->middleware('permission:Drivers delete', ['only' => ['bulk_delete', 'destroy']]);
		$this->middleware('permission:Drivers list');
		$this->middleware('permission:Drivers import', ['only' => ['importDrivers']]);
		$this->phone_code = array(
		  //  '+93' => '+93',
// 			'+358' => '+358',
// 			'+355' => '+355',
// 			'+213' => '+213',
// 			'+1 684' => '+1 684',
// 			'+376' => '+376',
// 			'+244' => '+244',
// 			'+1 264' => '+1 264',
// 			'+672' => '+672',
// 			'+1268' => '+1268',
// 			'+54' => '+54',
// 			'+374' => '+374',
// 			'+297' => '+297',
// 			'+61' => '+61',
// 			'+43' => '+43',
// 			'+994' => '+994',
// 			'+1 242' => '+1 242',
// 			'+973' => '+973',
// 			'+880' => '+880',
// 			'+1 246' => '+1 246',
// 			'+375' => '+375',
// 			'+32' => '+32',
// 			'+501' => '+501',
// 			'+229' => '+229',
// 			'+1 441' => '+1 441',
// 			'+975' => '+975',
// 			'+591' => '+591',
// 			'+387' => '+387',
// 			'+267' => '+267',
// 			'+55' => '+55',
// 			'+246' => '+246',
// 			'+673' => '+673',
// 			'+359' => '+359',
// 			'+226' => '+226',
// 			'+257' => '+257',
// 			'+855' => '+855',
// 			'+237' => '+237',
// 			'+1' => '+1',
// 			'+238' => '+238',
// 			'+ 345' => '+ 345',
// 			'+236' => '+236',
// 			'+235' => '+235',
// 			'+56' => '+56',
// 			'+86' => '+86',
// 			'+61' => '+61',
// 			'+61' => '+61',
// 			'+57' => '+57',
// 			'+269' => '+269',
// 			'+242' => '+242',
// 			'+243' => '+243',
// 			'+682' => '+682',
// 			'+506' => '+506',
// 			'+225' => '+225',
// 			'+385' => '+385',
// 			'+53' => '+53',
// 			'+357' => '+357',
// 			'+420' => '+420',
// 			'+45' => '+45',
// 			'+253' => '+253',
// 			'+1 767' => '+1 767',
// 			'+1 849' => '+1 849',
// 			'+593' => '+593',
// 			'+20' => '+20',
// 			'+503' => '+503',
// 			'+240' => '+240',
// 			'+291' => '+291',
// 			'+372' => '+372',
// 			'+251' => '+251',
// 			'+500' => '+500',
// 			'+298' => '+298',
// 			'+679' => '+679',
// 			'+358' => '+358',
// 			'+33' => '+33',
// 			'+594' => '+594',
// 			'+689' => '+689',
// 			'+241' => '+241',
// 			'+220' => '+220',
// 			'+995' => '+995',
// 			'+49' => '+49',
// 			'+233' => '+233',
// 			'+350' => '+350',
// 			'+30' => '+30',
// 			'+299' => '+299',
// 			'+1 473' => '+1 473',
// 			'+590' => '+590',
// 			'+1 671' => '+1 671',
// 			'+502' => '+502',
// 			'+44' => '+44',
// 			'+224' => '+224',
// 			'+245' => '+245',
// 			'+595' => '+595',
// 			'+509' => '+509',
// 			'+379' => '+379',
// 			'+504' => '+504',
// 			'+852' => '+852',
// 			'+36' => '+36',
// 			'+354' => '+354',
			'+91' => '+91',
// 			'+62' => '+62',
// 			'+98' => '+98',
// 			'+964' => '+964',
// 			'+353' => '+353',
// 			'+44' => '+44',
// 			'+972' => '+972',
// 			'+39' => '+39',
// 			'+1 876' => '+1 876',
// 			'+81' => '+81',
// 			'+44' => '+44',
// 			'+962' => '+962',
// 			'+7 7' => '+7 7',
// 			'+254' => '+254',
// 			'+686' => '+686',
// 			'+850' => '+850',
// 			'+82' => '+82',
// 			'+965' => '+965',
// 			'+996' => '+996',
// 			'+856' => '+856',
// 			'+371' => '+371',
// 			'+961' => '+961',
// 			'+266' => '+266',
// 			'+231' => '+231',
// 			'+218' => '+218',
// 			'+423' => '+423',
// 			'+370' => '+370',
// 			'+352' => '+352',
// 			'+853' => '+853',
// 			'+389' => '+389',
// 			'+261' => '+261',
// 			'+265' => '+265',
// 			'+60' => '+60',
// 			'+960' => '+960',
// 			'+223' => '+223',
// 			'+356' => '+356',
// 			'+692' => '+692',
// 			'+596' => '+596',
// 			'+222' => '+222',
// 			'+230' => '+230',
// 			'+262' => '+262',
// 			'+52' => '+52',
// 			'+691' => '+691',
// 			'+373' => '+373',
// 			'+377' => '+377',
// 			'+976' => '+976',
// 			'+382' => '+382',
// 			'+1664' => '+1664',
// 			'+212' => '+212',
// 			'+258' => '+258',
// 			'+95' => '+95',
// 			'+264' => '+264',
// 			'+674' => '+674',
// 			'+977' => '+977',
// 			'+31' => '+31',
// 			'+599' => '+599',
// 			'+687' => '+687',
// 			'+64' => '+64',
// 			'+505' => '+505',
// 			'+227' => '+227',
// 			'+234' => '+234',
// 			'+683' => '+683',
// 			'+672' => '+672',
// 			'+1 670' => '+1 670',
// 			'+47' => '+47',
// 			'+968' => '+968',
// 			'+92' => '+92',
// 			'+680' => '+680',
// 			'+970' => '+970',
// 			'+507' => '+507',
// 			'+675' => '+675',
// 			'+595' => '+595',
// 			'+51' => '+51',
// 			'+63' => '+63',
// 			'+872' => '+872',
// 			'+48' => '+48',
// 			'+351' => '+351',
// 			'+1 939' => '+1 939',
// 			'+974' => '+974',
// 			'+40' => '+40',
// 			'+7' => '+7',
// 			'+250' => '+250',
// 			'+262' => '+262',
// 			'+590' => '+590',
// 			'+290' => '+290',
// 			'+1 869' => '+1 869',
// 			'+1 758' => '+1 758',
// 			'+590' => '+590',
// 			'+508' => '+508',
// 			'+1 784' => '+1 784',
// 			'+685' => '+685',
// 			'+378' => '+378',
// 			'+239' => '+239',
// 			'+966' => '+966',
// 			'+221' => '+221',
// 			'+381' => '+381',
// 			'+248' => '+248',
// 			'+232' => '+232',
// 			'+65' => '+65',
// 			'+421' => '+421',
// 			'+386' => '+386',
// 			'+677' => '+677',
// 			'+252' => '+252',
// 			'+27' => '+27',
// 			'+500' => '+500',
// 			'+34' => '+34',
// 			'+94' => '+94',
// 			'+249' => '+249',
// 			'+597' => '+597',
// 			'+47' => '+47',
// 			'+268' => '+268',
// 			'+46' => '+46',
// 			'+41' => '+41',
// 			'+963' => '+963',
// 			'+886' => '+886',
// 			'+992' => '+992',
// 			'+255' => '+255',
// 			'+66' => '+66',
// 			'+670' => '+670',
// 			'+228' => '+228',
// 			'+690' => '+690',
// 			'+676' => '+676',
// 			'+1 868' => '+1 868',
// 			'+216' => '+216',
// 			'+90' => '+90',
// 			'+993' => '+993',
// 			'+1 649' => '+1 649',
// 			'+688' => '+688',
// 			'+256' => '+256',
// 			'+380' => '+380',
// 			'+971' => '+971',
// 			'+44' => '+44',
// 			'+1' => '+1',
// 			'+598' => '+598',
// 			'+998' => '+998',
// 			'+678' => '+678',
// 			'+58' => '+58',
// 			'+84' => '+84',
// 			'+1 284' => '+1 284',
// 			'+1 340' => '+1 340',
// 			'+681' => '+681',
// 			'+967' => '+967',
// 			'+260' => '+260',
// 			'+263' => '+263',
// 			'+1 809' => '+1 809',
// 			'+1 829' => '+1 829',

		);
	}

	public function importDrivers(ImportRequest $request) {
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
		//         $user->givePermissionTo(['Notes add','Notes edit','Notes delete','Notes list','Drivers list']);
		//         $user->save();
		//     }
		//}

		return back();
	}

	public function index() {

		return view("drivers.index");
	}

	public function fetch_data(Request $request) {
		if ($request->ajax()) {

			$users = User::select('users.*')
				->leftJoin('users_meta', 'users_meta.user_id', '=', 'users.id')
				->leftJoin('driver_vehicle', 'driver_vehicle.driver_id', '=', 'users.id')
				->leftJoin('vehicles', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')

				->with(['metas'])->whereUser_type("D")->groupBy('users.id');
            	// Fetch all admins
			$admins = User::with(['metas'])
			->where(function ($query) {
				$query->where('user_type', 'O');
			})
			->whereHas('metas', function ($query) {
				$query->where('key', 'client')->where('value', 0);
			})
			->select('id', 'name')->get();
			return DataTables::eloquent($users)
				->addColumn('check', function ($user) {
					return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
				})
				->editColumn('name', function ($user) {
					return "<a href=" . route('drivers.show', $user->id) . ">$user->name</a>";
				})
				->addColumn('driver_image', function ($user) {
					$src = ($user->driver_image != null)?asset('uploads/' . $user->driver_image): asset('assets/images/no-user.jpg');

					return '<img src="' . $src . '" height="70px" width="70px">';
				})
			// ->addColumn('vehicle', function ($user) {
			//     // return ($user->vehicle_id != null) ? $user->driver_vehicle->vehicle->make_name . '-' . $user->driver_vehicle->vehicle->model_name . '-' . $user->driver_vehicle->vehicle->license_plate : '';

			//     # below code is using many-to-many relations
			//     // dd($user);
			//     $vehicles = [];
			//     foreach ($user->vehicles as $vehicle) {
			//         $vehicles[] = $vehicle->make_name . '-' . $vehicle->model_name . '-' . $vehicle->license_plate;
			//     }

			//     return implode(', ', $vehicles);

			// })
			// ->filterColumn('vehicle', function ($query, $keyword) {
			//     $query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"]);
			//     return $query;
			// })
				->addColumn('is_active', function ($user) {
					return ($user->is_active == 1) ? "YES" : "NO";
				})
				->addColumn('phone', function ($user) {
					return $user->phone_code . ' ' . $user->phone;
				})

				->addColumn('start_date', function ($user) {
					return $user->start_date;
				})
    			->addColumn('assign_admin', function ($user) use ($admins) {
                     $dropdown = '<select class="form-control assign-admin" data-driver-id="' . $user->id . '">';
                        $dropdown .= '<option value="">Select Admin</option>';
                        foreach ($admins as $admin) {
                            $dropdown .= '<option value="' . $admin->id . '">' . $admin->name . '</option>';
                        }
                        $dropdown .= '</select>';
                        return $dropdown;
                })
                ->addColumn('assigned_admin',function($user){
                  return $user->assigned_admin ? User::find($user->assigned_admin)->name : 'No Admin Assigned';  
                })
				->addColumn('action', function ($user) {
					return view('drivers.list-actions', ['row' => $user]);
				})
				->filterColumn('is_active', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'is_active');
						$q->whereRaw("IF(value = 1 , 'YES', 'NO') like ? ", ["%{$keyword}%"]);
					});
					return $query;
				})
				->filterColumn('phone', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where(function ($q) use ($keyword) {
							$q->where('key', 'phone');
							$q->where("value", 'like', "%$keyword%");
						})->orWhere(function ($q) use ($keyword) {
							$q->where('key', 'phone_code');
							$q->where("value", 'like', "%$keyword%");
						});
					});
					return $query;
				})
				->filterColumn('start_date', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'start_date');
						$q->where("value", 'like', "%$keyword%");
					});
					return $query;
				})
				->rawColumns(['driver_image', 'action', 'check', 'name','assign_admin'])
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}
	
public function assignAdmin(Request $request)
{
    Log::info('Assign admin request:', $request->all());

    $request->validate([
        'driver_id' => 'required|exists:users,id',
        'admin_id' => 'required|exists:users,id',
		
    ]);

    try {
        $driver = User::findOrFail($request->driver_id);
        $driver->assigned_admin = $request->admin_id;
        $driver->save();

        Log::info("Admin assigned successfully: Driver ID: {$driver->id}, Admin ID: {$driver->assigned_admin}");
        return response()->json(['success' => true, 'message' => 'Admin assigned successfully.']);
    } catch (\Exception $e) {
        Log::error('Error assigning admin: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to assign admin.'], 500);
    }
}

    	public function fetch_admin_data(Request $request) {
		if ($request->ajax()) {

			$users = User::select('users.*')
				->leftJoin('users_meta', 'users_meta.user_id', '=', 'users.id')
				->leftJoin('driver_vehicle', 'driver_vehicle.driver_id', '=', 'users.id')
				->leftJoin('vehicles', 'driver_vehicle.vehicle_id', '=', 'vehicles.id')
                ->where('users.assigned_admin', Auth::user()->id)
				->with(['metas'])->whereUser_type("D")->groupBy('users.id');

			return DataTables::eloquent($users)
				->addColumn('check', function ($user) {
					return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
				})
				->editColumn('name', function ($user) {
					return "<a href=" . route('drivers.show', $user->id) . ">$user->name</a>";
				})
				->addColumn('driver_image', function ($user) {
					$src = ($user->driver_image != null)?asset('uploads/' . $user->driver_image): asset('assets/images/no-user.jpg');

					return '<img src="' . $src . '" height="70px" width="70px">';
				})
			// ->addColumn('vehicle', function ($user) {
			//     // return ($user->vehicle_id != null) ? $user->driver_vehicle->vehicle->make_name . '-' . $user->driver_vehicle->vehicle->model_name . '-' . $user->driver_vehicle->vehicle->license_plate : '';

			//     # below code is using many-to-many relations
			//     // dd($user);
			//     $vehicles = [];
			//     foreach ($user->vehicles as $vehicle) {
			//         $vehicles[] = $vehicle->make_name . '-' . $vehicle->model_name . '-' . $vehicle->license_plate;
			//     }

			//     return implode(', ', $vehicles);

			// })
			// ->filterColumn('vehicle', function ($query, $keyword) {
			//     $query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"]);
			//     return $query;
			// })
				->addColumn('is_active', function ($user) {
					return ($user->is_active == 1) ? "YES" : "NO";
				})
				->addColumn('phone', function ($user) {
					return $user->phone_code . ' ' . $user->phone;
				})

				->addColumn('start_date', function ($user) {
					return $user->start_date;
				})
				->addColumn('action', function ($user) {
					return view('drivers.list-actions', ['row' => $user]);
				})
				->filterColumn('is_active', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'is_active');
						$q->whereRaw("IF(value = 1 , 'YES', 'NO') like ? ", ["%{$keyword}%"]);
					});
					return $query;
				})
				->filterColumn('phone', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where(function ($q) use ($keyword) {
							$q->where('key', 'phone');
							$q->where("value", 'like', "%$keyword%");
						})->orWhere(function ($q) use ($keyword) {
							$q->where('key', 'phone_code');
							$q->where("value", 'like', "%$keyword%");
						});
					});
					return $query;
				})
				->filterColumn('start_date', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'start_date');
						$q->where("value", 'like', "%$keyword%");
					});
					return $query;
				})
				->rawColumns(['driver_image', 'action', 'check', 'name'])
				->make(true);
			//return datatables(User::all())->toJson();

		}
	}
	
	
	public function show($id) {
		$index['driver'] = User::find($id);
		// $index['driver']->load('metas');
		// $index['bookings'] = Bookings::where('driver_id', $id)->latest()->get();
		// $index['bookings']->load(['metas','vehicle.metas','driver','vehicle.maker','vehicle.vehiclemodel','vehicle.types','vehicle.vehiclecolor','customer']);
		// // dd($index);
		return view('drivers.show', $index);
	}

	public function fetch_bookings_data(Request $request) {
		if ($request->ajax()) {
			$date_format_setting = (Hyvikk::get('date_format'))?Hyvikk::get('date_format'): 'd-m-Y';
			if (Auth::user()->user_type == "C") {
				$bookings = Bookings::where('customer_id', Auth::id())->latest();
			} elseif (Auth::user()->group_id == null || Auth::user()->user_type == "S") {
				$bookings = Bookings::latest();
			} else {
				$vehicle_ids = VehicleModel::where('group_id', Auth::user()->group_id)->pluck('id')->toArray();
				$bookings = Bookings::whereIn('vehicle_id', $vehicle_ids)->latest();
			}
			$bookings->select('bookings.*')
				->leftJoin('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')

				->leftJoin('bookings_meta', function ($join) {
					$join->on('bookings_meta.booking_id', '=', 'bookings.id')
						->where('bookings_meta.key', '=', 'vehicle_typeid');
				})
				->leftJoin('vehicle_types', 'bookings_meta.value', '=', 'vehicle_types.id')
				->when($request->driver_id, function ($q, $driver_id) {
					$q->where('bookings.driver_id', $driver_id);
				})
				->when($request->customer_id, function ($q, $customer_id) {
					$q->where('bookings.customer_id', $customer_id);
				})
				->with(['customer', 'metas']);

			return DataTables::eloquent($bookings)
				->addColumn('check', function ($user) {
					return '<input type="checkbox" name="ids[]" value="' . $user->id . '" class="checkbox" id="chk' . $user->id . '" onclick=\'checkcheckbox();\'>';
				})
				->addColumn('customer', function ($row) {
					return ($row->customer->name) ?? "";
				})
				->addColumn('ride_status', function ($row) {
					return ($row->getMeta('ride_status')) ?? "";
				})
				->editColumn('pickup_addr', function ($row) {
					return str_replace(",", "<br/>", $row->pickup_addr);
				})
				->editColumn('dest_addr', function ($row) {
					// dd($row->dest_addr);
					return str_replace(",", "<br/>", $row->dest_addr);
				})
				->editColumn('pickup', function ($row) use ($date_format_setting) {
					$pickup = '';
					$pickup = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->pickup)) {
						$pickup = date($date_format_setting . ' h:i A', strtotime($row->pickup));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->pickup)),
							'timestamp' => Carbon::parse($row->pickup),
						];
					}
					return $pickup;
				})
				->editColumn('dropoff', function ($row) use ($date_format_setting) {
					$dropoff = [
						'display' => '',
						'timestamp' => '',
					];
					if (!is_null($row->dropoff)) {
						$dropoff = date($date_format_setting . ' h:i A', strtotime($row->dropoff));
						return [
							'display' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
							'timestamp' => Carbon::parse($row->dropoff),
						];
					}
					return $dropoff;
				})

				->editColumn('payment', function ($row) {
					if ($row->payment == 1) {
						return '<span class="text-success"> ' . __('fleet.paid1') . ' </span>';
					} else {
						return '<span class="text-warning"> ' . __('fleet.pending') . ' </span>';
					}
				})
				->editColumn('tax_total', function ($row) {
					return ($row->tax_total)?Hyvikk::get('currency') . " " . $row->tax_total: "";
				})
				->addColumn('vehicle', function ($row) {
					$vehicle_type = VehicleTypeModel::find($row->getMeta('vehicle_typeid'));
					return !empty($row->vehicle_id) ? $row->vehicle->make_name . '-' . $row->vehicle->model_name . '-' . $row->vehicle->license_plate : ($vehicle_type->displayname) ?? "";
				})
				->filterColumn('vehicle', function ($query, $keyword) {
					$query->whereRaw("CONCAT(vehicles.make_name , '-' , vehicles.model_name , '-' , vehicles.license_plate) like ?", ["%$keyword%"])
						->orWhereRaw("(vehicle_types.displayname like ? and bookings.vehicle_id IS NULL)", ["%$keyword%"]);
					return $query;
				})
				->filterColumn('ride_status', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'ride_status');
						$q->whereRaw("value like ?", ["%{$keyword}%"]);
					});
					return $query;
				})
				->filterColumn('tax_total', function ($query, $keyword) {
					$query->whereHas("metas", function ($q) use ($keyword) {
						$q->where('key', 'tax_total');
						$q->whereRaw("value like ?", ["%{$keyword}%"]);
					});
					return $query;
				})
				->addColumn('action', function ($user) {
					return view('bookings.list-actions', ['row' => $user]);
				})
				->filterColumn('payment', function ($query, $keyword) {
					$query->whereRaw("IF(payment = 1 , '" . __('fleet.paid1') . "', '" . __('fleet.pending') . "') like ? ", ["%{$keyword}%"]);

				})
				->filterColumn('pickup', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(pickup,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
				})
				->filterColumn('dropoff', function ($query, $keyword) {
					$query->whereRaw("DATE_FORMAT(dropoff,'%d-%m-%Y %h:%i %p') LIKE ?", ["%$keyword%"]);
				})
				->rawColumns(['payment', 'action', 'check', 'pickup_addr', 'dest_addr'])
				->make(true);
			//return datatables(User::all())->toJson();
		}
	}

	public function destroy(Request $request) {
		$driver = User::find($request->id);
		if ($driver->vehicles->count()) {

			$driver->vehicles()->detach($driver->vehicles->pluck('id')->toArray());
		}

		DriverVehicleModel::where('driver_id', $request->id)->delete();

		if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
			unlink('./uploads/' . $driver->driver_image);
		}
		if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
			unlink('./uploads/' . $driver->license_image);
		}
		if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
			unlink('./uploads/' . $driver->documents);
		}

		User::find($request->get('id'))->user_data()->delete();
		//User::find($request->get('id'))->delete();
		$driver->update([
			'email' => time() . "_deleted" . $driver->email,
		]);

		$driver->delete();

		return redirect()->route('drivers.index');
	}

	public function bulk_delete(Request $request) {
		$drivers = User::whereIn('id', $request->ids)->get();
		foreach ($drivers as $driver) {
			if ($driver->vehicles->count()) {
				$driver->vehicles()->detach($driver->vehicles->pluck('id')->toArray());
			}
			$driver->update([
				'email' => time() . "_deleted" . $driver->email,
			]);
			if (file_exists('./uploads/' . $driver->driver_image) && !is_dir('./uploads/' . $driver->driver_image)) {
				unlink('./uploads/' . $driver->driver_image);
			}

			if (file_exists('./uploads/' . $driver->license_image) && !is_dir('./uploads/' . $driver->license_image)) {
				unlink('./uploads/' . $driver->license_image);
			}

			if (file_exists('./uploads/' . $driver->documents) && !is_dir('./uploads/' . $driver->documents)) {
				unlink('./uploads/' . $driver->documents);
			}
			$driver->delete();
		}

		DriverVehicleModel::whereIn('driver_id', $request->ids)->delete();
		//User::whereIn('id', $request->ids)->delete();
		// return redirect('admin/customers');
		return back();
	}

		public function create() {
			// Retrieve all vehicles
			$data['vehicles'] = VehicleModel::get();
		
			// Retrieve all vendors
			$data['vendors'] = Vendor::all();
		
			// Pass phone codes
			$data['phone_code'] = $this->phone_code;
		
			// Return the view with vehicles and vendors data
			return view("drivers.create", $data);
		}
		

	public function edit(User $driver) {
		if ($driver->user_type != "D") {
			return redirect("admin/drivers");
		}
		$driver->load('vehicles');

		if (Auth::user()->group_id == null || Auth::user()->user_type == "S" || Auth::user()->user_type == "M") {
			$vehicles = VehicleModel::get();
		} else {
			$vehicles = VehicleModel::where('group_id', Auth::user()->group_id)
				->get();
		}
		$phone_code = $this->phone_code;
		return view("drivers.edit", compact("driver", "phone_code", 'vehicles'));
	}



	//old upload_file function


	// private function upload_file($file, $field, $id) {
	// 	$destinationPath = './uploads'; // upload path
	// 	$extension = $file->getClientOriginalExtension();

	// 	$fileName1 = Str::uuid() . '.' . $extension;

	// 	$file->move($destinationPath, $fileName1);
	// 	$user = User::find($id);
	// 	$user->setMeta([$field => $fileName1]);
	// 	$user->save();

	// }

	public function update(DriverRequest $request) {
		$id = $request->get('id');
		$user = User::find($id);

		if ($user->vehicle_id != $request->vehicle_id) {
			// $old_vehicle = VehicleModel::find($user->vehicle_id);
			// if ($old_vehicle) {
			//     $old_vehicle->driver_id = null;
			//     $old_vehicle->save();
			// }

			// $vehicle = VehicleModel::find($request->get('vehicle_id'));
			// $vehicle->driver_id = $user->id;
			// $vehicle->save();
			// DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $request->get('vehicle_id'), 'date' => date('Y-m-d H:i:s')]);
			// DriverVehicleModel::updateOrCreate(['driver_id' => $user->id], ['vehicle_id' => $request->get('vehicle_id'), 'driver_id' => $user->id]);
		}

		# many-to-many driver vehicle relation.
		// $user->vehicles()->sync($request->vehicle_id);

		// foreach ($request->vehicle_id as $v_id) {
		//     DriverLogsModel::create(['driver_id' => $user->id, 'vehicle_id' => $v_id, 'date' => date('Y-m-d H:i:s')]);
		// }

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

		$user->name = $request->get("first_name") . " " . $request->get("last_name");
		$name = explode(' ', $request->name);

		$user->first_name = $name[0] ?? '';
		$user->middle_name = $name[1] ?? '';
		$user->last_name = $name[2] ?? '';
		$user->email = $request->get('email');
		$user->address = $request->get('address');
		$user->save();
		// $user->driver_image = $request->get('driver_image');
		$form_data = $request->all();
		unset($form_data['driver_image']);
		unset($form_data['documents']);
		unset($form_data['license_image']);

		$user->setMeta($form_data);

		$to = \Carbon\Carbon::now();

		$from = \Carbon\Carbon::createFromFormat('Y-m-d', $request->get('exp_date'));

		$diff_in_days = $to->diffInDays($from);

		if ($diff_in_days > 20) {
			$t = DB::table('notifications')
				->where('type', 'like', '%RenewDriverLicence%')
				->where('data', 'like', '%"vid":' . $user->id . '%')
				->delete();

		}

		$user->save();

		return redirect()->route("drivers.index");
	}


	private function upload_file($file, $field, $id)
{
    $destinationPath = public_path('uploads'); // Use the public directory for uploads
    $extension = $file->getClientOriginalExtension();
    $fileName = Str::uuid() . '.' . $extension;

    $file->move($destinationPath, $fileName);

    // Update the driver's file field
    $driver = Driver::find($id);
    $driver->update([$field => $fileName]);
}


	public function store(DriverRequest $request)
{
	// dd($request->all());
	// exit();	
    $validated = $request->validate([
        'first_name' => 'required|string',
        'city' => 'required|string',
        'DOB' => 'required|date',
        'phone_code' => 'required|string',
        'phone' => 'required|numeric',
        'emp_id' => 'required|string|unique:drivers,emp_id',
        'vendor_id' => 'required|numeric',
        'license_number' => 'required|string|unique:drivers,license_number',
        'license_number_date' => 'date',
        'induction_date' => 'date',
        'badge_number' => 'string',
        'badge_number_date' => 'date',
        'alternate_gov_id' => 'string',
        'alternate_gov_id_number' => 'string',
        'background_verification_status' => 'string',
        'background_verification_date' => 'date',
        'police_verification_status' => 'string',
        'police_verification_date' => 'date',
        'medical_verification_status' => 'string',
        'medical_verification_date' => 'date',
		'training_verification_status' => 'string',
        'training_date' => 'date',
        'eye_test_date' => 'date',
        'driver_License_image' => 'nullable|mimes:jpg,png,jpeg',
        'induction_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'alternate_gov_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'background_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'police_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'medical_verification_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'training_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'eye_test_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'documents_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
        'current_address_file' => 'nullable|mimes:jpg,png,jpeg,pdf,doc,docx',
    ]);

    $data = $request->except([
        'driver_License_image',
        'induction_file',
        'alternate_gov_file',
        'background_verification_file',
        'police_verification_file',
        'medical_verification_file',
        'training_file',
        'eye_test_file',
        'documents_file',
        'current_address_file',
    ]);

    // Create the driver record
    $driver = Driver::create($data);

    // Handle file uploads
    $fileFields = [
        'driver_License_image',
        'induction_file',
        'alternate_gov_file',
        'background_verification_file',
        'police_verification_file',
        'medical_verification_file',
        'training_file',
        'eye_test_file',
        'documents_file',
        'current_address_file',
    ];

    foreach ($fileFields as $field) {
        if ($request->hasFile($field) && $request->file($field)->isValid()) {
            $this->upload_file($request->file($field), $field, $driver->id);
        }
    }

    return redirect()->route('drivers.index')->with('success', 'Driver created successfully');
}


	public function enable($id) {
		// $driver = UserMeta::whereUser_id($id)->first();
		$driver = User::find($id);
		$driver->is_active = 1;
		$driver->save();
		return redirect()->route("drivers.index");
	}

	public function disable($id) {
		$bookings = Bookings::where('driver_id', $id)->where('status', 0)->get();
	
		if (count($bookings) > 0) {
			$newErrors = [
				'error' => 'Some active Bookings still have this driver, please either change the driver in those bookings or you can deactivate this driver after those bookings are complete!',
				'data' => $bookings->pluck('id')->toArray()
			];
			return redirect()->route('drivers.index')->with('errors', $newErrors)->with('bookings', $bookings);
		} else {
			$driver = User::find($id);
			$driver->is_active = 0;
			$driver->save();
			return redirect()->route('drivers.index');
		}
	}

	public function my_bookings() {
		$bookings = Bookings::orderBy('id', 'desc')->whereDriver_id(Auth::user()->id)->get();
		$data = [];
		foreach($bookings as $booking){
			if($booking->getMeta('ride_status') != 'Cancelled'){
				$data[] = $booking;
			}
		}
		// $data['data'] = Bookings::orderBy('id', 'desc')->whereDriver_id(Auth::user()->id)->get();
		return view('drivers.my_bookings', compact('data'));
	}
// public function my_bookings(Request $request)
// {
//     // if ($request->ajax()) {
//     //     $date_format_setting = Hyvikk::get('date_format') ?: 'd-m-Y';

//     //     $bookings = Bookings::where('driver_id', Auth::id())
//     //         ->where('ride_status', '!=', 'Cancelled')
//     //         ->latest()
//     //         ->select('bookings.*')
//     //         ->leftJoin('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
//     //         ->leftJoin('bookings_meta', function ($join) {
//     //             $join->on('bookings_meta.booking_id', '=', 'bookings.id')
//     //                 ->where('bookings_meta.key', '=', 'vehicle_typeid');
//     //         })
//     //         ->leftJoin('vehicle_types', 'bookings_meta.value', '=', 'vehicle_types.id')
//     //         ->with(['customer']);

//     //     return DataTables::eloquent($bookings)
//     //         ->addColumn('customer', function ($row) {
//     //             return $row->customer->name ?? '';
//     //         })
//     //         ->addColumn('vehicle', function ($row) {
//     //             return $row->vehicle->make_name . ' - ' . $row->vehicle->model_name . ' - ' . $row->vehicle->license_plate;
//     //         })
//     //         ->editColumn('pickup', function ($row) use ($date_format_setting) {
//     //             return date($date_format_setting . ' g:i A', strtotime($row->pickup));
//     //         })
//     //         ->editColumn('dropoff', function ($row) use ($date_format_setting) {
//     //             return date($date_format_setting . ' g:i A', strtotime($row->dropoff));
//     //         })
//     //         ->editColumn('pickup_addr', function ($row) {
//     //             return $row->pickup_addr;
//     //         })
//     //         ->editColumn('dropoff_addr', function ($row) {
//     //             return $row->dest_addr;
//     //         })
//     //         ->addColumn('passengers', function ($row) {
//     //             return $row->travellers;
//     //         })
//     //         ->make(true);
//     // }

//     return view('drivers.my_bookings');
// }



	public function yearly() {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
		$v_id = array('0');
		$c = array();
		foreach ($bookings as $key) {
			if ($key->vehicle_id != null) {
				$v_id[] = $key->vehicle_id;
			}

		}

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {

			$y[date('Y')] = date('Y');

		}
		$data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();

		$data['year_select'] = date("Y");

		$data['vehicle_select'] = null;
		$data['years'] = $y;
		$in = join(",", $v_id);
		$data['income'] = IncomeModel::select(DB::raw("sum(IFNULL(driver_amount,amount)) as income"))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->get();
		$data['expenses'] = Expense::select(DB::raw('sum(IFNULL(driver_amount,amount)) as expense'))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->get();
		$data['expense_by_cat'] = Expense::select('type', 'expense_type', DB::raw('sum(amount) as expense'))->whereYear('date', date('Y'))->whereIn('vehicle_id', $v_id)->groupBy(['expense_type', 'type'])->get();

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
		$data['yearly_income'] = $this->yearly_income();
		$data['yearly_expense'] = $this->yearly_expense();
		return view('drivers.yearly', $data);

	}

	public function yearly_post(Request $request) {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
		$v_id = array();
		foreach ($bookings as $key) {
			$v_id[] = $key->vehicle_id;
		}
		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		$b = array();
		$i = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}
		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
		$data['year_select'] = $request->get("year");
		$data['vehicle_select'] = $request->get("vehicle_id");
		$data['yearly_income'] = $this->yearly_income();
		$data['yearly_expense'] = $this->yearly_expense();

		$income1 = IncomeModel::select(DB::raw("sum(amount) as income"))->whereYear('date', $data['year_select']);
		$expense1 = Expense::select(DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select']);
		$expense2 = Expense::select('type', 'expense_type', DB::raw("sum(amount) as expense"))->whereYear('date', $data['year_select'])->groupBy(['expense_type', 'type']);
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->whereIn('vehicle_id', $v_id)->get();
			$data['expenses'] = $expense1->whereIn('vehicle_id', $v_id)->get();
			$data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $v_id)->get();
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
		return view('drivers.yearly', $data);
	}

	public function monthly() {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
		$v_id = array('0');
		foreach ($bookings as $key) {
			if ($key->vehicle_id != null) {
				$v_id[] = $key->vehicle_id;
			}
		}

		$years = DB::select("select distinct year(date) as years from income  union select distinct year(date) as years from expense order by years desc");
		$y = array();
		foreach ($years as $year) {
			$y[$year->years] = $year->years;
		}

		if ($years == null) {
			$y[date('Y')] = date('Y');
		}
		$data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();

		$data['year_select'] = date("Y");
		$data['month_select'] = date("n");
		$data['vehicle_select'] = null;
		$data['years'] = $y;
		$data['yearly_income'] = $this->yearly_income();
		$data['yearly_expense'] = $this->yearly_expense();
		$in = join(",", $v_id);

		$data['income'] = IncomeModel::select(DB::raw('sum(IFNULL(driver_amount,amount)) as income'))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->whereIn('vehicle_id', $v_id)->get();

		$data['expenses'] = Expense::select(DB::raw('sum(IFNULL(driver_amount,amount)) as expense'))->whereYear('date', date('Y'))->whereMonth('date', date('n'))->whereIn('vehicle_id', $v_id)->get();
		$data['expense_by_cat'] = DB::select("select type,expense_type,sum(amount) as expense from expense where deleted_at is null and year(date)=" . date("Y") . " and month(date)=" . date("n") . " and vehicle_id in(" . $in . ") group by expense_type,type");

		$ss = ServiceItemsModel::get();
		$c = array();
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
		return view("drivers.monthly", $data);
	}

	public function monthly_post(Request $request) {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
		$v_id = array('0');
		foreach ($bookings as $key) {
			if ($key->vehicle_id != null) {
				$v_id[] = $key->vehicle_id;
			}

		}
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
		$data['vehicles'] = VehicleModel::whereIn('id', $v_id)->get();
		$data['year_select'] = $request->get("year");
		$data['month_select'] = $request->get("month");
		$data['vehicle_select'] = $request->get("vehicle_id");
		$data['yearly_income'] = $this->yearly_income();
		$data['yearly_expense'] = $this->yearly_expense();

		$income1 = IncomeModel::select(DB::raw('sum(amount) as income'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
		$expense1 = Expense::select(DB::raw('sum(amount) as expense'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select']);
		$expense2 = Expense::select('type', 'expense_type', DB::raw('sum(amount) as expense'))->whereYear('date', $data['year_select'])->whereMonth('date', $data['month_select'])->groupBy(['expense_type', 'type']);
		if ($data['vehicle_select'] != "") {
			$data['income'] = $income1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expenses'] = $expense1->where('vehicle_id', $data['vehicle_select'])->get();
			$data['expense_by_cat'] = $expense2->where('vehicle_id', $data['vehicle_select'])->get();
		} else {
			$data['income'] = $income1->whereIn('vehicle_id', $v_id)->get();
			$data['expenses'] = $expense1->whereIn('vehicle_id', $v_id)->get();
			$data['expense_by_cat'] = $expense2->whereIn('vehicle_id', $v_id)->get();
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
		return view("drivers.monthly", $data);
	}

	private function yearly_income() {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
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
		return implode(",", $yr);
	}
	private function yearly_expense() {
		$bookings = DriverLogsModel::where('driver_id', Auth::user()->id)->get();
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
		return implode(",", $yr);

	}

	// driver records from firebase
	public function firebase() {
		$database = app('firebase.database');
		$details = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();
		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

		// $data = Firebase::get('/User_Locations/');

		// dd($data);
		//$details = json_decode($data, true);

		//dd($details);
		$markers = array();
		foreach ($details as $d) {
			// echo $d['user_name'] . "</br>";
			if ($d['user_type'] == "D") {

				$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude'], 'av' => $d['availability']],
				);
			}
		}
		// dd($markers);
	}

	public function driver_maps() {
		$database = app('firebase.database');
		$all_data = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();

		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

		//$all_data = json_decode($data, true);
		$drivers = array();
		if ($all_data != null) {
			foreach ($all_data as $d) {
				if (isset($d['latitude']) && isset($d['longitude'])) {
					if ($d['latitude'] != null || $d['longitude'] != null) {
						$drivers[] = array('user_name' => $d['user_name'], 'availability' => $d['availability'],
							'user_id' => $d['user_id'],
						);
					}
				}

			}
		}

		$index['details'] = $drivers;
		// dd($drivers);
		return view('driver_maps', $index);
	}

	public function markers() {
		// $data = Firebase::get('/User_Locations/');
		$database = app('firebase.database');
		$details = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();
		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

		//$details = json_decode($data, true);

		// dd($details);
		$markers = array();
		foreach ($details as $d) {
			if (isset($d['latitude']) && isset($d['longitude'])) {
				if ($d['latitude'] != null || $d['longitude'] != null) {
					if ($d['availability'] == "1") {
						$icon = "online.png";
						$status = "Online";
					} else {
						$icon = "offline.png";
						$status = "Offline";
					}

					$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
				}
			}

		}
		return json_encode($markers);

	}

	//temp
	public function markers_filter($id) {
		// $data = Firebase::get('/User_Locations/');

		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);
		$database = app('firebase.database');
		$details = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();

		//$details = json_decode($data, true);

		// dd($details);
		$markers = array();
		foreach ($details as $d) {
			if (isset($d['latitude']) && isset($d['longitude'])) {
				if ($d['latitude'] != null || $d['longitude'] != null) {
					if ($d['availability'] == "1") {
						$icon = "online.png";
						$status = "Online";
					} else {
						$icon = "offline.png";
						$status = "Offline";
					}
					if ($id == 1) {
						if ($d['availability'] == "1") {
							$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
						}

					}if ($id == 0) {
						if ($d['availability'] == "0") {
							$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
						}

					}if ($id == 2) {
						$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
					}

				}
			}
		}
		return json_encode($markers);

	}

	// marker with status selection in dropdown
	public function track_markers($id) {
		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

		$database = app('firebase.database');
		$details = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();

		//$details = json_decode($data, true);

		// dd($details);
		$markers = array();
		foreach ($details as $d) {
			if (isset($d['latitude']) && isset($d['longitude'])) {
				if ($d['latitude'] != null || $d['longitude'] != null) {
					if ($d['availability'] == "1") {
						$icon = "online.png";
						$status = "Online";
					} else {
						$icon = "offline.png";
						$status = "Offline";
					}
					if ($id == 1) {
						if ($d['availability'] == "1") {
							$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
						}

					}if ($id == 0) {
						if ($d['availability'] == "0") {
							$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
						}

					}if ($id == 2) {
						$markers[] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
					}
					// //appending $new in our array
					// array_unshift($arr, $new);
					// //now make it unique.
					// $final = array_unique($arr);

				}
			}
		}
		return json_encode($markers);
	}

	// view of single driver tracking
	public function track_driver($id) {
		$database = app('firebase.database');
		$data = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();
		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);

		$details = $index['details'] = $data;
		foreach ($details as $d) {
			if ($d['user_id'] == $id) {
				if ($d['availability'] == "1") {
					$icon = "online.png";
					$status = "Online";
				} else {
					$icon = "offline.png";
					$status = "Offline";
				}
				if (isset($d['latitude']) && isset($d['longitude'])) {
					$index['driver'] = array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status);
				}
			}
		}
		// dd($index['driver']);
		return view('track_driver', $index);
	}

	public function single_driver($id) {
		//$data = Firebase::get('/User_Locations/', ["orderBy" => '"user_type"', "equalTo" => '"D"']);
		$database = app('firebase.database');
		$data = $database
			->getReference('/User_Locations')
			->orderByChild('user_type')
			->equalTo('D')
			->getValue();

		// $details = json_decode($data, true);
		// dd($data);
		foreach ($data as $d) {

			if ($d['user_id'] == $id) {
				if (isset($d['latitude']) && isset($d['longitude'])) {
					if ($d['latitude'] != null || $d['longitude'] != null) {
						if ($d['availability'] == "1") {
							$icon = "online.png";
							$status = "Online";
						} else {
							$icon = "offline.png";
							$status = "Offline";
						}

						$driver = [array("id" => $d["user_id"], "name" => $d["user_name"], "position" => ["lat" => $d['latitude'], "long" => $d['longitude']], "icon" => $icon, 'status' => $status)];
					}
				}
			}
		}
		return json_encode($driver);
	}
}
