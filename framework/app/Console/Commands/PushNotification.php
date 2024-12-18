<?php

namespace App\Console\Commands;

use App\Model\Hyvikk;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use DB;
use Illuminate\Console\Command;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotification extends Command
{

    protected $signature = 'push:notification';

    protected $description = 'push browser notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->push_notification();
    }

    private function push_notification()
    {
        $date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';

        $auth = array(
            'VAPID' => array(
                'subject' => 'Alert about new post',
                'publicKey' => 'BKt+swntut+5W32Psaggm4PVQanqOxsD5PRRt93p+/0c+7AzbWl87hFF184AXo/KlZMazD5eNb1oQVNbK1ti46Y=',
                'privateKey' => 'NaMmQJIvddPfwT1rkIMTlgydF+smNzNXIouzRMzc29c=', // in the real world, this would be in a secret file
            ),
        );

        $select1 = DB::table('push_notification')->select('*')->get()->toArray();

        $webPush = new WebPush($auth);

        foreach ($select1 as $fetch) {
            $sub = Subscription::create([
                'endpoint' => $fetch->endpoint, // Firefox 43+,
                'publicKey' => $fetch->publickey, // base 64 encoded, should be 88 chars
                'authToken' => $fetch->authtoken, // base 64 encoded, should be 24 chars
                'contentEncoding' => $fetch->contentencoding,
            ]);
            $user = User::find($fetch->user_id);
            $notifications = $user->unreadNotifications;
            foreach ($notifications as $notification) {
                if ($notification->type == "App\Notifications\RenewDriverLicence") {
                    $title = __('fleet.renew_driving_licence');
                    $body = __('fleet.driver') . ": " . User::find($notification->data['vid'])->name . "\n" . __('fleet.driver_licence_expire') . " " . date($date_format_setting, strtotime($notification->data['msg'])) . ".";
                    $url = url('admin/driver_notification', ['type' => 'renew-driving-licence']);

                } elseif ($notification->type == "App\Notifications\RenewRegistration") {
                    $vehicle = VehicleModel::find($notification->data['vid']);
                    $title = __('fleet.renew_registration');
                    $body = __('fleet.vehicle') . ": " . $vehicle->make_name . "-" . $vehicle->model_name . "-" . $vehicle->license_plate . "\n" . __('fleet.reg_certificate') . " " . date($date_format_setting, strtotime($notification->data['msg']));
                    $url = url('admin/vehicle_notification', ['type' => 'renew-registrations']);

                } elseif ($notification->type == "App\Notifications\RenewInsurance") {
                    $vehicle = VehicleModel::find($notification->data['vid']);
                    $title = __('fleet.renew_insurance');
                    $body = __('fleet.vehicle') . ": " . $vehicle->make_name . "-" . $vehicle->model_name . "-" . $vehicle->license_plate . "\n" . __('fleet.vehicle_insurance') . " " . date($date_format_setting, strtotime($notification->data['msg']));
                    $url = url('admin/vehicle_notification', ['type' => 'renew-insurance']);
                } elseif ($notification->type == "App\Notifications\RenewVehicleLicence") {
                    $vehicle = VehicleModel::find($notification->data['vid']);
                    $title = __('fleet.renew_licence');
                    $body = __('fleet.vehicle') . ": " . $vehicle->make_name . "-" . $vehicle->model_name . "-" . $vehicle->license_plate . "\n" . __('fleet.vehicle_licence') . " " . date($date_format_setting, strtotime($notification->data['msg']));
                    $url = url('admin/vehicle_notification', ['type' => 'renew-licence']);

                } elseif ($notification->type == "App\Notifications\ServiceReminderNotification") {
                    // dd($notification);
                    $reminder = ServiceReminderModel::find($notification->data['vid']);

                    $title = __('fleet.serviceReminders');
                    $body = __('fleet.vehicle') . ": " . $reminder->vehicle->make_name . "-" . $reminder->vehicle->model_name . "-" . $reminder->vehicle->license_plate . "\n" . $notification->data['msg'] . "\n" . __('fleet.date') . ": " . date($date_format_setting, strtotime($notification->data['date']));
                    $url = url('admin/reminder', ['type' => 'service-reminder']);
                }

                $array = array(
                    'title' => $title ?? "",
                    'body' => $body ?? "",
                    'img' => url('assets/images/' . Hyvikk::get('icon_img')),
                    'url' => $url ?? url('admin'),
                );
                $object = json_encode($array);

                if ($fetch->user_id == $user->id) {
                    $test = $webPush->sendOneNotification($sub, $object);
                }
                foreach ($webPush->flush() as $report) {

                    $endpoint = $report->getRequest()->getUri()->__toString();

                }
            }

        }

    }

}
