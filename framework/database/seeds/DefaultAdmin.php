<?php
use App\Model\BookingIncome;
use App\Model\Bookings;
use App\Model\CompanyServicesModel;
use App\Model\DriverLogsModel;
use App\Model\DriverVehicleModel;
use App\Model\Expense;
use App\Model\FuelModel;
use App\Model\IncomeModel;
use App\Model\PartsCategoryModel;
use App\Model\ReasonsModel;
use App\Model\ServiceItemsModel;
use App\Model\TeamModel;
use App\Model\Testimonial;
use App\Model\User;
use App\Model\VehicleColor;
use App\Model\VehicleGroupModel;
use App\Model\VehicleMake;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use App\Model\Vehicle_Model;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DefaultAdmin extends Seeder
{
    public function run(Faker $faker)
    {
        DB::table('users')->truncate();
        DB::table('vehicles')->truncate();
        DB::table('expense')->truncate();
        DB::table('income')->truncate();
        DB::table('reasons')->truncate();
        DB::table('company_services')->truncate();
        DB::table('team')->truncate();
        DB::table('testimonials')->truncate();

        for ($i = 0; $i < 5; $i++) {
            TeamModel::create(['name' => $faker->name, 'details' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Temporibus neque est nemo et ipsum fugiat, ab facere adipisci. Aliquam quibusdam molestias quisquam distinctio? Culpa, voluptatem voluptates exercitationem sequi velit quaerat.', 'designation' => 'Owner']);
            Testimonial::create(['name' => $faker->name, 'details' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet animi doloribus, repudiandae iusto magnam soluta voluptates, expedita aspernatur consectetur! Ex fugit ducimus itaque, quibusdam nemo in animi quae libero repellendus!']);
        }

        CompanyServicesModel::create(['title' => 'Best price guranteed', 'image' => 'fleet-bestprice.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => '24/7 Customer care', 'image' => 'fleet-care.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => 'Home pickups', 'image' => 'fleet-homepickup.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => 'Easy Bookings', 'image' => 'fleet-easybooking.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);

        ReasonsModel::create(['reason' => 'No fuel']);
        ReasonsModel::create(['reason' => 'Tire punctured']);

       

        VehicleTypeModel::create([
            'vehicletype' => 'Hatchback',
            'displayname' => 'Hatchback',
            'isenable' => 1,
            'seats' => 4,
        ]);
        VehicleTypeModel::create(['vehicletype' => 'Sedan', 'displayname' => 'Sedan', 'isenable' => 1,
            'seats' => 4]);
        VehicleTypeModel::create(['vehicletype' => 'Mini van', 'displayname' => 'Mini van', 'isenable' => 1,
            'seats' => 7,
        ]);
        VehicleTypeModel::create(['vehicletype' => 'Saloon', 'displayname' => 'Saloon', 'isenable' => 1,
            'seats' => 4,
        ]);
        VehicleTypeModel::create(['vehicletype' => 'SUV', 'displayname' => 'SUV', 'isenable' => 1,
            'seats' => 4,
        ]);
        VehicleTypeModel::create(['vehicletype' => 'Bus', 'displayname' => 'Bus', 'isenable' => 1,
            'seats' => 40,
        ]);
        VehicleTypeModel::create(['vehicletype' => 'Truck', 'displayname' => 'Truck', 'isenable' => 1,
            'seats' => 3,
        ]);

        $modules = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15);

        $admin = User::create([
            'name' => "Super Administrator",
            'email' => "master@admin.com",
            'password' => bcrypt('password'),
            'api_token' => str_random(60),
            'user_type' => "S",
            'user_id' => 1,
        ]);
        $admin->setMeta(['profile_image' => 'no-user.jpg', 'module' => serialize($modules)]);
        $admin->save();
        // $accessToken = $admin->createToken('authToken')->accessToken;
        $id = User::create([
            "name" => "User One",
            "email" => "user1@admin.com",
            "password" => bcrypt("password"),
            'api_token' => str_random(60),
            "user_type" => "O",
            "group_id" => 1,
            'user_id' => 1,
        ])->id;
        $user1 = User::find($id);
        $user1->setMeta(['module' => serialize($modules)]);
        $user1->save();
        $id2 = User::create([
            "name" => "User Two",
            "email" => "user2@admin.com",
            "password" => bcrypt("password"),
            'api_token' => str_random(60),
            "user_type" => "O",
            "group_id" => 1,
            'user_id' => 1,
        ])->id;
        $user2 = User::find($id2);
        $user2->setMeta(['module' => serialize($modules)]);
        $user2->save();

        $c1 = User::create([
            "name" => "Customer One",
            "email" => "customer1@gmail.com",
            "password" => bcrypt("password"),
            'api_token' => str_random(60),
            "user_type" => "C",
            'user_id' => 1]);

        $c2 = User::create([
            "name" => "Customer Two",
            "email" => "customer2@gmail.com",
            "password" => bcrypt("password"),
            'api_token' => str_random(60),
            "user_type" => "C",
            'user_id' => 1]);
        $name1 = explode(" ", $c1->name);
        $name2 = explode(" ", $c2->name);
        $customer1 = User::find($c1->id);
        $customer1->setMeta(["first_name" => $name1[0],
            "last_name" => $name1[1],
            "address" => "728 Evalyn Knolls Apt. 119 Lake Jaydenville, MD 74979-3406",
            "mobno" => "8639379915669",
            'gender' => 0,
        ]);
        $customer1->save();
        $customer2 = User::find($c2->id);
        $customer2->setMeta(["first_name" => $name2[0],
            "last_name" => $name2[1],
            "address" => "91158 Luigi Cliffs Lake Darby, MA 39627-1727",
            "mobno" => "9773607007903",
            'gender' => 1,
        ]);
        $customer2->save();

        VehicleGroupModel::create([
            'name' => 'Default',
            'description' => 'Default vehicle group',
            'note' => 'Default vehicle group',
            'user_id' => 1,
        ]);

        PartsCategoryModel::create(['name' => 'Engine Parts', 'user_id' => 1]);
        PartsCategoryModel::create(['name' => 'Electricals', 'user_id' => 1]);

        $vehicle1 = [

            'make_name' => 'Tata',
            'model_name' => 'Punch',
            'color_name' => 'Red',
            'type_id' => '3',
            'year' => '2015',
            'engine_type' => 'Petrol',
            'horse_power' => '190',
            'vin' => '2342342',
            'license_plate' => '9191bh',
            'mileage' => '45464',
            'lic_exp_date' => date('Y-m-d', strtotime(' 250 day')),
            'reg_exp_date' => date('Y-m-d', strtotime(' 150 day')),
            'in_service' => '1',
            'user_id' => '1',
            'int_mileage' => 50,
            "group_id" => 1,
            "vehicle_image" => "car1.jpeg",

        ];

        $vehicle2 = [
            'make_name' => 'Maruti',
            'model_name' => 'Suzuki',
            'color_name' => 'Black',
            'type_id' => 3,
            'year' => '2012',
            'engine_type' => 'Petrol',
            'horse_power' => '150',
            'vin' => '124578',
            'license_plate' => '1245ab',
            'mileage' => '45464',
            'lic_exp_date' => date('Y-m-d', strtotime(' 365 day')),
            'reg_exp_date' => date('Y-m-d', strtotime(' 90 day')),
            'in_service' => '1',
            'user_id' => '1',
            'int_mileage' => 40,
            "group_id" => 1,
            "vehicle_image" => "car2.jpeg",

        ];

        $v = VehicleModel::create($vehicle1);
        $v2 = VehicleModel::create($vehicle2);

        $vehicle_meta1 = VehicleModel::find($v->id);
        $vehicle_meta1->setMeta([
            'driver_id' => 6,
            'average' => 35.45,
            'ins_number' => '70651',
            'ins_exp_date' => date('Y-m-d', strtotime(' 190 day'))]);
        $vehicle_meta1->save();

        $vehicle_meta2 = VehicleModel::find($v2->id);
        $vehicle_meta2->setMeta([
            'average' => 42.5,
            'ins_number' => '36945',
            'ins_exp_date' => date('Y-m-d', strtotime(' 190 day'))]);
        $vehicle_meta2->save();

        Expense::create([
            'vehicle_id' => $v->id,
            'amount' => mt_rand(1000, 5000),
            'user_id' => $id,
            'expense_type' => 1,
            'comment' => 'Sample Comment',
            'date' => date('Y-m-d', strtotime(' -1 day')),

        ]);

        IncomeModel::create([
            'vehicle_id' => $v->id,
            'amount' => mt_rand(1000, 5000),
            'user_id' => $id,
            'income_cat' => 1,
            'date' => date('Y-m-d', strtotime(' -5 day')),
            'tax_percent' => 0,
            'tax_charge_rs' => 0,
        ]);

        Expense::create([
            'vehicle_id' => $v2->id,
            'amount' => mt_rand(1000, 5000),
            'user_id' => $id2,
            'expense_type' => 4,
            'comment' => 'Sample Comment',
            'date' => date('Y-m-d', strtotime(' -5 day')),

        ]);

        IncomeModel::create([
            'vehicle_id' => $v2->id,
            'amount' => mt_rand(1000, 5000),
            'user_id' => $id2,
            'income_cat' => 1,
            'date' => date('Y-m-d', strtotime(' -1 day')),
            'tax_percent' => 0,
            'tax_charge_rs' => 0,
        ]);

        // completed booking
        $income_id = IncomeModel::create([
            'vehicle_id' => 1,
            'amount' => 500,
            'user_id' => 1,
            'income_cat' => 1,
            'mileage' => 10,
            'date' => date('Y-m-d'),
            'income_id' => 1,
            'tax_percent' => 0,
            'tax_charge_rs' => 0,
        ])->id;

        BookingIncome::create(['booking_id' => 1, "income_id" => $income_id]);
        $new_book = Bookings::find(1);
        $new_book->status = 1;
        $new_book->payment = 1;
        $new_book->tax_total = 500;
        $new_book->total_tax_percent = 0;
        $new_book->total_tax_charge_rs = 0;
        $new_book->ride_status = "Completed";
        $new_book->journey_date = date('d-m-Y', strtotime($new_book->pickup));
        $new_book->journey_time = date('H:i:s', strtotime($new_book->pickup));
        $new_book->setMeta([
            'customerId' => 4,
            'vehicleId' => 1,
            'day' => 1,
            'mileage' => 10,
            'waiting_time' => 0,
            'date' => date('Y-m-d'),
            'total' => 500,
            'receipt' => 1,

        ]);
        $new_book->save();
        // completed booking
        $ride = Bookings::find(2);
        $ride->ride_status = 'Upcoming';
        $ride->journey_date = date('d-m-Y', strtotime($ride->pickup));
        $ride->journey_time = date('H:i:s', strtotime($ride->pickup));
        $ride->save();

        $fuel1 = [
            'vehicle_id' => $v->id,
            'user_id' => $id,
            'start_meter' => 1000,
            'end_meter' => 2000,
            'note' => 'sample note',
            'qty' => 10,
            'fuel_from' => 'Fuel Tank',
            'cost_per_unit' => 50,
            'consumption' => 100,
            'date' => date('Y-m-d', strtotime(' -2 day')),
            'province' => 'Gujarat',
        ];

        $fuel2 = [
            'vehicle_id' => $v->id,
            'user_id' => $id,
            'start_meter' => 2000,
            'end_meter' => 0,
            'note' => 'sample note',
            'qty' => 10,
            'fuel_from' => 'Fuel Tank',
            'cost_per_unit' => 50,
            'consumption' => 0,
            'date' => date('Y-m-d', strtotime(' +10 day')),
            'province' => 'Gujarat',
        ];

        $exp1 = FuelModel::create($fuel1);
        $exp2 = FuelModel::create($fuel2);

        Expense::create([
            'vehicle_id' => $exp1->vehicle_id,
            'amount' => $exp1->qty * $exp1->cost_per_unit,
            'user_id' => $exp1->user_id,
            'expense_type' => 8,
            'comment' => 'Sample Comment',
            'date' => $exp1->date,
            'exp_id' => $exp1->id,

        ]);

        Expense::create([
            'vehicle_id' => $exp2->vehicle_id,
            'amount' => $exp2->qty * $exp2->cost_per_unit,
            'user_id' => $exp2->user_id,
            'expense_type' => 8,
            'comment' => 'Sample Comment',
            'date' => $exp2->date,
            'exp_id' => $exp2->id,

        ]);

        DriverVehicleModel::create(['driver_id' => 6,
            'vehicle_id' => 1,
        ]);

        DriverLogsModel::create(['driver_id' => 6, 'vehicle_id' => 1, 'date' => date('Y-m-d H:i:s')]);

        ServiceItemsModel::create(['description' => 'Change oil',
            'time_interval' => 'on',
            'overdue_time' => 60,
            'overdue_unit' => 'day(s)',
            'show_time' => 'on',
            'duesoon_time' => 2,
            'duesoon_unit' => 'day(s)',
            'user_id' => 1,
        ]);

        // $part = PartsModel::create([
        //     'barcode' => mt_rand(1111, 9999),
        //     'number' => mt_rand(1111, 9999),
        //     'description' => 'Wiper blades',
        //     'unit_cost' => mt_rand(1000, 5000),
        //     'vendor_id' => 1,
        //     'note' => 'sample part',
        //     'stock' => mt_rand(10, 20),
        // ]);

        // PartStock::create([
        //     'part_id' => $part->id,
        //     'price_eur' => mt_rand(100, 500),
        //     'price_local' => mt_rand(100, 500),
        //     'transport' => mt_rand(100, 500),
        //     'customs' => mt_rand(100, 500),
        //     'volume' => mt_rand(10, 30),
        //     'user_id' => $admin->id,
        // ]);

        Artisan::call('passport:install');
    }
}
