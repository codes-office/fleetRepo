<?php
/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Mail;

use App\Model\BookingIncome;
use App\Model\Bookings;
use Hyvikk;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerInvoice extends Mailable {
	use SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public $booking;
	public $invoice_id;
	public function __construct(Bookings $booking) {
		$this->booking = $booking;
		$this->invoice_id = BookingIncome::where('booking_id', $booking->id)->latest()->first();
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->from(Hyvikk::get("email"))->subject('Generate Invoice. Invoice ID: ' . $this->invoice_id->income_id)->markdown('emails.customer_invoice');
	}
}
