<?php

namespace App\Console\Commands;

use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use App\Notifications\RenewDriverLicence;
use App\Notifications\RenewInsurance;
use App\Notifications\RenewRegistration;
use App\Notifications\RenewVehicleLicence;
use App\Notifications\ServiceReminderNotification;
use DB;
use Illuminate\Console\Command;

class NotificationsCommand extends Command
{

    protected $signature = 'notification:generate';

    protected $description = 'Generate notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->service_reminder();
        $this->driver_notify();
        $this->vehicle_notify();
        $this->insurance_notify();
    }

    private function driver_notify()
    {
        $users = User::where('user_type', 'S')->get();
        $d = User::where('user_type', 'D')->get();

        foreach ($d as $data) {

            $driver_id = $data->id;
            $lic_date = $data->getMeta('exp_date');
            $msg = $data->getMeta('exp_date');
            if ($lic_date != null) {
                $to = \Carbon\Carbon::now();
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $lic_date);
                $diff_in_days = $to->diffInDays($from);
                if ($diff_in_days <= 20) {
                    foreach ($users as $user) {

                        $check_already = DB::table('notifications')
                            ->where('notifiable_id', $user->id)
                            ->where('type', 'like', '%RenewDriverLicence%')
                            ->where('data', 'like', '%"msg":"' . $lic_date . '"%')
                            ->where('data', 'like', '%"vid":' . $data->id . '%')
                            ->first();

                        if (is_null($check_already)) {
                            $user->notify(new RenewDriverLicence($msg, $driver_id, $lic_date));
                        } else {
                            DB::table('notifications')
                                ->where('id', $check_already->id)
                                ->update(['read_at' => null, 'updated_at' => $to]);
                        }
                    }
                }
            }

        }
    }
    private function vehicle_notify()
    {
        $users = User::where('user_type', 'S')->get();

        $d = VehicleModel::get();
        foreach ($d as $data) {

            $vehicle_id = $data->id;
            $reg_date = $data->reg_exp_date;
            $msg = $data->reg_exp_date;
            $to = \Carbon\Carbon::now();
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $reg_date);
            $diff_in_days = $to->diffInDays($from);
            if ($diff_in_days <= 20) {
                foreach ($users as $user) {
                    $check_already = DB::table('notifications')
                        ->where('notifiable_id', $user->id)
                        ->where('type', 'like', '%RenewRegistration%')
                        ->where('data', 'like', '%"msg":"' . $reg_date . '"%')
                        ->where('data', 'like', '%"vid":' . $data->id . '%')
                        ->first();

                    if (is_null($check_already)) {
                        $user->notify(new RenewRegistration($msg, $vehicle_id, $reg_date));
                    } else {
                        DB::table('notifications')
                            ->where('id', $check_already->id)
                            ->update(['read_at' => null, 'updated_at' => $to]);
                    }
                }
            }

        }

        foreach ($d as $data) {

            $vehicle_id = $data->id;
            $lic_date = $data->lic_exp_date;
            $msg = $data->lic_exp_date;
            $to = \Carbon\Carbon::now();
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $lic_date);
            $diff_in_days = $to->diffInDays($from);
            if ($diff_in_days <= 20) {
                foreach ($users as $user) {
                    $check_already = DB::table('notifications')
                        ->where('notifiable_id', $user->id)
                        ->where('type', 'like', '%RenewVehicleLicence%')
                        ->where('data', 'like', '%"msg":"' . $lic_date . '"%')
                        ->where('data', 'like', '%"vid":' . $data->id . '%')
                        ->first();

                    if (is_null($check_already)) {
                        $user->notify(new RenewVehicleLicence($msg, $vehicle_id, $lic_date));
                    } else {
                        DB::table('notifications')
                            ->where('id', $check_already->id)
                            ->update(['read_at' => null, 'updated_at' => $to]);
                    }
                }
            }

        }

    }

    private function insurance_notify()
    {
        $users = User::where('user_type', 'S')->get();

        $v = VehicleModel::get();
        foreach ($v as $vehicle) {
            if ($vehicle->getMeta('ins_exp_date') != null) {
                $ins_date = $vehicle->getMeta('ins_exp_date');

                $to = \Carbon\Carbon::now();
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $ins_date);
                $diff_in_days = $to->diffInDays($from);
                if ($diff_in_days <= 20) {
                    foreach ($users as $user) {
                        $check_already = DB::table('notifications')
                            ->where('notifiable_id', $user->id)
                            ->where('type', 'like', '%RenewInsurance%')
                            ->where('data', 'like', '%"msg":"' . $ins_date . '"%')
                            ->where('data', 'like', '%"vid":' . $vehicle->id . '%')
                            ->first();

                        if (is_null($check_already)) {

                            $user->notify(new RenewInsurance($ins_date, $vehicle->id, $ins_date));
                        } else {
                            DB::table('notifications')
                                ->where('id', $check_already->id)
                                ->update(['read_at' => null, 'updated_at' => $to]);
                        }
                    }
                }
            }
        }
    }

    private function service_reminder()
    {
        $users = User::where('user_type', 'S')->get();
        $d = ServiceReminderModel::get();
        foreach ($d as $data) {
            $interval = substr($data->services->overdue_unit, 0, -3);
            $int = $data->services->overdue_time . $interval;
            if ($data->last_date != 'N/D') {
                $date = date('Y-m-d', strtotime($int, strtotime($data->last_date)));
            } else {
                $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));
            }

            // echo $date;

            $to = \Carbon\Carbon::now();
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
            $diff_in_days = $to->diffInDays($from);

            $duesoon = substr($data->services->duesoon_unit, 0, -3);
            $int1 = $data->services->duesoon_time . $duesoon;
            if ($data->last_date != 'N/D') {
                $date1 = date('Y-m-d', strtotime($int1, strtotime($data->last_date)));
            } else {
                $date1 = date('Y-m-d', strtotime($int1, strtotime(date('Y-m-d'))));
            }

            $from1 = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
            $condition = $to->diffInDays($from1);
            if ($data->services->duesoon_time = null) {
                $condition = 20;
            }
            // dd($diff_in_days, $condition);
            if ($diff_in_days <= $condition) {
                foreach ($users as $user) {
                    $check_already = DB::table('notifications')
                        ->where('notifiable_id', $user->id)
                        ->where('type', 'like', '%ServiceReminder%')
                        ->where('data', 'like', '%"msg":"' . $date . '"%')
                        ->where('data', 'like', '%"vid":' . $data->id . '%')
                        ->first();

                    if (is_null($check_already)) {
                        $user->notify(new ServiceReminderNotification($data->services->description, $data->id, $date));
                    }
                }
            }

        }
    }

}
