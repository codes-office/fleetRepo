<?php

namespace App\Console\Commands;

use App\Mail\RenewDrivingLicence;
use App\Mail\RenewInsurance;
use App\Mail\RenewRegistration;
use App\Mail\RenewVehicleLicence;
use App\Mail\ServiceReminder;
use App\Model\ServiceReminderModel;
use App\Model\User;
use App\Model\VehicleModel;
use DB;
use Hyvikk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Hyvikk::email_msg('email') == 1 && (Hyvikk::email_msg('users') != null)) {
            $this->email_send();
        }

    }

    public function email_send()
    {
        $u = Hyvikk::email_msg('users');

        $users = User::whereIn('id', unserialize($u))->get();
        $chk = unserialize(Hyvikk::email_msg('options'));
        $d = VehicleModel::get();

        if (in_array(1, $chk)) {

            foreach ($d as $data) {

                $vehicle = $data->make_name . '-' . $data->model_name . '-' . $data->license_plate;
                $reg_date = $data->reg_exp_date;
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
                            ->whereDate('updated_at', date('Y-m-d'))
                            ->first();

                        if (is_null($check_already)) {
                            Mail::to($user->email)->send(new RenewRegistration($vehicle, $reg_date, $user->name));
                        }

                    }
                }
            }
        }

        if (in_array(3, $chk)) {

            foreach ($d as $data) {

                $vehicle = $data->make_name . '-' . $data->model_name . '-' . $data->license_plate;
                $lic_date = $data->lic_exp_date;
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
                            ->whereDate('updated_at', date('Y-m-d'))
                            ->first();

                        if (is_null($check_already)) {
                            Mail::to($user->email)->send(new RenewVehicleLicence($vehicle, $lic_date, $user->name));
                        }

                    }
                }
            }
        }

        if (in_array(4, $chk)) {

            $d1 = User::where('user_type', 'D')->where('deleted_at', null)->get();

            foreach ($d1 as $data) {

                $driver = $data->name;
                $lic_date = $data->getMeta('exp_date');
                $to = \Carbon\Carbon::now();
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $lic_date);
                $diff_in_days = $to->diffInDays($from);
                if ($diff_in_days <= 20) {
                    foreach ($users as $user) {
                        $check_already = DB::table('notifications')
                            ->where('notifiable_id', $user->id)
                            ->where('type', 'like', '%RenewDrivingLicence%')
                            ->where('data', 'like', '%"msg":"' . $lic_date . '"%')
                            ->where('data', 'like', '%"vid":' . $data->id . '%')
                            ->whereDate('updated_at', date('Y-m-d'))
                            ->first();

                        if (is_null($check_already)) {
                            Mail::to($user->email)->send(new RenewDrivingLicence($driver, $lic_date, $diff_in_days, $user->name));
                        }

                    }
                }
            }
        }

        if (in_array(2, $chk)) {

            $v = VehicleModel::get();
            foreach ($v as $vehicle) {
                if ($vehicle->getMeta('ins_exp_date') != null) {
                    $ins_date = $vehicle->getMeta('ins_exp_date');
                    $vehicle = $vehicle->make_name . '-' . $vehicle->model_name . '-' . $vehicle->license_plate;
                    $to = \Carbon\Carbon::now();
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $ins_date);
                    $diff_in_days = $to->diffInDays($from);
                    if ($diff_in_days <= 20) {
                        foreach ($users as $user) {
                            $check_already = DB::table('notifications')
                                ->where('notifiable_id', $user->id)
                                ->where('type', 'like', '%RenewInsurance%')
                                ->where('data', 'like', '%"msg":"' . $ins_date . '"%')
                                ->where('data', 'like', '%"vid":' . $data->id . '%')
                                ->whereDate('updated_at', date('Y-m-d'))
                                ->first();

                            if (is_null($check_already)) {
                                Mail::to($user->email)->send(new RenewInsurance($vehicle, $ins_date, $diff_in_days, $user->name));
                            }

                        }
                    }
                }
            }
        }

        if (in_array(5, $chk)) {

            $s = ServiceReminderModel::get();
            foreach ($s as $data) {
                $interval = substr($data->services->overdue_unit, 0, -3);
                $int = $data->services->overdue_time . $interval;
                $date = date('Y-m-d', strtotime($int, strtotime(date('Y-m-d'))));

                $to = \Carbon\Carbon::now();
                $from = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
                $diff_in_days = $to->diffInDays($from);

                $duesoon = substr($data->services->duesoon_unit, 0, -3);
                $int1 = $data->services->duesoon_time . $duesoon;
                $date1 = date('Y-m-d', strtotime($int1, strtotime(date('Y-m-d'))));

                $from1 = \Carbon\Carbon::createFromFormat('Y-m-d', $date1);
                $condition = $to->diffInDays($from1);
                if ($data->services->duesoon_time = null) {
                    $condition = 20;
                }
                $detail = $data->services->description;
                $vehicle = $data->vehicle->make_name . '-' . $data->vehicle->model_name . '-' . $data->vehicle->license_plate;
                if ($diff_in_days <= $condition) {
                    foreach ($users as $user) {
                        $check_already = DB::table('notifications')
                            ->where('notifiable_id', $user->id)
                            ->where('type', 'like', '%ServiceReminder%')
                            ->where('data', 'like', '%"msg":"' . $ins_date . '"%')
                            ->where('data', 'like', '%"vid":' . $data->id . '%')
                            ->whereDate('updated_at', date('Y-m-d'))
                            ->first();

                        if (is_null($check_already)) {
                            Mail::to($user->email)->send(new ServiceReminder($detail, $vehicle, $date, $diff_in_days, $user->name));
                        }

                    }
                }
            }
        }

    }
}
