<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class PaymentSettignsRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return (Auth::user()->user_type == 'S' || Auth::user()->user_type == 'O');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		// dd($this->request->currency_code);
		$stripe = 'nullable';
		$razorpay = 'nullable';
		if (\Request::get('method')) {
			if (in_array("stripe", \Request::get('method'))) {
				$stripe = 'required';
			}

			if (in_array("razorpay", \Request::get('method'))) {
				$razorpay = 'required';
			}
		}

		return [
			'method' => 'required',
			'stripe_publishable_key' => $stripe,
			'stripe_secret_key' => $stripe,
			'razorpay_key' => $razorpay,
			'razorpay_secret' => $razorpay,
		];
	}

	public function messages() {
		return [
			'method.required' => 'You must have to select atleast one payment method.',
			'stripe_publishable_key.required' => 'You must have to provide Stripe Publishable Key while choosing Stripe Payment method.',
			'stripe_secret_key.required' => 'You must have to provide Stripe Secret Key while choosing Stripe Payment method.',
			'razorpay_key.required' => 'You must have to provide RazorPay Key while choosing Stripe Payment method.',
			'razorpay_secret.required' => 'You must have to provide RazorPay Secret while choosing Stripe Payment method.',
		];
	}
}
