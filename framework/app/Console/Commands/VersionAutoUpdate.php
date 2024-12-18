<?php

namespace App\Console\Commands;

use App\Helpers\InstalledFileManager;
use App\Model\ApiSettings;
use App\Model\Bookings;
use App\Model\CompanyServicesModel;
use App\Model\EmailContent;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\Hyvikk;
use App\Model\Settings;
use App\Model\TwilioSettings;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use DB;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class VersionAutoUpdate extends Command
{

    protected $signature = 'update:version';

    protected $description = 'Automatic update fleet manager to latest version';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(InstalledFileManager $fileManager)
    {

        // count number of tables in database
        $tables = DB::select('SHOW TABLES');

        // dd($tables[0]->total); //total number of tables
        if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version3") {

            // existing database of fleet3 having tables | update fleet3 new features with old database records remains same

            $this->updateVersion3to4();
            $fileManager->update();

        }
        if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4") {

            // existing database of fleet4 having tables | update fleet4 => fleet4.0.1 with old database records remains same

            $this->updateVersion4to401();
            $fileManager->update();

        }

        if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.1") {

            // existing database of fleet4.0.1 having tables | update fleet4.0.1 => fleet4.0.2 with old database records remains same

            $this->updateVersion401to402();
            $fileManager->update();

        }

        if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.2") {

            // existing database of fleet4.0.2 having tables | update fleet4.0.2 => fleet4.0.3 with old database records remains same

            $this->updateVersion402to403();
            $fileManager->update();

        }
        if (sizeof($tables) > 0 && file_exists(storage_path('installed')) && file_get_contents(storage_path('installed')) == "version4.0.3") {
            // existing database of fleet4 having tables | update fleet4.0.3 => fleet5.0 with old database records remains same

            $this->updateVersion403to50();
            $fileManager->update();
        }

    }

    public function updateVersion403to50()
    {
        Artisan::call('backup:run');
        Artisan::call('migrate');
        TwilioSettings::create(['name' => 'sid', 'value' => '']);
        TwilioSettings::create(['name' => 'token', 'value' => '']);
        TwilioSettings::create(['name' => 'from', 'value' => '']);
        TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
        TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
    }

    public function updateVersion402to403()
    {
        Artisan::call('backup:run');
        Settings::updateOrCreate(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
    }

    public function updateVersion401to402()
    {
        Artisan::call('backup:run');
        Artisan::call('migrate');
        $this->tax_charge();
        $this->payment_settings();
        // version5
        TwilioSettings::create(['name' => 'sid', 'value' => '']);
        TwilioSettings::create(['name' => 'token', 'value' => '']);
        TwilioSettings::create(['name' => 'from', 'value' => '']);
        TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
        TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
    }

    public function updateVersion3to4()
    {
        Artisan::call('backup:run');
        Artisan::call('migrate');
        $this->api_change();
        $fare_change = FareSettings::where('key_name', 'base_fare')->first();
        if ($fare_change != null) {
            $this->vehicle_types();
        }
        $this->move_folders();
        $this->frontend_settings();
        $this->booking_receipts();
        $this->tax_charge();
        $this->payment_settings();
        // version5
        TwilioSettings::create(['name' => 'sid', 'value' => '']);
        TwilioSettings::create(['name' => 'token', 'value' => '']);
        TwilioSettings::create(['name' => 'from', 'value' => '']);
        TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
        TwilioSettings::create(['name' => 'driver_message', 'value' => '']);

    }

    public function updateVersion4to401()
    {
        Artisan::call('backup:run');
        Artisan::call('migrate');
        $this->move_folders();
        $this->frontend_settings();
        $this->booking_receipts();
        $this->tax_charge();
        $this->payment_settings();
        // version5
        TwilioSettings::create(['name' => 'sid', 'value' => '']);
        TwilioSettings::create(['name' => 'token', 'value' => '']);
        TwilioSettings::create(['name' => 'from', 'value' => '']);
        TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
        TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
    }

    public function payment_settings()
    {
        PaymentSettings::updateOrCreate(['name' => 'method', 'value' => json_encode(["cash"])]);
        PaymentSettings::updateOrCreate(['name' => 'currency_code', 'value' => 'INR']);
        PaymentSettings::updateOrCreate(['name' => 'stripe_publishable_key', 'value' => null]);
        PaymentSettings::updateOrCreate(['name' => 'stripe_secret_key', 'value' => null]);
        PaymentSettings::updateOrCreate(['name' => 'razorpay_key', 'value' => null]);
        PaymentSettings::updateOrCreate(['name' => 'razorpay_secret', 'value' => null]);

        FrontendModel::updateOrCreate(['key_name' => 'language', 'key_value' => 'en']);
    }

    public function tax_charge()
    {
        Settings::updateOrCreate(['label' => 'Tax Charge', 'name' => 'tax_charge', 'value' => 'null']);
        Settings::updateOrCreate(['label' => 'Fuel Unit', 'name' => 'fuel_unit', 'value' => 'gallon']);

        // add 0% tax charge to existing bookings
        $bookings = Bookings::get();
        foreach ($bookings as $booking) {
            if ($booking->total) {
                $booking->tax_total = $booking->total;
                $booking->total_tax_percent = 0;
                $booking->total_tax_charge_rs = 0;
                $booking->save();
            }
        }
    }

    public function booking_receipts()
    {
        $bookings = Bookings::where('status', 1)->get();
        foreach ($bookings as $booking) {
            $booking->receipt = 1;
            $booking->save();
        }
    }

    public function move_folders()
    {
        // move all images of img,files folder to assets/images folder and delete img,files folder
        $files = Storage::disk('public_files2')->files();
        $imgs = Storage::disk('public_img2')->files();

        foreach ($files as $file) {
            if (file_exists("../files/" . $file)) {
                \File::copy("../files/" . $file, "../assets/images/" . $file);
            }
        }
        foreach ($imgs as $img) {
            if (file_exists("../img/" . $img)) {
                \File::copy("../img/" . $img, "../assets/images/" . $img);
            }
        }

        if (file_exists('../img')) {
            File::deleteDirectory('../img');
        }
        if (file_exists('../files')) {
            File::deleteDirectory('../files');
        }
    }

    public function frontend_settings()
    {

        // add frontend settings
        FrontendModel::updateOrCreate(['key_name' => 'about_us'], ['key_name' => 'about_us', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
        FrontendModel::updateOrCreate(['key_name' => 'contact_email'], ['key_name' => 'contact_email', 'key_value' => 'master@admin.com']);
        FrontendModel::updateOrCreate(['key_name' => 'contact_phone'], ['key_name' => 'contact_phone', 'key_value' => '0123456789']);
        FrontendModel::updateOrCreate(['key_name' => 'customer_support'], ['key_name' => 'customer_support', 'key_value' => '0999988888']);
        FrontendModel::updateOrCreate(['key_name' => 'about_description'], ['key_name' => 'about_description', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
        FrontendModel::updateOrCreate(['key_name' => 'about_title'], ['key_name' => 'about_title', 'key_value' => 'Proudly serving you']);
        FrontendModel::updateOrCreate(['key_name' => 'facebook'], ['key_name' => 'facebook', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'twitter'], ['key_name' => 'twitter', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'instagram'], ['key_name' => 'instagram', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'linkedin'], ['key_name' => 'linkedin', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'faq_link'], ['key_name' => 'faq_link', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'cities', 'key_value' => 5]);
        FrontendModel::updateOrCreate(['key_name' => 'vehicles', 'key_value' => 10]);
        FrontendModel::updateOrCreate(['key_name' => 'cancellation', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'terms', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'privacy_policy', 'key_value' => null]);
        FrontendModel::updateOrCreate(['key_name' => 'enable', 'key_value' => 1]);
        FrontendModel::updateOrCreate(['key_name' => 'admin_approval', 'key_value' => 0]);
        
        // add default service item time interval
        Settings::updateOrCreate(['label' => 'Time Interval', 'name' => 'time_interval', 'value' => 30]);

        // company services for frontend
        CompanyServicesModel::create(['title' => 'Best price guranteed', 'image' => 'fleet-bestprice.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => '24/7 Customer care', 'image' => 'fleet-care.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => 'Home pickups', 'image' => 'fleet-homepickup.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
        CompanyServicesModel::create(['title' => 'Easy Bookings', 'image' => 'fleet-easybooking.png', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.Neque at, nobis repudiandae dolores.']);
    }

    public function api_change()
    {

        // enable/disable google api to use google places api
        ApiSettings::updateOrCreate(['key_name' => 'google_api'], ['key_name' => 'google_api', 'key_value' => '1']);

        // enable email to send email
        EmailContent::updateOrCreate(['key' => 'email'], ['key' => 'email', 'value' => 0]);
    }

    public function vehicle_types()
    {
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Hatchback'], ['vehicletype' => 'Hatchback',
            'displayname' => 'Hatchback',
            'isenable' => 1,
            'seats' => 4]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Sedan'], ['vehicletype' => 'Sedan', 'displayname' => 'Sedan', 'isenable' => 1,
            'seats' => 4]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Mini van'], ['vehicletype' => 'Mini van', 'displayname' => 'Mini van', 'isenable' => 1,
            'seats' => 7]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Saloon'], ['vehicletype' => 'Saloon', 'displayname' => 'Saloon', 'isenable' => 1,
            'seats' => 4]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'SUV'], ['vehicletype' => 'SUV', 'displayname' => 'SUV', 'isenable' => 1,
            'seats' => 4]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Bus'], ['vehicletype' => 'Bus', 'displayname' => 'Bus', 'isenable' => 1,
            'seats' => 40]);
        VehicleTypeModel::updateOrCreate(['vehicletype' => 'Truck'], ['vehicletype' => 'Truck', 'displayname' => 'Truck', 'isenable' => 1,
            'seats' => 3]);

        $vehicles = VehicleModel::get();
        foreach ($vehicles as $key) {
            $type[] = $key->type;
        }
        $get_types = array_unique($type);

        $vehicle_types = VehicleTypeModel::get();
        foreach ($vehicle_types as $key) {
            $all_types[] = $key->vehicletype;

            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_fare', 'key_value' => Hyvikk::fare('base_fare'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_km', 'key_value' => Hyvikk::fare('base_km'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_time', 'key_value' => Hyvikk::fare('base_time'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_std_fare', 'key_value' => Hyvikk::fare('std_fare'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_fare', 'key_value' => Hyvikk::fare('weekend_base_fare'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_km', 'key_value' => Hyvikk::fare('weekend_base_km'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_wait_time', 'key_value' => Hyvikk::fare('weekend_wait_time'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_std_fare', 'key_value' => Hyvikk::fare('weekend_std_fare'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_fare', 'key_value' => Hyvikk::fare('night_base_fare'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_km', 'key_value' => Hyvikk::fare('night_base_km'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_wait_time', 'key_value' => Hyvikk::fare('night_wait_time'), 'type_id' => $key->id]);
            FareSettings::updateOrCreate(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_std_fare', 'key_value' => Hyvikk::fare('night_std_fare'), 'type_id' => $key->id]);

        }
        foreach ($get_types as $key) {
            if (!in_array($key, $all_types)) {
                $new = VehicleTypeModel::updateOrCreate(['vehicletype' => $key], ['vehicletype' => $key, 'displayname' => $key, 'isenable' => 1,
                    'seats' => 4]);

                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_km', 'key_value' => '10', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_base_time', 'key_value' => '2', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_base_km', 'key_value' => '10', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_weekend_std_fare', 'key_value' => '20', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_fare', 'key_value' => '500', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_base_km', 'key_value' => '10', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_wait_time', 'key_value' => '2', 'type_id' => $new->id]);
                FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key)) . '_night_std_fare', 'key_value' => '20', 'type_id' => $new->id]);

            }
        }

        foreach ($vehicles as $vehicle) {
            $vehicle_type = VehicleTypeModel::where('vehicletype', $vehicle->type)->first();
            $vehicle->type_id = $vehicle_type->id;
            $vehicle->save();
        }
    }

}
