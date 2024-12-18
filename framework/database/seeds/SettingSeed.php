<?php

use App\Model\ApiSettings;
use App\Model\ChatSettingsModel;
use App\Model\EmailContent;
use App\Model\FareSettings;
use App\Model\FrontendModel;
use App\Model\PaymentSettings;
use App\Model\Settings;
use App\Model\TwilioSettings;
use App\Model\VehicleTypeModel;
use Illuminate\Database\Seeder;

class SettingSeed extends Seeder
{

    public function run()
    {
        $this->default_settings();
        $this->api_settings();
        $this->fare_settings();
        $this->email_settings();
        $this->frontend_settings();
        $this->payment_settings();
        $this->chat_settings();
        $this->twilio_settings();
    }

    private function default_settings()
    {
        DB::table('settings')->truncate();

        Settings::create(['label' => 'Website Name', 'name' => 'app_name', 'value' => 'Fleet Manager']);

        Settings::create(['label' => 'Business Address 1', 'name' => 'badd1', 'value' => 'Company Address 1']);
        Settings::create(['label' => 'Business Address 2', 'name' => 'badd2', 'value' => 'Company Address 2']);
        Settings::create(['label' => 'Email Address', 'name' => 'email', 'value' => 'master@admin.com']);
        Settings::create(['label' => 'City', 'name' => 'city', 'value' => 'Bhavnagar']);
        Settings::create(['label' => 'State', 'name' => 'state', 'value' => 'Gujarat']);
        Settings::create(['label' => 'Country', 'name' => 'country', 'value' => 'India']);
        Settings::create(['label' => 'Distence Format', 'name' => 'dis_format', 'value' => 'km']);
        Settings::create(['label' => 'Language', 'name' => 'language', 'value' => 'English-en']);
        Settings::create(['label' => 'Currency', 'name' => 'currency', 'value' => '₹']);
        Settings::create(['label' => 'Tax No', 'name' => 'tax_no', 'value' => 'ABCD8735XXX']);
        Settings::create(['label' => 'Invoice Text', 'name' => 'invoice_text', 'value' => 'Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.']);
        Settings::create(['label' => 'Small Logo', 'name' => 'icon_img', 'value' => 'logo-40.png']);
        Settings::create(['label' => 'Main Logo', 'name' => 'logo_img', 'value' => 'logo.png']);
        Settings::create(['label' => 'Time Interval', 'name' => 'time_interval', 'value' => 30]);
        Settings::create(['label' => 'Tax Charge', 'name' => 'tax_charge', 'value' => 'null']);
        Settings::create(['label' => 'Fuel Unit', 'name' => 'fuel_unit', 'value' => 'gallon']);
        Settings::create(['label' => 'Date Format', 'name' => 'date_format', 'value' => 'd-m-Y']);
        Settings::create(['label' => 'Website Footer', 'name' => 'web_footer', 'value' => '<p><span style="font-size: 16px;">© Hyvikk Solutions '.date('Y').'. All Rights Reserved.&nbsp;<span class="vertical-spacer d-none d-lg-inline">|</span>&nbsp;Powered By&nbsp;</span><a href="https://hyvikk.com/" target="_blank" class="link"><span style="font-size: 16px;">Hyvikk</span></a></p>']);
        Settings::create(['label' => 'Fuel enable for Driver', 'name' => 'fuel_enable_driver', 'value' => 0]);
        Settings::create(['label' => 'Income enable for Driver', 'name' => 'income_enable_driver', 'value' => 0]);
		Settings::create(['label' => 'Expense enable for Driver', 'name' => 'expense_enable_driver', 'value' => 0]);
    }

    private function api_settings()
    {
        DB::table('api_settings')->truncate();
        ApiSettings::create(['key_name' => 'api', 'key_value' => '1']);
        ApiSettings::create(['key_name' => 'anyone_register', 'key_value' => '0']);
        ApiSettings::create(['key_name' => 'region_availability', 'key_value' => 'region one, region two, region three']);
        ApiSettings::create(['key_name' => 'driver_review', 'key_value' => '0']);
        ApiSettings::create(['key_name' => 'booking', 'key_value' => '3']);
        ApiSettings::create(['key_name' => 'cancel', 'key_value' => '2']);
        ApiSettings::create(['key_name' => 'max_trip', 'key_value' => '1']);
        ApiSettings::create(['key_name' => 'api_key', 'key_value' => '']);
        ApiSettings::create(['key_name' => 'db_url', 'key_value' => '']);
        ApiSettings::create(['key_name' => 'db_secret', 'key_value' => '']);
        ApiSettings::create(['key_name' => 'server_key', 'key_value' => '']);
        ApiSettings::create(['key_name' => 'google_api', 'key_value' => '0']);

    }

    private function fare_settings()
    {
        DB::table('fare_settings')->truncate();
        $vehicle_types = VehicleTypeModel::get();
        // dd($vehicle_types);
        foreach ($vehicle_types as $key) {
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_fare', 'key_value' => '500', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_km', 'key_value' => '10', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_base_time', 'key_value' => '2', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_std_fare', 'key_value' => '20', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_fare', 'key_value' => '500', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_base_km', 'key_value' => '10', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_wait_time', 'key_value' => '2', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_weekend_std_fare', 'key_value' => '20', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_fare', 'key_value' => '500', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_base_km', 'key_value' => '10', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_wait_time', 'key_value' => '2', 'type_id' => $key->id]);
            FareSettings::create(['key_name' => strtolower(str_replace(" ", "", $key->vehicletype)) . '_night_std_fare', 'key_value' => '20', 'type_id' => $key->id]);
        }
    }

    private function email_settings()
    {
        DB::table('email_content')->truncate();
        EmailContent::create(['key' => 'insurance', 'value' => 'vehicle insurance email content']);
        EmailContent::create(['key' => 'vehicle_licence', 'value' => 'vehicle licence email content']);
        EmailContent::create(['key' => 'driving_licence', 'value' => 'driving licence email content']);
        EmailContent::create(['key' => 'registration', 'value' => 'vehicle registration email content']);
        EmailContent::create(['key' => 'service_reminder', 'value' => 'service reminder email content']);
        EmailContent::create(['key' => 'users', 'value' => '']);
        EmailContent::create(['key' => 'options', 'value' => '']);
        EmailContent::create(['key' => 'email', 'value' => 0]);

    }

    private function frontend_settings()
    {
        DB::table('frontend')->truncate();
        FrontendModel::create(['key_name' => 'about_us', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
        FrontendModel::create(['key_name' => 'contact_email', 'key_value' => 'master@admin.com']);
        FrontendModel::create(['key_name' => 'contact_phone', 'key_value' => '0123456789']);
        FrontendModel::create(['key_name' => 'customer_support', 'key_value' => '0999988888']);
        FrontendModel::create(['key_name' => 'about_description', 'key_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.']);
        FrontendModel::create(['key_name' => 'about_title', 'key_value' => 'Proudly serving you']);
        FrontendModel::create(['key_name' => 'facebook', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'twitter', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'instagram', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'linkedin', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'faq_link', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'cities', 'key_value' => 5]);
        FrontendModel::create(['key_name' => 'vehicles', 'key_value' => 10]);
        FrontendModel::create(['key_name' => 'cancellation', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'terms', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'privacy_policy', 'key_value' => null]);
        FrontendModel::create(['key_name' => 'enable', 'key_value' => 1]);
        FrontendModel::create(['key_name' => 'language', 'key_value' => 'English-en']);
        FrontendModel::create(['key_name' => 'admin_approval', 'key_value' => 1]);
        FrontendModel::updateOrCreate(['key_name' => 'booking_time', 'key_value' => 1]);        
    }

    private function payment_settings()
    {
        DB::table('payment_settings')->truncate();
        PaymentSettings::create(['name' => 'method', 'value' => json_encode(["cash"])]);
        PaymentSettings::create(['name' => 'currency_code', 'value' => 'INR']);
        PaymentSettings::create(['name' => 'stripe_publishable_key', 'value' => '']);
        PaymentSettings::create(['name' => 'stripe_secret_key', 'value' => '']);
        PaymentSettings::create(['name' => 'razorpay_key', 'value' => '']);
        PaymentSettings::create(['name' => 'razorpay_secret', 'value' => '']);
        PaymentSettings::updateOrCreate(['name' => 'paystack_secret', 'value' => null]);
    }

    private function chat_settings()
    {
        DB::table('chat_settings')->truncate();
        ChatSettingsModel::create(['name' => 'pusher_app_id', 'value' => '']);
        ChatSettingsModel::create(['name' => 'pusher_app_key', 'value' => '']);
        ChatSettingsModel::create(['name' => 'pusher_app_secret', 'value' => '']);
        ChatSettingsModel::create(['name' => 'pusher_app_cluster', 'value' => '']);
    }

    private function twilio_settings()
    {
        DB::table('twilio_settings')->truncate();
        TwilioSettings::create(['name' => 'sid', 'value' => '']);
        TwilioSettings::create(['name' => 'token', 'value' => '']);
        TwilioSettings::create(['name' => 'from', 'value' => '']);
        TwilioSettings::create(['name' => 'customer_message', 'value' => '']);
        TwilioSettings::create(['name' => 'driver_message', 'value' => '']);
    }
}
