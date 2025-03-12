<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

*/

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Bookings;
use App\Model\Hyvikk;
use App\Model\MessageModel;
use App\Model\ReviewModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Rules\UniqueMobile;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as Login;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Validator;
use Twilio\Rest\Client;
use App\Model\Timeslot;

class UsersApi extends Controller {
	public function send_otp(Request $request) {
		$mobileNumber = $request->get('mobile_number');
	Log::info('Mobile Number: ' . $mobileNumber);
	if (empty($mobileNumber)) {
        return [
            'success' => 0,
            'message' => 'Mobile number is required.'
        ];
    }
  // Remove any non-numeric characters and the '+' sign if it exists
  $mobileNumber = preg_replace('/\D/', '', $mobileNumber); // Remove all non-numeric characters

  // Check if the number starts with '91' and remove it (country code)
  if (substr($mobileNumber, 0, 2) === '91') {
	  $mobileNumber = substr($mobileNumber, 2); // Remove the '91' prefix
  }
   

	
    // Check if the user with the provided mobile number exists and is of type 'C'
    $user = User::where('number', $mobileNumber)->first(); // Assuming 'mobno' stores the mobile number
// Log::info('User: ' . $user);
    if (!$user) {
        return [
            'success' => 0,
            'message' => 'User not found.'
        ];
    }

    if ($user->user_type !== 'C') {
        return [
            'success' => 0,
            'message' => 'Only users with type "C" are allowed to receive OTP.'
        ];
    }

		// Twilio credentials from .env or any custom method you're using
		$sid = hyvikk::twilio('sid'); // Account SID
		$token = hyvikk::twilio('token'); // Auth Token
		$serviceSid = hyvikk::twilio('serviceSid'); // Correct Service SID (for Twilio Verify Service)
	
		if (!$sid || !$token || !$serviceSid) {
			return [
				'success' => 0,
				'message' => 'Twilio credentials are missing.'
			];
		}
	
		try {

			 // Format the mobile number by adding +91 if it's not already present
			 if (strpos($mobileNumber, '+91') !== 0) {
				$mobileNumber = '+91' . $mobileNumber;
			}
	
			// Initialize Twilio client
			$twilio = new Client($sid, $token);
	
			// Send OTP verification request
			$verification = $twilio->verify->v2->services($serviceSid)
				->verifications
				->create($mobileNumber, "sms");
	
			return [
				'success' => 1,
				'message' => 'OTP sent successfully.',
			];
	
		} catch (\Exception $e) {
			Log::error('Twilio Error: ' . $e->getMessage());
			return [
				'success' => 0,
				'message' => 'Failed to send OTP. Please try again.',
				'error' => $e->getMessage(),
			];
		}
	}
	public function verify_otp(Request $request) {
		// Define validation rules
		$validationRules = [
			'mobile_number' => 'required|string', // Allow 10 to 15 digits
			'otp' => 'required|string|size:4', // Assume OTP is always 4 digits
			'fcm_id' => 'required|string|max:255', // FCM ID should be a string with a max length of 255
			'device_token' => 'required|string|max:255', // Device token should be a string with a max length of 255
		];
	
		// Validate the request
		$validator = Validator::make($request->all(), $validationRules);
	
		if ($validator->fails()) {
			Log::info('verify_otp - Validation failed: ' . json_encode($validator->errors()->all()));
			return response()->json([
				'success' => 0,
				'message' => 'Validation failed.',
				'errors' => $validator->errors()
			]);
		}
	
		// Extract mobile number and OTP from the request
		$mobileNumber = $request->get('mobile_number');
		$otp = $request->get('otp');
		$fcm_id = $request->get('fcm_id'); // Get FCM ID from the request
		$device_token = $request->get('device_token'); // Get device token from the request
		
		// Log::info('verify_otp - Received Mobile Number: ' . $mobileNumber);
		// Log::info('verify_otp - Received OTP: ' . $otp);
	
		// Validate input
		if (empty($mobileNumber) || empty($otp)) {
			// Log::info('verify_otp - Mobile number or OTP is empty.');
			return [
				'success' => 0,
				'message' => 'Mobile number and OTP are required.'
			];
		}
	
		// Clean the mobile number
		$mobileNumber = preg_replace('/\D/', '', $mobileNumber); 
		Log::info('verify_otp - Cleaned Mobile Number: ' . $mobileNumber);
	
		// Remove country code if present
		if (substr($mobileNumber, 0, 2) === '91') {
			$mobileNumber = substr($mobileNumber, 2); 
			Log::info('verify_otp - Mobile Number after removing country code: ' . $mobileNumber);
		}
	
		// Check if user exists
		$user = User::where('number', $mobileNumber)->first();
		Log::info('verify_otp - User found: ' . ($user ? 'Yes' : 'No'));
	
		if (!$user) {
			Log::info('verify_otp - User not found for mobile number: ' . $mobileNumber);
			return [
				'success' => 0,
				'message' => 'User not found.'
			];
		}
	
		if ($user->user_type !== 'C') {
			Log::info('verify_otp - User type is not "C". User type: ' . $user->user_type);
			return [
				'success' => 0,
				'message' => 'Only users with type "C" are allowed to verify OTP.'
			];
		}
	
		// Twilio credentials check
		$sid = hyvikk::twilio('sid');
		$token = hyvikk::twilio('token');
		$serviceSid = hyvikk::twilio('serviceSid');
	
		if (!$sid || !$token || !$serviceSid) {
			Log::info('verify_otp - Twilio credentials are missing.');
			return [
				'success' => 0,
				'message' => 'Twilio credentials are missing.'
			];
		}
	
		try {
			// Format mobile number to include +91 if missing
			if (strpos($mobileNumber, '+91') !== 0) {
				$mobileNumber = '+91' . $mobileNumber;
				Log::info('verify_otp - Mobile Number after adding country code: ' . $mobileNumber);
			}
	
			// Initialize Twilio client
			$twilio = new Client($sid, $token);
			Log::info('verify_otp - Twilio client initialized.');

			// Update user login status
			$user->login_status = 1;
			$user->device_token = $device_token;
            $user->save();

			 // Update FCM ID by calling the update_fcm function
			 $updateFcmResponse = $this->update_fcm(new Request([
                'user_id' => $user->id,
                'fcm_id' => $fcm_id
            ]));

			// Verify OTP
			$verificationCheck = $twilio->verify->v2->services($serviceSid)
				->verificationChecks
				->create([
					'to' => $mobileNumber,
					'code' => $otp
				]);
	
			Log::info('verify_otp - Verification status: ' . $verificationCheck->status);
	
			if ($verificationCheck->status === "approved") {
				Log::info('verify_otp - OTP verified successfully.');
			
				// Prepare the response data
				$data = [
					'success' => 1,
					'message' => 'OTP verified successfully.',
					'data' => [
						'userinfo' => [
							"user_id" => $user->id,
							"api_token" => $user->api_token,
							"fcm_id" => $user->getMeta('fcm_id'),
							"device_token" => $user->getMeta('device_token'),
							"socialmedia_uid" => $user->getMeta('socialmedia_uid'),
							"user_name" => $user->name,
							"user_type" => $user->user_type,
							"mobno" => $user->number,
							"emailid" => $user->email,
							"gender" => $user->getMeta('gender'),
							"profile_pic" => $user->getMeta('profile_pic'),
							"status" => $user->getMeta('login_status'),
							"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at))
						]
					]
				];
			
				// Ensure the response is being returned properly
				return response()->json($data);
			} else {
				Log::info('verify_otp - OTP verification failed.');
				return response()->json([
					'success' => 0,
					'message' => 'Invalid OTP. Please try again.'
				]);
			}
	
		} catch (\Exception $e) {
			Log::error('verify_otp - Twilio Error: ' . $e->getMessage());
			return [
				'success' => 0,
				'message' => 'Failed to verify OTP. Please try again.',
				'error' => $e->getMessage()
			];
		}
	}



public function getActiveSlots(Request $request)
{
    // Step 1: Validate the input
    Log::info('Starting getActiveSlots endpoint', ['request_data' => $request->all()]);
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id', // Ensure the user_id exists in the users table
    ]);

    // Step 2: Fetch the user and determine the company ID
    $userId = $validated['user_id'];
    Log::info('Validated user ID', ['user_id' => $userId]);

    $user = User::find($userId);
    if (!$user) {
        Log::error('User not found', ['user_id' => $userId]);
        return response()->json(['error' => 'User not found'], 404);
    }

    $companyId = $user->assigned_admin; // Replace `assigned_admin_id` with the actual field name
    Log::info('Fetched company ID for user', ['user_id' => $userId, 'company_id' => $companyId]);

    // Step 3: Fetch and filter the timeslot data
    $timeslots = Timeslot::where('active', 1)
        ->where('company_id', $companyId) // Filter by company ID
        ->whereIn('log', ['login', 'logout'])
        ->get();

    Log::info('Fetched timeslots', ['company_id' => $companyId, 'timeslots' => $timeslots->toArray()]);

    // Step 4: Process the data
    $response = [];
    foreach ($timeslots as $slot) {
        $company = $slot->company_id;
        $slotData = [
            'shift' => $slot->shift,
            'log' => $slot->log,
            'days_available' => json_decode($slot->days_available, true), // Decode days_available if JSON
        ];

        Log::info('Processing timeslot', ['slot' => $slotData]);

        if (!isset($response[$company])) {
            $response[$company] = [];

	public function getActiveSlots()
    {
        // Fetch and filter the timeslot data
        $timeslots = Timeslot::where('active', 1)
            ->whereIn('log', ['login', 'logout'])
            ->get();

        // Process the data
        $response = [];
        foreach ($timeslots as $slot) {
            $company = $slot->created_to;
            $slotData = [
                'shift' => $slot->from . '-' . $slot->to,
                'log' => $slot->log,
                'days_available' => $slot->days_available,

            ];

            if (!isset($response[$company])) {
                $response[$company] = [];
            }

            $response[$company][] = $slotData;

        }

        $response[$company][] = $slotData;
    }

    Log::info('Final response prepared', ['response' => $response]);

    // Step 5: Return the response as JSON
    return response()->json($response, 200);
}



	public function map_api(Request $request) {

		$validation = Validator::make($request->all(), [
			'user_id' => 'required|integer',
			'fcm_id' => 'required',
			// 'source' => 'required',
			// 'destination' => 'required',
			'src_lat' => 'required',
			'src_long' => 'required',
			'dest_lat' => 'required',
			'dest_long' => 'required',
			// 'type_id' => 'required|integer',
		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$index['success'] = "0";
			$index['message'] = implode(", ", $errors->all());
			$index['data'] = "";
		} else {
			if (User::where('id', $request->user_id)->where('api_token', $request->api_token)->exists()) {

				$key = Hyvikk::api('api_key');

				// $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $request->source . "&destination=" . $request->destination . "&mode=driving&units=metric&sensor=false&key=" . $key;
				$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $request->src_lat . "," . $request->src_long . "&destination=" . $request->dest_lat . "," . $request->dest_long . "&mode=driving&units=metric&sensor=false&key=" . $key;

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
				curl_close($ch);
				$response = json_decode($data, true);
				// dd($response['routes'][0]['legs'][0]['duration']['text']);
				// dd($response['routes'][0]['legs'][0]['distance']['text']);

				// $v_type = VehicleTypeModel::find($request->type_id);
				// $type = strtolower(str_replace(" ", "", $v_type->vehicletype));
				// $fare_details = array();

				// $total_kms = explode(" ", $response['routes'][0]['legs'][0]['distance']['text'])[0];

				// $km_base = Hyvikk::fare($type . '_base_km');
				// $base_fare = Hyvikk::fare($type . '_base_fare');
				// $std_fare = Hyvikk::fare($type . '_std_fare');
				// $base_km = Hyvikk::fare($type . '_base_km');

				// if ($total_kms <= $km_base) {
				//  $total_fare = $base_fare;

				// } else {
				//  $total_fare = $base_fare + (($total_kms - $km_base) * $std_fare);
				// }
				// // calculate tax charges
				// $count = 0;
				// if (Hyvikk::get('tax_charge') != "null") {
				//  $taxes = json_decode(Hyvikk::get('tax_charge'), true);
				//  foreach ($taxes as $key => $val) {
				//      $count = $count + $val;
				//  }
				// }
				// $total_fare = round($total_fare, 2);
				// $tax_total = round((($total_fare * $count) / 100) + $total_fare, 2);
				// $total_tax_percent = $count;
				// $total_tax_charge_rs = round(($total_fare * $count) / 100, 2);

				// $fare_details = array(
				//  'total_amount' => $tax_total,
				//  'total_tax_percent' => $total_tax_percent,
				//  'total_tax_charge_rs' => $total_tax_charge_rs,
				//  'ride_amount' => $total_fare,
				//  'base_fare' => $base_fare,
				//  'base_km' => $base_km,
				// );

				$index['success'] = "1";
				$index['message'] = "Data Received Successfully !";
				$index['data'] = array(
					'map_info' => $response,
					// 'fare_details' => $fare_details,
				);

			} else {
				$index['success'] = "0";
				$index['message'] = "User verification failed!";
				$index['data'] = "";
			}
		}
		return $index;
	}

	public function update_fcm(Request $request) {
		Log::info('update_fcm - Received Request: ' . json_encode($request->all()));
	
		// Validate the input
		$validation = Validator::make($request->all(), [
			'fcm_id' => 'required',
			'user_id' => 'required|integer',
		]);
	
		$errors = $validation->errors();
		if (count($errors) > 0) {
			Log::info('update_fcm - Validation failed: ' . json_encode($errors->all()));
			$data['success'] = 0;
			$data['message'] = "Unable to update FCM ID.";
			$data['data'] = "";
	
			return $data;
		}
	
		$user = User::find($request->user_id);
		if (!$user) {
			Log::info('update_fcm - User not found with ID: ' . $request->user_id);
			$data['success'] = 0;
			$data['message'] = "User not found.";
			$data['data'] = "";
	
			return $data;
		}
	
		Log::info('update_fcm - User found: ' . $user->name);
	
		if ($user->login_status == 1) {
			$user->fcm_id = $request->fcm_id;
			$user->save();
			Log::info('update_fcm - FCM ID updated for user: ' . $user->id . ', New FCM ID: ' . $request->fcm_id);
	
			$data['success'] = 1;
			$data['message'] = "FCM ID updated Successfully!";
			$data['data'] = "";
		} else {
			Log::info('update_fcm - User login status is not active. ID: ' . $user->id);
			$data['success'] = 0;
			$data['message'] = "Unable to update FCM ID.";
			$data['data'] = "";
		}
	
		Log::info('update_fcm - Response: ' . json_encode($data));
		return $data;
	}
	

	public function user_registration(Request $request) {

		$exist = User::where('email', $request->get('emailid'))->withTrashed()->first();
		$number = $request->get('mobno');
		$mobno = User::meta()
			->where(function ($query) use ($number) {
				$query->where('users_meta.key', '=', 'mobno')
					->where('users_meta.value', '=', $number)
					->where('users_meta.deleted_at', '=', null);
			})->exists();
		$validation = Validator::make($request->all(), [
			'mobno' => ['required', new UniqueMobile],
			'gender' => 'required',
			'password' => 'required',
			'emailid' => 'required',
			'user_name' => 'required',
		]);
		$errors = $validation->errors();
		// dd($errors);
		if (count($errors) > 0 || ($exist != null && $exist->deleted_at == null)) {
			$data['success'] = 0;
			if (($exist != null && $exist->deleted_at == null) || $mobno) {
				$data['message'] = "Email Address or Mobile Number Already Exists !";
			} else {
				$data['message'] = "Unable to Register. Please, check the Details OR Try again Later";
			}

			$data['data'] = "";

		} else {
			if ($request->get('mode') == '1') {

				$mode = 'android';
			} else {

				$mode = 'ios';
			}
			if ($exist == null) {

				$id = User::create(['name' => $request->get('user_name'), 'email' => $request->get('emailid'), 'password' => bcrypt($request->get('password')), 'user_type' => 'C', 'api_token' => str_random(60)])->id;
				$user = User::find($id);
				$user->login_status = 1;
				$name = explode(" ", $request->get('user_name'));
				$user->first_name = $name[0];

				if (sizeof($name) > 1) {
					$user->last_name = $name[1];
				}
				$user->setMeta($request->all());
				$user->phone_code = $request->get('phone_code');
				$user->mode = $mode;
				$user->save();

			} else {
				$user = $exist;

				$user->name = $request->get('user_name');
				$user->email = $request->get('emailid');
				$user->password = bcrypt($request->get('password'));
				$user->user_type = "C";
				$user->api_token = str_random(60);
				$user->deleted_at = null;
				$user->setMeta($request->all());
				$user->mode = $mode;
				$user->phone_code = $request->get('phone_code');
				$user->save();
			}
			$data['success'] = 1;
			$data['message'] = "You have Registered Successfully!";
			$data['data'] = ['userinfo' => array('user_id' => $user->id, 'api_token' => $user->api_token, 'fcm_id' => $user->getMeta('fcm_id'), 'device_token' => $user->getMeta('device_token'), 'socialmedia_uid' => $user->getMeta('socialmedia_uid'), 'user_name' => $user->name, 'mobno' => $user->getMeta('mobno'), 'phone_code' => $user->phone_code, 'emailid' => $user->email, 'gender' => $user->getMeta('gender'), 'password' => $user->password, 'profile_pic' => $user->getMeta('profile_pic'), 'status' => $user->getMeta('login_status'), 'timestamp' => date('Y-m-d H:i:s', strtotime($user->created_at)))];
		}
		return $data;

	}

	// user_login without social media connected
	public function user_login(Request $request) {
		$email = $request->get("username");
		$password = $request->get("password");

		if (Login::attempt(['email' => $email, 'password' => $password])) {
			$user = Login::user();
			$user->fcm_id = $request->get('fcm_id');
			$user->login_status = 1;

			$user->device_token = $request->get('device_token');
			$user->save();
			$data['success'] = 1;
			$data['message'] = "You have Signed in Successfully!";
			if ($user->user_type == "C") {
				$data['data'] = ['userinfo' => array("user_id" => $user->id,
					"api_token" => $user->api_token,
					"fcm_id" => $user->getMeta('fcm_id'),
					"device_token" => $user->getMeta('device_token'),
					"socialmedia_uid" => $user->getMeta('socialmedia_uid'),
					"user_name" => $user->name,
					"user_type" => $user->user_type,
					"mobno" => $user->getMeta('mobno'),
					"phone_code" => $user->getMeta('phone_code'),
					"emailid" => $user->email,
					"gender" => $user->getMeta('gender'),
					"password" => $user->password,
					"profile_pic" => $user->getMeta('profile_pic'),
					"status" => $user->getMeta('login_status'),
					"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)))];
			}
			if ($user->user_type == "D") {
				if ($user->vehicle_id != null) {
					$v = VehicleModel::whereIn('id', $user->vehicle_id)->get();
					$vehicle = (implode(',', $v->pluck('license_plate')->toArray())) ?? "";
				} else { $vehicle = "";}
				$data['data'] = ['userinfo' => array("user_id" => $user->id,
					"api_token" => $user->api_token,
					"fcm_id" => $user->getMeta('fcm_id'),
					"device_token" => $user->getMeta('device_token'),
					"socialmedia_uid" => "",
					"user_name" => $user->name,
					"user_type" => $user->user_type,
					"mobno" => $user->getMeta('phone'),
					"phone_code" => $user->getMeta('phone_code'),
					"emailid" => $user->email,
					"gender" => $user->getMeta('gender'),
					"password" => $user->password,
					"profile_pic" => $user->getMeta('driver_image'),
					"address" => $user->getMeta('address'),
					"id-proof" => $user->getMeta('license_image'),
					"id-proof-type" => "License",
					"vehicle-number" => $vehicle,
					"availability" => $user->getMeta('is_available'),
					"status" => $user->getMeta('login_status'),
					"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)))];

			}

		} else {

			$data['success'] = 0;
			$data['message'] = "Invalid Login Credentials";
			$data['data'] = "";
		}

		return $data;
	}

	// user login through social media
	public function login_with_sm(Request $request) {
		if ($request->get('is_smconnected') == 1) {
			$user = User::where('email', $request->get('emailid'))->withTrashed()->first();
			if ($user == null) {
				$validation = Validator::make($request->all(), [
					'mobno' => ['required', new UniqueMobile],
					'gender' => 'required',
					'emailid' => 'required',
					'user_name' => 'required',
				]);
				$errors = $validation->errors();

				if (count($errors) > 0) {
					$data['success'] = 0;
					$data['message'] = "Login failed. Please, Try Again Later…";
					$data['data'] = "";

				} else {
					$id = User::create(['name' => $request->get('user_name'), 'email' => $request->get('emailid'), 'password' => "", 'user_type' => 'C', 'api_token' => str_random(60)])->id;
					$newuser = User::find($id);
					$name = explode(" ", $request->get('user_name'));
					$newuser->first_name = $name[0];

					if (sizeof($name) > 1) {
						$newuser->last_name = $name[1];
					}
					$form_data = $request->all();
					unset($form_data['profile_pic']);
					$newuser->setMeta($form_data);
					if ($request->get('is_profilepic_selected') == '0') {
						$newuser->profile_pic = $request->get('profile_pic'); //direct url
					} else {
						if ($request->file('profile_pic') && $request->file('profile_pic')->isValid()) {
							if (file_exists('./uploads/' . $newuser->profile_pic) && !is_dir('./uploads/' . $newuser->profile_pic)) {
								unlink('./uploads/' . $newuser->profile_pic);
							}
							$this->upload_file($request->file('profile_pic'), "profile_pic", $newuser->id);
						}

					}
					$newuser->phone_code = $request->get('phone_code');
					$newuser->save();
					Login::loginUsingId($id);
					$user = Login::user();
					$user->login_status = 1;

					$user->save();
					$data['success'] = 1;
					$data['message'] = "You have Registered Successfully!";
					$data['data'] = ['userinfo' => array("user_id" => $user->id,
						'user_type' => $user->user_type,
						"api_token" => $user->api_token,
						"fcm_id" => $user->getMeta('fcm_id'),
						"device_token" => $user->getMeta('device_token'),
						"socialmedia_uid" => $user->getMeta('socialmedia_uid'),
						"user_name" => $user->name,
						"mobno" => $user->getMeta('mobno'),
						"phone_code" => $user->getMeta('phone_code'),
						"emailid" => $user->email,
						"gender" => $user->getMeta('gender'),
						"password" => $user->password,
						"profile_pic" => $user->getMeta('profile_pic'),
						"status" => $user->getMeta('login_status'),
						"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)))];
				}
			} elseif ($user != null) {
				if ($user->deleted_at == null) {

					Login::loginUsingId($user->id);
					$user->login_status = 1;
					$user = Login::user();
					$user->email = $request->get('emailid');
					$user->name = $request->get('user_name');
					$user->gender = $request->get('gender');
					$user->mobno = $request->get('mobno');
					$user->fcm_id = $request->get('fcm_id');
					if ($request->get('is_profilepic_selected') == '0') {
						$user->profile_pic = $request->get('profile_pic'); //direct url
					} else {
						if ($request->file('profile_pic') && $request->file('profile_pic')->isValid()) {
							if (file_exists('./uploads/' . $user->profile_pic) && !is_dir('./uploads/' . $user->profile_pic)) {
								unlink('./uploads/' . $user->profile_pic);
							}
							$this->upload_file($request->file('profile_pic'), "profile_pic", $user->id);
						}

					}
					$user->device_token = $request->get('device_token');
					$user->socialmedia_uid = $request->get('socialmedia_uid');
					$user->phone_code = $request->get('phone_code');
					$user->save();
					$user = User::find($user->id);
					$data['success'] = 1;
					$data['message'] = "You have Signed in Successfully!";
					$data['data'] = ['userinfo' => array("user_id" => $user->id,
						'user_type' => $user->user_type,
						"fcm_id" => $user->getMeta('fcm_id'),
						"api_token" => $user->api_token,
						"device_token" => $user->getMeta('device_token'),
						"socialmedia_uid" => $user->getMeta('socialmedia_uid'),
						"user_name" => $user->name,
						"mobno" => $user->getMeta('mobno'),
						"phone_code" => $user->getMeta('phone_code'),
						"emailid" => $user->email,
						"gender" => $user->getMeta('gender'),
						"password" => $user->password,
						"profile_pic" => $user->getMeta('profile_pic'),
						"status" => $user->getMeta('login_status'),
						"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)))];
				} elseif ($user->deleted_at != null) {

					$user->deleted_at = null;
					$user->save();
					Login::loginUsingId($user->id);

					$user->login_status = 1;
					$user = Login::user();
					$user->name = $request->get('user_name');
					$user->gender = $request->get('gender');
					$user->mobno = $request->get('mobno');
					$user->fcm_id = $request->get('fcm_id');
					if ($request->get('is_profilepic_selected') == '0') {
						$user->profile_pic = $request->get('profile_pic'); //direct url
					} else {
						if ($request->file('profile_pic') && $request->file('profile_pic')->isValid()) {
							if (file_exists('./uploads/' . $user->profile_pic) && !is_dir('./uploads/' . $user->profile_pic)) {
								unlink('./uploads/' . $user->profile_pic);
							}
							$this->upload_file($request->file('profile_pic'), "profile_pic", $user->id);
						}

					}
					$user->device_token = $request->get('device_token');
					$user->socialmedia_uid = $request->get('socialmedia_uid');
					$user->phone_code = $request->get('phone_code');
					$user->save();
					$user = User::find($user->id);
					// dd($user->getMeta('profile_pic'));
					$data['success'] = 1;
					$data['message'] = "You have Signed in Successfully!";
					$data['data'] = ['userinfo' => array("user_id" => $user->id,
						'user_type' => $user->user_type,
						"fcm_id" => $user->getMeta('fcm_id'),
						"api_token" => $user->api_token,
						"device_token" => $user->getMeta('device_token'),
						"socialmedia_uid" => $user->getMeta('socialmedia_uid'),
						"user_name" => $user->name,
						"mobno" => $user->getMeta('mobno'),
						"phone_code" => $user->getMeta('phone_code'),
						"emailid" => $user->email,
						"gender" => $user->getMeta('gender'),
						"password" => $user->password,
						"profile_pic" => $user->getMeta('profile_pic'),
						"status" => $user->getMeta('login_status'),
						"timestamp" => date('Y-m-d H:i:s', strtotime($user->created_at)))];

				}
			} else {
				$data['success'] = 0;
				$data['message'] = "Login failed. Please, Try Again Later…";
				$data['data'] = "";
			}
			return $data;
		}

	}

	public function forgot_password(Request $request) {
		$this->validateEmail($request);

		$response = $this->broker()->sendResetLink(
			$request->only('email')
		);

		if ($response == Password::RESET_LINK_SENT) {
			$data['success'] = 1;
			$data['message'] = "Please, check your Emails for Login Credentials.";
			$data['data'] = "";
		} else {
			$data['success'] = 0;
			$data['message'] = "Could not Send Verification Email. Please, Try again Later!";
			$data['data'] = "";
		}

		return $data;

	}

	protected function validateEmail(Request $request) {
		$this->validate($request, ['email' => 'required|email']);
	}

	public function broker() {
		return Password::broker();
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

	public function edit_profile(Request $request) {
		$user = User::find($request->get('user_id'));
		$validation = Validator::make($request->all(), [
			'mobno' => ['required', new UniqueMobile],
			'emailid' => 'required|unique:users,email,' . \Request::get('user_id'),

		]);
		$errors = $validation->errors();
		// dd($errors);
		$number = $request->get('mobno');
		$userid = $request->get('user_id');
		$mobno = User::meta()
			->where(function ($query) use ($number, $userid) {
				$query->where('users_meta.key', '=', 'mobno')
					->where('users_meta.value', '=', $number)
					->where('users_meta.user_id', '!=', $userid)
					->where('users_meta.deleted_at', '=', null);

			})->exists();
		$exist = User::where('email', $request->get('emailid'))->where('id', '!=', $userid)->withTrashed()->exists();
		if (count($errors) > 0 || $user == null) {
			$data['success'] = 0;
			if ($mobno || $exist) {
				$data['message'] = "Email Address or Mobile Number Already Registered.";
			} else {
				$data['message'] = "Unable to Update Profile. Please,Try again Later!";

			}

			$data['data'] = "";

		} else {

			if ($user->user_type == "C") {
				if ($request->get('is_profilepic_selected') == '0') {
					$user->profile_pic = $request->get('profile_pic'); //direct url
				} else {
					if ($request->file('profile_pic') && $request->file('profile_pic')->isValid()) {
						if (file_exists('./uploads/' . $user->profile_pic) && !is_dir('./uploads/' . $user->profile_pic)) {
							unlink('./uploads/' . $user->profile_pic);
						}
						$this->upload_file($request->file('profile_pic'), "profile_pic", $user->id);
					}

				}

				$name = explode(" ", $request->get('user_name'));
				$user->name = $request->get('user_name');
				$user->first_name = $name[0];
				if (sizeof($name) > 1) {
					$user->last_name = $name[1];
				}

				$user->mobno = $request->get('mobno');
				$user->email = $request->get('emailid');
				$user->gender = $request->get('gender');
				$user->phone_code = $request->get('phone_code');
				$user->save();
				// $user = User::find($user->id);
				$data['success'] = 1;
				$data['message'] = "Profile has been Updated Successfully!";
				$data['data'] = ['userinfo' => array('user_id' => $request->get('user_id'), "user_type" => $user->user_type, 'user_name' => $user->name, 'mobno' => $user->getMeta('mobno'), "phone_code" => $user->getMeta('phone_code'), 'emailid' => $user->email, 'gender' => $user->getMeta('gender'), 'profile_pic' => $user->getMeta('profile_pic'), 'status' => $user->getMeta('login_status'), 'timestamp' => date('Y-m-d H:i:s', strtotime($user->updated_at)))];
			}

			if ($user->user_type == "D") {
				if ($request->get('is_profilepic_selected') == '0') {

					$user->driver_image = $request->get('profile_pic'); //direct url
				} else {
					if ($request->file('profile_pic') && $request->file('profile_pic')->isValid()) {
						if (file_exists('./uploads/' . $user->driver_image) && !is_dir('./uploads/' . $user->driver_image)) {
							unlink('./uploads/' . $user->driver_image);
						}
						$this->upload_file($request->file('profile_pic'), "driver_image", $user->id);
					}

				}

				if ($user->vehicle_id != null) {
					$v = VehicleModel::whereIn('id', $user->vehicle_id)->get();
					$vehicle = (implode(',', $v->pluck('license_plate')->toArray())) ?? "";
				} else { $vehicle = "";}

				$user->phone = $request->get('mobno');
				$user->email = $request->get('emailid');
				$user->phone_code = $request->get('phone_code');
				$user->save();
				// $user = User::find($user->id);

				$data['success'] = 1;
				$data['message'] = "Profile has been Updated Successfully!";
				$data['data'] = ['userinfo' => array("user_id" => $user->id,
					"user_name" => $user->name,
					"user_type" => $user->user_type,
					"mobno" => $user->getMeta('phone'),
					"phone_code" => $user->getMeta('phone_code'),
					"emailid" => $user->email,
					"gender" => $user->getMeta('gender'),
					"password" => $user->password,
					"profile_pic" => $user->getMeta('driver_image'),
					"status" => $user->getMeta('login_status'),
					"address" => $user->getMeta('address'),
					"id-proof" => $user->getMeta('license_image'),
					"id-proof-type" => "License",
					"vehicle-number" => $vehicle,
					"availability" => $user->getMeta('is_available'),
					"timestamp" => date('Y-m-d H:i:s', strtotime($user->updated_at)))];
			}
		}

		return $data;
	}

	public function change_password(Request $request) {
		$user = User::find($request->get('user_id'));
		$validation = Validator::make($request->all(), [
			'new_password' => 'required',
		]);
		$errors = $validation->errors();
		if ($user == null || count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = "Unable to Update Password. Please, Try again Later!";
			$data['data'] = "";

		} else {
			$user->password = bcrypt($request->get('new_password'));
			$user->save();
			$data['success'] = 1;
			$data['message'] = "Your Password has been Updated Successfully.";
			$data['data'] = "";

		}
		return $data;
	}

	public function message_us(Request $request) {
		$validation = Validator::make($request->all(), [
			'message' => 'required',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = "Oops, Something got Wrong. Please, Try again Later!";
			$data['data'] = "";
		} else {
			$user = User::find($request->user_id);
			MessageModel::create(['fcm_id' => $request->fcm_id, 'user_id' => $request->user_id, 'message' => $request->message, 'name' => $user->name, 'email' => $user->email]);
			$data['success'] = 1;
			$data['message'] = "Thank you ! We will get back to you Soon...";
			$data['data'] = "";
		}
		return $data;
	}

	public function book_now(Request $request) {

		$validation = Validator::make($request->all(), [
			'source_address' => 'required',
			'dest_address' => 'required',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0 || $request->get('booking_type') != 0) {
			$data['success'] = 0;
			$data['message'] = "Unable to Process your Ride Request. Please, Try again Later!";
			$data['data'] = "";
		} else {
			$booking = Bookings::create(['customer_id' => $request->get('user_id'),
				'pickup_addr' => $request->get('source_address'),
				'dest_addr' => $request->get('dest_address'),
				'travellers' => $request->get('no_of_persons'),
			]);

			$book = Bookings::find($booking->id);
			$book->fcm_id = $request->get('fcm_id');
			$book->source_lat = $request->get('source_lat');
			$book->source_long = $request->get('source_long');
			$book->dest_lat = $request->get('dest_lat');
			$book->dest_long = $request->get('dest_long');
			$book->journey_date = date('d-m-Y');
			$book->journey_time = date('H:i:s');
			$book->accept_status = 0; //0=yet to accept, 1= accept
			$book->ride_status = null;
			$book->booking_type = 0;
			$book->vehicle_typeid = $request->vehicle_typeid;
			$book->save();
			$vehicle_typeid = $request->vehicle_typeid;
			$this->book_now_notification($booking->id, $vehicle_typeid);
			$data['success'] = 1;
			$data['message'] = "Your Request has been Submitted Successfully.";
			$data['data'] = array('booking_id' => $booking->id);
			// browser notification to driver,admin,customer

		}
		return $data;
	}

	public function book_later(Request $request) {
		$validation = Validator::make($request->all(), [
			'source_address' => 'required',
			'dest_address' => 'required',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0 || $request->get('booking_type') != 1) {
			$data['success'] = 0;
			$data['message'] = "Unable to Process your Ride Request. Please, Try again Later!";
			$data['data'] = "";
		} else {
			$booking = Bookings::create(['customer_id' => $request->get('user_id'),
				'pickup_addr' => $request->get('source_address'),
				'dest_addr' => $request->get('dest_address'),
				'travellers' => $request->get('no_of_persons'),
			]);

			$book = Bookings::find($booking->id);
			$book->fcm_id = $request->get('fcm_id');
			$book->source_lat = $request->get('source_lat');
			$book->source_long = $request->get('source_long');
			$book->dest_lat = $request->get('dest_lat');
			$book->dest_long = $request->get('dest_long');
			$book->journey_date = $request->get('journey_date');
			$book->journey_time = $request->get('journey_time');
			$book->booking_type = 1;
			$book->accept_status = 0; //0=yet to accept, 1= accept
			$book->ride_status = null;
			$book->vehicle_typeid = $request->vehicle_typeid;
			$book->save();
			$vehicle_typeid = $request->vehicle_typeid;
			$this->book_later_notification($book->id, $vehicle_typeid);
			$data['success'] = 1;
			$data['message'] = "Your Request has been Submitted Successfully.";
			$data['data'] = array('booking_id' => $booking->id);
			// browser notification to driver,admin,customer

		}
		return $data;

	}

	public function update_destination(Request $request) {
		$booking = Bookings::find($request->get('booking_id'));
		$validation = Validator::make($request->all(), [
			'dest_address' => 'required',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0 || $booking == null) {
			$data['success'] = 0;
			$data['message'] = "Unable to Process your Ride Request. Please, Try again Later !";
			$data['data'] = "";
		} else {
			$d_lat = $booking->getMeta('dest_lat');
			$d_long = $booking->getMeta('dest_long');
			$old = $booking->dest_addr;
			$booking->dest_addr = $request->get('dest_address');
			$booking->dest_lat = $request->get('dest_lat');
			$booking->dest_long = $request->get('dest_long');
			$booking->save();
			$this->update_dest_notification($booking->id, $old, $d_lat, $d_long);
			$this->ride_ongoing_notification($booking->id);
			$data['success'] = 1;
			$data['message'] = "Your Destination has  been Updated Successfully.";
			$data['data'] = ['rideinfo' => array(
				"user_id" => $request->get('user_id'),
				"booking_id" => $request->get('booking_id'),
				"dest_address" => $request->get('dest_address'),
				"dest_lat" => $request->get('dest_lat'),
				"dest_long" => $request->get('dest_long'),
			)];
		}
		return $data;
	}

	public function ride_ongoing_notification($id) {
		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "ride_ongoing_notification";
		$data['message'] = 'Data Received.';
		$data['title'] = "Heading Towards [ " . $booking->dest_addr . " ]";
		$data['description'] = "Ongoing Ride From [ " . $booking->pickup_addr . " ]";
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array(
			'user_id' => $booking->customer_id,
			'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
			'booking_id' => $id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'start_lat' => $booking->getMeta('start_lat'),
			'start_long' => $booking->getMeta('start_long'),
			'approx_timetoreach' => $booking->getMeta('approx_timetoreach'),
			'user_name' => $booking->customer->name,
			'user_profile' => $booking->customer->getMeta('profile_pic'),
		);
		if ($booking->customer->getMeta('fcm_id') != null) {
			// PushNotification::app('appNameAndroid')
			//     ->to($booking->customer->getMeta('fcm_id'))
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$booking->customer->getMeta('fcm_id')])
				->send();
		}
		if ($booking->driver->getMeta('fcm_id') != null) {
			// PushNotification::app('appNameAndroid')
			//     ->to($booking->driver->getMeta('fcm_id'))
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$booking->driver->getMeta('fcm_id')])
				->send();
		}

	}

	public function update_dest_notification($id, $old, $d_lat, $d_long) {
		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "update_destination_notification";
		$data['message'] = 'Data Received.';
		$data['title'] = "Destination Updated for the Ongoing Ride";
		$data['description'] = "Refresh the Route.";
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('rideinfo' => array('user_id' => $booking->customer_id,
			'booking_id' => $booking->id,
			'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
			'user_name' => $booking->customer->name,
			'user_profile' => $booking->customer->getMeta('profile_pic'),
			'source_address' => $booking->pickup_addr,
			'dest_address' => $old,
			'source_lat' => $booking->getMeta('source_lat'),
			'source_long' => $booking->getMeta('source_long'),
			'dest_lat' => $d_lat,
			'dest_long' => $d_long,
			'new_dest_address' => $booking->dest_addr,
			'approx_timetoreach' => $booking->getMeta('approx_timetoreach'),
			'new_dest_lat' => $booking->getMeta('dest_lat'),
			'new_dest_long' => $booking->getMeta('dest_long')));
		if ($booking->driver->getMeta('fcm_id') != null) {
			// PushNotification::app('appNameAndroid')
			//     ->to($booking->driver->getMeta('fcm_id'))
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$booking->driver->getMeta('fcm_id')])
				->send();
		}

	}

	public function review(Request $request) {
		$validation = Validator::make($request->all(), [
			'ratings' => 'required|numeric|min:1|max:5',
			'review_text' => 'required',
			'booking_id' => 'required',
		]);
		$errors = $validation->errors();
		if (count($errors) > 0) {
			$data['success'] = 0;
			$data['message'] = "Unable to Save your Reviews. Please, Try again Later!";
			$data['data'] = "";

		} else {
			$book = Bookings::find($request->get('booking_id'));
			$review = ReviewModel::create(['user_id' => $request->get('user_id'),
				'driver_id' => $book->driver_id,
				'booking_id' => $request->get('booking_id'),
				'ratings' => $request->get('ratings'),
				'review_text' => $request->get('review_text'),
			]);
			$this->share_ride_review($book->id);
			$data['success'] = 1;
			$data['message'] = "Thank you. Your Review helps us Improve our Services.";
			$data['data'] = ['rideinfo' => array('user_id' => $review->user_id, 'booking_id' => $review->booking_id, 'ratings' => $review->ratings, 'review_text' => $review->review_text)];
		}
		return $data;

	}

	public function ride_history(Request $request) {
		$bookings = Bookings::where('customer_id', $request->get('customer_id'))->get();

		if ($bookings->toArray() != null) {
			$data['success'] = 1;
			$data['message'] = "Data Received.";
			if (Hyvikk::get('dis_format') == 'meter') {
				$unit = 'm';
			}if (Hyvikk::get('dis_format') == 'km') {
				$unit = 'km';
			}

			foreach ($bookings as $book) {
				if ($book->getMeta('total_kms') != null) {
					$total_kms = $book->getMeta('total_kms') . " " . $unit;
				} else {
					$total_kms = "";
				}
				$details[] = array('booking_id' => $book->id, 'user_id' => $book->customer_id, 'book_date' => date('Y-m-d', strtotime($book->created_at)), 'book_time' => date('H:i:s', strtotime($book->created_at)), 'source_address' => $book->pickup_addr, 'source_time' => date('Y-m-d H:i:s', strtotime($book->getMeta('ridestart_timestamp'))), 'dest_address' => $book->dest_addr, 'dest_time' => date('Y-m-d H:i:s', strtotime($book->getMeta('rideend_timestamp'))), 'driving_time' => $book->getMeta('driving_time'), 'total_kms' => $total_kms, 'amount' => $book->getMeta('total'), 'ride_status' => $book->getMeta('ride_status'));
			}
			$data['data'] = array('rides' => $details);

		} else {
			$data['success'] = 0;
			$data['message'] = "Unable to Receive Rides History. Please, Try again Later!";
			$data['data'] = "";
		}

		return $data;
	}

	public function user_single_ride_info(Request $request) {
		$booking = Bookings::find($request->get('booking_id'));
		if (Hyvikk::get('dis_format') == 'meter') {
			$unit = 'm';
		}if (Hyvikk::get('dis_format') == 'km') {
			$unit = 'km';
		}

		if ($booking == null) {
			$data['success'] = 0;
			$data['message'] = "Unable to Receive Ride Details. Please, Try again Later !";
			$data['data'] = "";
		} else {
			$rideinfo = array('user_id' => $booking->customer_id, 'booking_id' => $booking->id, 'source_address' => $booking->pickup_addr, 'dest_address' => $booking->dest_addr, 'source_time' => date('H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
				'dest_time' => date('H:i:s', strtotime($booking->getMeta('rideend_timestamp'))),
				'book_date' => date('Y-m-d', strtotime($booking->created_at)),
				'book_time' => date('H:i:s', strtotime($booking->created_at)),
				'driving_time' => $booking->getMeta('driving_time'),
				'total_kms' => $booking->getMeta('total_kms') . " " . $unit,
				'amount' => $booking->getMeta('total'),
				'ride_status' => $booking->getMeta('ride_status'),
			);
			$d = User::find($booking->driver_id);
			$reviews = ReviewModel::where('booking_id', $request->get('booking_id'))->first();
			$avg = ReviewModel::where('driver_id', $booking->driver_id)->avg('ratings');

			$driver = array('driver_id' => $booking->driver_id,
				'driver_name' => $d->name,
				'profile_pic' => $d->getMeta('driver_image'),
				'ratings' => round($avg, 2),
			);

			if ($reviews == null) {
				$review = new \stdClass;
			} else {

				$review = array('user_id' => $reviews->user_id, 'booking_id' => $reviews->booking_id, 'ratings' => $reviews->ratings, 'review_text' => $reviews->review_text, 'date' => date('Y-m-d', strtotime($reviews->created_at)));

			}
			$data['success'] = 1;
			$data['message'] = "Data Received.";
			$data['data'] = array('rideinfo' => $rideinfo, 'driver_details' => $driver, 'ride_review' => $review, 'fare_breakdown' => array('base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', $booking->vehicle->types->vehicletype)) . '_base_fare'),
				'ride_amount' => $booking->getMeta('total'), 'extra_charges' => 0));
		}
		return $data;
	}

	public function get_reviews(Request $request) {
		$reviews = ReviewModel::where('driver_id', $request->get('driver_id'))->where('booking_id', '!=', $request->get('booking_id'))->get();

		if ($reviews->toArray() != null) {
			$data['success'] = 1;
			$data['message'] = "Data Received.";
			foreach ($reviews as $r) {
				$review[] = array('user_id' => $r->user->id, 'user_name' => $r->user->name, 'profile_pic' => $r->user->getMeta('profile_pic'), 'booking_id' => $r->booking_id, 'ratings' => $r->ratings, 'review_text' => $r->review_text, 'date' => date('Y-m-d', strtotime($r->created_at)));
			}
			$data['data'] = ['driver_reviews' => $review];
		} else {
			$data['success'] = 0;
			$data['message'] = "Unable to Receive Driver's Reviews. Please, Try again Later!";
			$data['data'] = "";
		}
		return $data;
	}

	public function user_logout(Request $request) {

		$user = User::find($request->get('user_id'));
		$user->login_status = 0;
		$user->is_available = 0;
		$user->save();
		if ($user->login_status == 0) {
			$data['success'] = 1;
			$data['message'] = "You have Logged out Successfully.";
			$data['data'] = "";
		} else {
			$data['success'] = 0;
			$data['message'] = "Unable to Logout. Please, Try again Later!";
			$data['data'] = "";
		}

		return $data;
	}

	public function book_now_notification($id, $type_id) {

		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "book_now_notification";
		$data['message'] = 'Data Received.';
		$data['title'] = "New Ride Request (Book Now)";
		$data['description'] = "Do you want to Accept it ?";
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('riderequest_info' => array(
			'user_id' => $booking->customer_id,
			'booking_id' => $booking->id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'book_date' => date('Y-m-d'),
			'book_time' => date('H:i:s'),
			'journey_date' => date('d-m-Y'),
			'journey_time' => date('H:i:s'),
			'accept_status' => $booking->accept_status));
		if ($type_id == null) {
			$vehicles = VehicleModel::get()->pluck('id')->toArray();
		} else {
			$vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
		}
		$drivers = User::where('user_type', 'D')->get();

		// $test = PushNotification::app('appNameAndroid')
		//     ->to("fCsWgScV2qU:APA91bGeT1OKws4zk-1u09v83XFrnmEaIidPRl4-sTTOBbPvHXrq6lkRBLCfQFMml5v3gB1zbS0PDttKwEhvWC1fUQVhWhutVxKeVaxvPofD6XgMQn9UPJCKFnrB8h3amL0bhfFh4s98")
		//     ->send($data);
		// dd($test);

		foreach ($drivers as $d) {
			if (in_array($d->vehicle_id, $vehicles)) {

				if ($d->getMeta('fcm_id') != null && $d->getMeta('is_available') == 1 && $d->getMeta('is_on') != 1) {

					// PushNotification::app('appNameAndroid')
					//     ->to($d->getMeta('fcm_id'))
					//     ->send($data);

					$push = new PushNotification('fcm');
					$push->setMessage($data)
						->setApiKey(env('server_key'))
						->setDevicesToken([$d->getMeta('fcm_id')])
						->send();
				}
			}

		}

	}

	public function book_later_notification($id, $type_id) {
		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "book_later_notification";
		$data['message'] = 'Data Received.';
		$data['title'] = "New Ride Request (Book Later)";
		$data['description'] = "Do you want to Accept it ?";
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('riderequest_info' => array('user_id' => $booking->customer_id,
			'booking_id' => $booking->id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'book_date' => date('Y-m-d'),
			'book_time' => date('H:i:s'),
			'journey_date' => $booking->getMeta('journey_date'),
			'journey_time' => $booking->getMeta('journey_time'),
			'accept_status' => $booking->accept_status));
		if ($type_id == null) {
			$vehicles = VehicleModel::get()->pluck('id')->toArray();
		} else {
			$vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
		}
		$drivers = User::where('user_type', 'D')->get();
		foreach ($drivers as $d) {
			if (in_array($d->vehicle_id, $vehicles)) {
				// echo $d->vehicle_id . " " . $d->id . "<br>";
				if ($d->getMeta('fcm_id') != null) {
					// PushNotification::app('appNameAndroid')
					//     ->to($d->getMeta('fcm_id'))
					//     ->send($data);

					$push = new PushNotification('fcm');
					$push->setMessage($data)
						->setApiKey(env('server_key'))
						->setDevicesToken([$d->getMeta('fcm_id')])
						->send();
				}
			}
		}

	}

	public function share_ride_review($id) {
		$review = ReviewModel::where('booking_id', $id)->first();
		if ($review != null) {
			$ride_review = array(
				'user_id' => $review->user_id,
				'booking_id' => $id,
				'ratings' => $review->ratings,
				'review_text' => $review->review_text,
			);
		} else {
			$ride_review = array();
		}
		if (Hyvikk::get('dis_format') == 'meter') {
			$unit = 'm';
		}if (Hyvikk::get('dis_format') == 'km') {
			$unit = 'km';
		}
		$booking = Bookings::find($id);
		$data['success'] = 1;
		$data['key'] = "share_review_notification";
		$data['message'] = 'Data Received.';
		$data['title'] = "New Review for the Ride : " . $id;
		$data['description'] = $review->review_text . ": " . $review->ratings . " stars";
		$data['timestamp'] = date('Y-m-d H:i:s');
		$data['data'] = array('rideinfo' => array(
			'user_id' => $booking->customer_id,
			'booking_id' => $id,
			'source_address' => $booking->pickup_addr,
			'dest_address' => $booking->dest_addr,
			'source_time' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
			'dest_time' => date('Y-m-d H:i:s', strtotime($booking->getMeta('rideend_timestamp'))),

			'book_date' => date('d-m-Y', strtotime($booking->created_at)),
			'book_time' => date('Y-m-d H:i:s', strtotime($booking->created_at)),

			'driving_time' => $booking->getMeta('driving_time'),
			'total_kms' => $booking->getMeta('total_kms') . " " . $unit,
			'amount' => $booking->getMeta('total'),
			'ride_status' => $booking->getMeta('ride_status'),
			'base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', $booking->vehicle->types->vehicletype)) . '_base_fare'),
			'ride_amount' => $booking->getMeta('total'),
			'extra_charges' => 0,
			'payment_mode' => 'CASH',
			'is_confirmed' => $booking->status,
		),
			'ride_review' => $ride_review,
			'user_details' => array(
				'user_id' => $booking->customer_id,
				'user_name' => $booking->customer->name,
				'profile_pic' => $booking->customer->getMeta('profile_pic'),
			),
		);
		if ($booking->driver->getMeta('fcm_id') != null) {
			// PushNotification::app('appNameAndroid')
			//     ->to($booking->driver->getMeta('fcm_id'))
			//     ->send($data);

			$push = new PushNotification('fcm');
			$push->setMessage($data)
				->setApiKey(env('server_key'))
				->setDevicesToken([$booking->driver->getMeta('fcm_id')])
				->send();
		}

	}

}
