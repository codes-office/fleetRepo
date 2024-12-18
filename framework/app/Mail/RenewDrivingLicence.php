<?php

/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */
namespace App\Mail;

use Hyvikk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenewDrivingLicence extends Mailable {
	use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public $driver;
	public $lic_date;
	public $diff_in_days;
	public $user;
	public function __construct($driver, $lic_date, $diff_in_days, $user) {
		$this->driver = $driver;
		$this->lic_date = $lic_date;
		$this->diff_in_days = $diff_in_days;
		$this->user = $user;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->from(Hyvikk::get("email"))->subject('Renew Driving Licence')->markdown('emails.renew_driving_licence');
	}
}
