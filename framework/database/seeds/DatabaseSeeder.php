<?php
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

	public function run() {
		// factory(App\Model\Bookings::class, 2)->create();
		\App\Model\Bookings::factory()->count(2)->create();
		$this->call(DefaultAdmin::class);
		$this->call(SampleCategories::class);
		$this->call(SettingSeed::class);
		// factory(App\Model\WorkOrders::class, 2)->create()->each(function ($order) {
		// 	App\Model\WorkOrderLogs::create([
		// 		'created_on' => date('Y-m-d', strtotime($order->created_at)),
		// 		'vehicle_id' => $order->vehicle_id,
		// 		'vendor_id' => $order->vendor_id,
		// 		'required_by' => $order->required_by,
		// 		'status' => $order->status,
		// 		'description' => $order->description,
		// 		'meter' => $order->meter,
		// 		'note' => $order->note,
		// 		'price' => $order->price,
		// 		'type' => "Created",
		// 	]);
		// });

		\App\Model\WorkOrders::factory()->count(2)->create();

		App\Model\User::factory()->count(2)->create()->each(function ($user) {
			$faker = \Faker\Factory::create();
			$start_date = Carbon\Carbon::today()->toDateString();
			$date = strtotime(date('Y-m-d'));
			$newDate = date('Y-m-d', strtotime('+1 month', $date));

			$issue_date = Carbon\Carbon::today()->toDateString();
			$expDate = date('Y-m-d', strtotime('+2 month', $date));
			// $user = factory('App\Model\User')->create();
			$name = explode(" ", $user->name);
			$user->setMeta([

				'first_name' => $name[0],
				'last_name' => $name[1],
				'address' => $faker->address,
				'phone' => str_replace('+', '0', $faker->e164PhoneNumber),
				'issue_date' => $issue_date,
				'exp_date' => $expDate,
				'start_date' => $start_date,
				'end_date' => $newDate,
				// 'license_number' => '123',
				'license_number' => $faker->unique()->numberBetween($min = 100000, $max = 900000),
				'contract_number' => $faker->unique()->numberBetween($min = 1000, $max = 9000),
				'emp_id' => $faker->unique()->randomNumber,
			]);

			$user->save();
		});

		// $assign_driver = App\Model\User::find(6);
		// $assign_driver->vehicle_id = 1;
		// $assign_driver->save();
		$this->call(PermissionSeed::class);
	}
}
