<?php

use App\Model\ExpCats;
use App\Model\IncCats;
use Illuminate\Database\Seeder;

class SampleCategories extends Seeder {

	public function run() {
		$this->import_income_categories();
		$this->import_expense_categories();

	}

	private function import_expense_categories() {
		DB::table('expense_cat')->truncate();
		$data = array(
			array(
				'name' => 'Insurance',
				'user_id' => '1',
				'type' => 'd',
			),

			array(
				'name' => 'Patente',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Mechanics',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Car wash',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Vignette',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Maintenance',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Parking',
				'user_id' => '1',
				'type' => 'd',
			),
			array(
				'name' => 'Fuel',
				'user_id' => '1',
				'type' => 'd',

			),
			array(
				'name' => 'Car Services',
				'user_id' => '1',
				'type' => 'd',
			),

		);
		foreach ($data as $rec) {
			ExpCats::create($rec);
		}

	}
	private function import_income_categories() {
		DB::table('income_cat')->truncate();
		$data = array(
			array(
				'name' => 'Booking',
				'user_id' => '1',
				'type' => 'd',
			),
		);
		foreach ($data as $rec) {
			IncCats::create($rec);
		}

	}
}
