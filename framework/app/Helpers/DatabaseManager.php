<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Artisan;

class DatabaseManager {

	public function migrateAndSeed() {

		return $this->migrate();
	}

	private function migrate() {
		try {
			Artisan::call('migrate');

		} catch (Exception $e) {

			return $this->response($e->getMessage());

		}
		return $this->seed();

	}

	private function seed() {
		try {
			Artisan::call('db:seed');

		} catch (Exception $e) {
			return $this->response($e->getMessage());

		}

		return $this->response(trans('installer_messages.final.finished'), 'success');
	}

	private function response($message, $status = 'danger') {
		return array(
			'status' => $status,
			'message' => $message,
		);
	}

}
