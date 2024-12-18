<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\VehicleModel;
use App\Model\VehicleReviewModel;
use Auth;
use Illuminate\Http\Request;
use Validator;

class VehicleInspectionApiController extends Controller {

	public function get_vehicles() {
		$user = Auth::user();
		if ($user->group_id == null || $user->user_type == "S") {
			$vehicles = VehicleModel::get();
		} else {
			$vehicles = VehicleModel::where('group_id', $user->group_id)->get();
		}
		$details = array();
		foreach ($vehicles as $row) {
			$details[] = array(
				'id' => $row->id,
				'name' => $row->make_name . " - " . $row->model_name . " - " . $row->license_plate,
			);
		}
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
			VehicleReviewModel::whereIn('id', $request->ids)->delete();

			$data['success'] = "1";
			$data['message'] = "Records deleted successfully!";
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

			VehicleReviewModel::where('id', $request->id)->delete();

			$data['success'] = "1";
			$data['message'] = "Record deleted successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function update(Request $request) {
		$validation = Validator::make($request->all(), [
			'id' => 'required|integer',
			'vehicle_id' => 'required|integer',
			'reg_no' => 'required',
			'kms_outgoing' => 'required|numeric',
			'kms_incoming' => 'required|numeric',
			'datetime_outgoing' => 'required',
			'datetime_incoming' => 'required',
			'fuel_level_out' => 'required|integer',
			'fuel_level_in' => 'required|integer',
			// 'array' => 'required|array',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			$petrol_card = array('flag' => $request->get('petrol_card'), 'text' => $request->get('petrol_card_text'));
			$lights = array('flag' => $request->get('lights'), 'text' => $request->get('lights_text'));
			$invertor = array('flag' => $request->get('invertor'), 'text' => $request->get('invertor_text'));
			$car_mats = array('flag' => $request->get('car_mats'), 'text' => $request->get('car_mats_text'));
			$int_damage = array('flag' => $request->get('int_damage'), 'text' => $request->get('int_damage_text'));
			$int_lights = array('flag' => $request->get('int_lights'), 'text' => $request->get('int_lights_text'));
			$ext_car = array('flag' => $request->get('ext_car'), 'text' => $request->get('ext_car_text'));
			$tyre = array('flag' => $request->get('tyre'), 'text' => $request->get('tyre_text'));
			$ladder = array('flag' => $request->get('ladder'), 'text' => $request->get('ladder_text'));
			$leed = array('flag' => $request->get('leed'), 'text' => $request->get('leed_text'));
			$power_tool = array('flag' => $request->get('power_tool'), 'text' => $request->get('power_tool_text'));
			$ac = array('flag' => $request->get('ac'), 'text' => $request->get('ac_text'));
			$head_light = array('flag' => $request->get('head_light'), 'text' => $request->get('head_light_text'));
			$lock = array('flag' => $request->get('lock'), 'text' => $request->get('lock_text'));
			$windows = array('flag' => $request->get('windows'), 'text' => $request->get('windows_text'));
			$condition = array('flag' => $request->get('condition'), 'text' => $request->get('condition_text'));
			$oil_chk = array('flag' => $request->get('oil_chk'), 'text' => $request->get('oil_chk_text'));
			$suspension = array('flag' => $request->get('suspension'), 'text' => $request->get('suspension_text'));
			$tool_box = array('flag' => $request->get('tool_box'), 'text' => $request->get('tool_box_text'));

			// $all_variables = $request->array;
			// foreach ($all_variables as $var) {
			//     $var['label'] = array('flag' => ($var['state']) ? 1 : 0, 'text' => $var['details']);
			// }

			VehicleReviewModel::where('id', $request->id)->update([
				'user_id' => Auth::id(),
				'vehicle_id' => $request->vehicle_id,
				'reg_no' => $request->reg_no,
				'kms_outgoing' => $request->kms_outgoing,
				'kms_incoming' => $request->kms_incoming,
				'fuel_level_out' => $request->fuel_level_out,
				'fuel_level_in' => $request->fuel_level_in,
				'datetime_outgoing' => date('Y-m-d H:i:s', strtotime($request->datetime_outgoing)),
				'datetime_incoming' => date('Y-m-d H:i:s', strtotime($request->datetime_incoming)),
				'petrol_card' => serialize($petrol_card),
				'lights' => serialize($lights),
				'invertor' => serialize($invertor),
				'car_mats' => serialize($car_mats),
				'int_damage' => serialize($int_damage),
				'int_lights' => serialize($int_lights),
				'ext_car' => serialize($ext_car),
				'tyre' => serialize($tyre),
				'ladder' => serialize($ladder),
				'leed' => serialize($leed),
				'power_tool' => serialize($power_tool),
				'ac' => serialize($ac),
				'head_light' => serialize($head_light),
				'lock' => serialize($lock),
				'windows' => serialize($windows),
				'condition' => serialize($condition),
				'oil_chk' => serialize($oil_chk),
				'suspension' => serialize($suspension),
				'tool_box' => serialize($tool_box),
			]);
			$data['success'] = "1";
			$data['message'] = "Vehicle inspection updated successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function store(Request $request) {
		// dd($request->all());
		$validation = Validator::make($request->all(), [
			'vehicle_id' => 'required|integer',
			'reg_no' => 'required',
			'kms_outgoing' => 'required|numeric',
			'kms_incoming' => 'required|numeric',
			'datetime_outgoing' => 'required',
			'datetime_incoming' => 'required',
			'fuel_level_out' => 'required|integer',
			'fuel_level_in' => 'required|integer',
			// 'array' => 'required|array',

		]);
		$errors = $validation->errors();

		if (count($errors) > 0) {
			$data['success'] = "0";
			$data['message'] = implode(", ", $errors->all());
			$data['data'] = "";
		} else {

			// test is not done with array change
			// $all_variables = $request->array;
			// foreach ($all_variables as $var) {
			//     $$var['label'] = array('flag' => ($var['state']) ? 1 : 0, 'text' => $var['details']);
			//     dd($int_damage);
			// }
			// dd($petrol_card, $lights, $invertor, $car_mats); //testing dynamic array

			// all separate data in request parameter
			$petrol_card = array('flag' => $request->get('petrol_card'), 'text' => $request->get('petrol_card_text'));
			$lights = array('flag' => $request->get('lights'), 'text' => $request->get('lights_text'));
			$invertor = array('flag' => $request->get('invertor'), 'text' => $request->get('invertor_text'));
			$car_mats = array('flag' => $request->get('car_mats'), 'text' => $request->get('car_mats_text'));
			$int_damage = array('flag' => $request->get('int_damage'), 'text' => $request->get('int_damage_text'));
			$int_lights = array('flag' => $request->get('int_lights'), 'text' => $request->get('int_lights_text'));
			$ext_car = array('flag' => $request->get('ext_car'), 'text' => $request->get('ext_car_text'));
			$tyre = array('flag' => $request->get('tyre'), 'text' => $request->get('tyre_text'));
			$ladder = array('flag' => $request->get('ladder'), 'text' => $request->get('ladder_text'));
			$leed = array('flag' => $request->get('leed'), 'text' => $request->get('leed_text'));
			$power_tool = array('flag' => $request->get('power_tool'), 'text' => $request->get('power_tool_text'));
			$ac = array('flag' => $request->get('ac'), 'text' => $request->get('ac_text'));
			$head_light = array('flag' => $request->get('head_light'), 'text' => $request->get('head_light_text'));
			$lock = array('flag' => $request->get('lock'), 'text' => $request->get('lock_text'));
			$windows = array('flag' => $request->get('windows'), 'text' => $request->get('windows_text'));
			$condition = array('flag' => $request->get('condition'), 'text' => $request->get('condition_text'));
			$oil_chk = array('flag' => $request->get('oil_chk'), 'text' => $request->get('oil_chk_text'));
			$suspension = array('flag' => $request->get('suspension'), 'text' => $request->get('suspension_text'));
			$tool_box = array('flag' => $request->get('tool_box'), 'text' => $request->get('tool_box_text'));

			VehicleReviewModel::create([
				'user_id' => Auth::id(),
				'vehicle_id' => $request->vehicle_id,
				'reg_no' => $request->reg_no,
				'kms_outgoing' => $request->kms_outgoing,
				'kms_incoming' => $request->kms_incoming,
				'fuel_level_out' => $request->fuel_level_out,
				'fuel_level_in' => $request->fuel_level_in,
				'datetime_outgoing' => date('Y-m-d H:i:s', strtotime($request->datetime_outgoing)),
				'datetime_incoming' => date('Y-m-d H:i:s', strtotime($request->datetime_incoming)),
				'petrol_card' => serialize($petrol_card),
				'lights' => serialize($lights),
				'invertor' => serialize($invertor),
				'car_mats' => serialize($car_mats),
				'int_damage' => serialize($int_damage),
				'int_lights' => serialize($int_lights),
				'ext_car' => serialize($ext_car),
				'tyre' => serialize($tyre),
				'ladder' => serialize($ladder),
				'leed' => serialize($leed),
				'power_tool' => serialize($power_tool),
				'ac' => serialize($ac),
				'head_light' => serialize($head_light),
				'lock' => serialize($lock),
				'windows' => serialize($windows),
				'condition' => serialize($condition),
				'oil_chk' => serialize($oil_chk),
				'suspension' => serialize($suspension),
				'tool_box' => serialize($tool_box),
			]);
			$data['success'] = "1";
			$data['message'] = "Vehicle inspection added successfully!";
			$data['data'] = "";
		}
		return $data;
	}

	public function inspections() {
		$details = array();
		$records = VehicleReviewModel::orderBy('id', 'desc')->get();
		foreach ($records as $row) {
			$petrol_card = unserialize($row->petrol_card);
			$lights_indicators = unserialize($row->lights);
			$invertor_cigarette = unserialize($row->invertor);
			$car_mat_seat_cover = unserialize($row->car_mats);
			$interior_damage = unserialize($row->int_damage);
			$interior_lights = unserialize($row->int_lights);
			$exterior_damage = unserialize($row->ext_car);
			$tyres = unserialize($row->tyre);
			$extension_ladder = unserialize($row->ladder);
			$extension_leed = unserialize($row->leed);
			$power_tools = unserialize($row->power_tool);
			$ac = unserialize($row->ac);
			$lights_headlights = unserialize($row->head_light);
			$lock_alarm = unserialize($row->lock);
			$windows = unserialize($row->windows);
			$condition = unserialize($row->condition);
			$oil_chk = unserialize($row->oil_chk);
			$suspension = unserialize($row->suspension);
			$tool_box = unserialize($row->tool_box);
			$array = array(
				array(
					'label' => 'petrol_card',
					'state' => ($petrol_card['flag']) ? 1 : 0,
					'details' => $petrol_card['text'],
				),

				array(
					'label' => 'lights',
					'state' => ($lights_indicators['flag']) ? 1 : 0,
					'details' => $lights_indicators['text'],
				),

				array(
					'label' => 'invertor',
					'state' => ($invertor_cigarette['flag']) ? 1 : 0,
					'details' => $invertor_cigarette['text'],
				),

				array(
					'label' => 'car_mats',
					'state' => ($car_mat_seat_cover['flag']) ? 1 : 0,
					'details' => $car_mat_seat_cover['text'],
				),

				array(
					'label' => 'int_damage',
					'state' => ($interior_damage['flag']) ? 1 : 0,
					'details' => $interior_damage['text'],
				),

				array(
					'label' => 'int_lights',
					'state' => ($interior_lights['flag']) ? 1 : 0,
					'details' => $interior_lights['text'],
				),

				array(
					'label' => 'ext_car',
					'state' => ($exterior_damage['flag']) ? 1 : 0,
					'details' => $exterior_damage['text'],
				),

				array(
					'label' => 'tyre',
					'state' => ($tyres['flag']) ? 1 : 0,
					'details' => $tyres['text'],
				),

				array(
					'label' => 'ladder',
					'state' => ($extension_ladder['flag']) ? 1 : 0,
					'details' => $extension_ladder['text'],
				),

				array(
					'label' => 'leed',
					'state' => ($extension_leed['flag']) ? 1 : 0,
					'details' => $extension_leed['text'],
				),

				array(
					'label' => 'power_tool',
					'state' => ($power_tools['flag']) ? 1 : 0,
					'details' => $power_tools['text'],
				),

				array(
					'label' => 'ac',
					'state' => ($ac['flag']) ? 1 : 0,
					'details' => $ac['text'],
				),

				array(
					'label' => 'head_light',
					'state' => ($lights_headlights['flag']) ? 1 : 0,
					'details' => $lights_headlights['text'],
				),

				array(
					'label' => 'lock',
					'state' => ($lock_alarm['flag']) ? 1 : 0,
					'details' => $lock_alarm['text'],
				),

				array(
					'label' => 'windows',
					'state' => ($windows['flag']) ? 1 : 0,
					'details' => $windows['text'],
				),

				array(
					'label' => 'condition',
					'state' => ($condition['flag']) ? 1 : 0,
					'details' => $condition['text'],
				),

				array(
					'label' => 'oil_chk',
					'state' => ($oil_chk['flag']) ? 1 : 0,
					'details' => $oil_chk['text'],
				),

				array(
					'label' => 'suspension',
					'state' => ($suspension['flag']) ? 1 : 0,
					'details' => $suspension['text'],
				),

				array(
					'label' => 'tool_box',
					'state' => ($tool_box['flag']) ? 1 : 0,
					'details' => $tool_box['text'],
				),
			);

			$details[] = array(
				'id' => $row->id,
				'vehicle_id' => $row->vehicle_id,
				'user_id' => $row->user_id,
				'vehicle' => $row->vehicle->make_name . " - " . $row->vehicle->model_name . " - " . $row->vehicle->license_plate,
				'reg_no' => $row->reg_no,
				'review_by' => $row->user->name,

				'kms_outgoing' => $row->kms_outgoing,
				'kms_incoming' => $row->kms_incoming,
				'fuel_level_out' => $row->fuel_level_out,
				'fuel_level_in' => $row->fuel_level_in,
				'datetime_outgoing' => $row->datetime_outgoing,
				'datetime_incoming' => $row->datetime_incoming,
/*
'petrol_card' => array(
'flag' => ($petrol_card['flag']) ? 1 : 0,
'text' => $petrol_card['text'],
),

'lights_indicators' => array(
'flag' => ($lights_indicators['flag']) ? 1 : 0,
'text' => $lights_indicators['text'],
),

'invertor_cigarette' => array(
'flag' => ($invertor_cigarette['flag']) ? 1 : 0,
'text' => $invertor_cigarette['text'],
),

'car_mat_seat_cover' => array(
'flag' => ($car_mat_seat_cover['flag']) ? 1 : 0,
'text' => $car_mat_seat_cover['text'],
),

'interior_damage' => array(
'flag' => ($interior_damage['flag']) ? 1 : 0,
'text' => $interior_damage['text'],
),

'interior_lights' => array(
'flag' => ($interior_lights['flag']) ? 1 : 0,
'text' => $interior_lights['text'],
),

'exterior_damage' => array(
'flag' => ($exterior_damage['flag']) ? 1 : 0,
'text' => $exterior_damage['text'],
),

'tyres' => array(
'flag' => ($tyres['flag']) ? 1 : 0,
'text' => $tyres['text'],
),

'extension_ladder' => array(
'flag' => ($extension_ladder['flag']) ? 1 : 0,
'text' => $extension_ladder['text'],
),

'extension_leed' => array(
'flag' => ($extension_leed['flag']) ? 1 : 0,
'text' => $extension_leed['text'],
),

'power_tools' => array(
'flag' => ($power_tools['flag']) ? 1 : 0,
'text' => $power_tools['text'],
),

'ac' => array(
'flag' => ($ac['flag']) ? 1 : 0,
'text' => $ac['text'],
),

'lights_headlights' => array(
'flag' => ($lights_headlights['flag']) ? 1 : 0,
'text' => $lights_headlights['text'],
),

'lock_alarm' => array(
'flag' => ($lock_alarm['flag']) ? 1 : 0,
'text' => $lock_alarm['text'],
),

'windows' => array(
'flag' => ($windows['flag']) ? 1 : 0,
'text' => $windows['text'],
),

'condition' => array(
'flag' => ($condition['flag']) ? 1 : 0,
'text' => $condition['text'],
),

'oil_chk' => array(
'flag' => ($oil_chk['flag']) ? 1 : 0,
'text' => $oil_chk['text'],
),

'suspension' => array(
'flag' => ($suspension['flag']) ? 1 : 0,
'text' => $suspension['text'],
),

'tool_box' => array(
'flag' => ($tool_box['flag']) ? 1 : 0,
'text' => $tool_box['text'],
),*/

				// 'array' => $array,

				'petrol_card' => ($petrol_card['flag']) ? 1 : 0,
				'petrol_card_text' => $petrol_card['text'],
				'lights' => ($lights_indicators['flag']) ? 1 : 0,
				'lights_text' => $lights_indicators['text'],
				'invertor' => ($invertor_cigarette['flag']) ? 1 : 0,
				'invertor_text' => $invertor_cigarette['text'],
				'car_mats' => ($car_mat_seat_cover['flag']) ? 1 : 0,
				'car_mats_text' => $car_mat_seat_cover['text'],
				'int_damage' => ($interior_damage['flag']) ? 1 : 0,
				'int_damage_text' => $interior_damage['text'],
				'int_lights' => ($interior_lights['flag']) ? 1 : 0,
				'int_lights_text' => $interior_lights['text'],
				'ext_car' => ($exterior_damage['flag']) ? 1 : 0,
				'ext_car_text' => $exterior_damage['text'],
				'tyre' => ($tyres['flag']) ? 1 : 0,
				'tyre_text' => $tyres['text'],
				'ladder' => ($extension_ladder['flag']) ? 1 : 0,
				'ladder_text' => $extension_ladder['text'],
				'leed' => ($extension_leed['flag']) ? 1 : 0,
				'leed_text' => $extension_leed['text'],
				'power_tool' => ($power_tools['flag']) ? 1 : 0,
				'power_tool_text' => $power_tools['text'],
				'ac' => ($ac['flag']) ? 1 : 0,
				'ac_text' => $ac['text'],
				'head_light' => ($lights_headlights['flag']) ? 1 : 0,
				'head_light_text' => $lights_headlights['text'],
				'lock' => ($lock_alarm['flag']) ? 1 : 0,
				'lock_text' => $lock_alarm['text'],
				'windows' => ($windows['flag']) ? 1 : 0,
				'windows_text' => $windows['text'],
				'condition' => ($condition['flag']) ? 1 : 0,
				'condition_text' => $condition['text'],
				'oil_chk' => ($oil_chk['flag']) ? 1 : 0,
				'oil_chk_text' => $oil_chk['text'],
				'suspension' => ($suspension['flag']) ? 1 : 0,
				'suspension_text' => $suspension['text'],
				'tool_box' => ($tool_box['flag']) ? 1 : 0,
				'tool_box_text' => $tool_box['text'],
			);
		}

		$data['success'] = "1";
		$data['message'] = "Data fetched!";
		$data['data'] = $details;
		return $data;
	}
}
