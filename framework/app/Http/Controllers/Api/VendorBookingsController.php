<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotification;
use App\Mail\BookingSubmissionEmail;
use App\Mail\PaymentReceived;
use App\Mail\RequestReceived;
use App\Model\BookingPaymentsModel;
use App\Model\Bookings;
use App\Model\CouponModel;
use App\Model\IncomeModel;
use App\Model\BookingIncome;
use App\Model\PackagesModel;
use App\Model\ReviewModel;
use App\Model\RideOffers;
use App\Model\User;
use App\Model\VehicleMake;
use App\Model\VehicleModel;
use App\Model\VehicleTypeModel;
use App\Model\Vehicle_Model;
use DB;
use Exception;
use Hyvikk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use NotifyVendor;
use PushNotification;
use Razorpay\Api\Api;
use Validator;
class VendorBookingsController extends Controller
{
    public function my_offers(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $id = $request->user_id;
            $offers = RideOffers::where('vendor_id', $id)->where('valid_till', '>=', date('Y-m-d H:i:s'))->get();
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $offers = RideOffers::where('vendor_id', $id)->where('valid_till', '>=', date('Y-m-d H:i:s'))->where("updated_at", ">", $time)->get();
            }
            $details = array();
            foreach ($offers as $offer) {
                $details[] = array(
                    'offer_id' => $offer->id,
                    'uid' => $offer->uid,
                    'distance' => $offer->distance,
                    'timing' => $offer->timing,
                    'source' => $offer->source,
                    'destination' => $offer->destination,
                    'valid_till' => date('Y-m-d H:i:s', strtotime($offer->valid_till)),
                    'vehicle_id' => $offer->vehicle_id,
                    'vehicle_make' => $offer->vehicle->maker->make,
                    'vehicle_model' => $offer->vehicle->vehiclemodel->model,
                    'vehicle_number' => $offer->vehicle->license_plate,
                    'vehicle_color' => ($offer->vehicle->color_id) ? $offer->vehicle->vehiclecolor->color : "",
                    'vehicle_type' => $offer->vehicle->types->displayname,
                    'base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', $offer->vehicle->types->vehicletype)) . '_base_fare'),
                    'total' => round($offer->total, 2),
                    'tax_total' => round($offer->tax_total, 2), //amount for customer (ride+tax+company_charges)
                    'total_tax_percent' => $offer->total_tax_percent,
                    'total_tax_charge_rs' => $offer->total_tax_charge_rs,
                    'valid_from' => date('Y-m-d H:i:s', strtotime($offer->valid_from)),
                    'user_id' => $offer->user_id,
                    'vendor_id' => $offer->vendor_id,
                    'make_id' => $offer->vehicle->make_id,
                    'model_id' => $offer->vehicle->model_id,
                    'type_id' => $offer->vehicle->type_id,
                    'color_id' => $offer->vehicle->color_id,
                    'booking_id' => $offer->booking_id,
                    'status' => $offer->status,
                    // 'driver_amount' => round($offer->total - $offer->company_commission + $offer->total_tax_charge_rs, 2), // show this amount to driver everywhere
                    'driver_amount' => round($offer->total - $offer->company_commission, 2), // show this amount to driver everywhere
                    'company_commission' => $offer->company_commission,
                    'commission' => $offer->commission . "%",
                    'company_charges' => $offer->company_charges,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($offer->updated_at)),
                    "delete_status" => (isset($offer->deleted_at)) ? 1 : 0,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $details;
        }
        return $data;
    }
    public function edit_offer(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'valid_from' => 'required',
            'valid_till' => 'required',
            'id' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $offer = RideOffers::find($request->id);
            $offer->valid_from = $request->valid_from;
            $offer->valid_till = $request->valid_till;
            $offer->save();
            $data['success'] = "1";
            $data['message'] = "Ride Offer updated successfully!";
            $data['data'] = array('offer_id' => $offer->id, 'timestamp' => date('Y-m-d H:i:s', strtotime($offer->updated_at)));
        }
        return $data;
    }
    public function delete_offer(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $id = $request->id;
            $offers = RideOffers::find($id)->delete();
            $data['success'] = "1";
            $data['message'] = "Ride offer deleted successfully!";
            $data['data'] = array('offer_id' => $id, 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }
    public function get_month_years()
    {
        // $dates = BookingPaymentsModel::selectRaw('year(created_at) year, monthname(created_at) month')
        //     ->groupBy('year', 'month')
        //     ->orderBy('year', 'desc')
        //     ->get();
        $dates = Bookings::selectRaw('year(created_at) year, monthname(created_at) month')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get();
        $records = array();
        foreach ($dates as $date) {
            $records[] = $date->month . ", " . $date->year;
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $records;
        // dd($records);
        return $data;
    }
    public function is_paid(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $status = (isset($booking->payment)) ? $booking->payment : 0;
            // if($booking->pay_later == 1){
            //     $status = 0;
            // }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = array(
                'is_paid' => $status,
            );
        }
        return $data;
    }
    public function book_package(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'journey_datetime' => 'required',
            // 'journey_time' => 'required',
            'amount' => 'required|numeric',
            'package_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'total_tax_percent' => 'required|numeric',
            'total_tax_charge_rs' => 'required|numeric',
            'discount_amount' => 'nullable|numeric',
            'coupon_id' => 'nullable|integer',
            'city' => 'required',
            'source' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            // dd($request->all());
            $package = PackagesModel::find($request->package_id);
            $booking = Bookings::create([
                'pickup' => date('Y-m-d H:i:s', strtotime($request->journey_datetime)),
                'status' => 0,
                'customer_id' => $request->user_id,
                // 'vehicle_id' => $package->vehicle_id,
                'pickup_addr' => $request->source,
                // 'dest_addr' => $package->destination,
                'is_booked' => 1,
            ]);
            if ($request->coupon_id) {
                $booking->coupon_id = $request->coupon_id;
                $booking->discount_amount = $request->discount_amount;
            }
            $booking->city = $request->city;
            $booking->vehicle_typeid = $package->type_id;
            $booking->booking_type = 1;
            // $booking->journey_date = $request->journey_date;
            // $booking->journey_time = $request->journey_time;
            $booking->journey_date = date('d-m-Y', strtotime($request->journey_datetime));
            $booking->journey_time = date('H:i:s', strtotime($request->journey_datetime));
            $booking->total = $request->amount;
            $booking->package_id = $request->package_id;
            $booking->booking_option = "Rental";
            $booking->accept_status = 0; //0=yet to accept, 1= accept
            $booking->ride_status = null;
            $driver_amount = round($request->get('amount') - ($request->get('amount') * Hyvikk::extra('general_company_commission')) / 100, 2);
            $booking->setMeta([
                'customerId' => $request->user_id,
                'date' => date('Y-m-d'),
                'total' => $request->get('amount'),
                'mileage' => 0,
                'waiting_time' => 0,
                'total_kms' => 0,
                'tax_total' => $request->total_amount,
                'total_tax_percent' => $request->total_tax_percent,
                'total_tax_charge_rs' => $request->total_tax_charge_rs,
                'driver_amount' => $driver_amount,
                'general_company_commission' => Hyvikk::extra('general_company_commission'),
            ]);
            $booking->save();
            // browser notification
            $this->push_notification($booking->id);
            $this->send_sms($booking->id);
            // send notification to drivers
            $this->booking_notification($booking->id, $booking->vehicle_typeid, $booking->city, 'Rental');
            $this->request_receive_mail($booking->id);
            // $chck = NotifyVendor::status_notification($booking->id, "pending_booking_notification");
            // return $chck;
            $r1 = time();
            $r2 = rand(10, 99);
            $r3 = substr(time(), 7);
            $r4 = rand(100, 199);
            $random_num = $r1 . $r2 . $r3 . $r4 . $booking->id;
            $receipt_no = substr($random_num, -10);
            try {
                // $receipt_no = time() . "_" . date('Y_m_d') . "_" . $booking_id;
                $api = new Api(Hyvikk::payment('razorpay_key'), Hyvikk::payment('razorpay_secret'));
                $order = $api->order->create(array('receipt' => $receipt_no, 'amount' => $request->total_amount * 100, 'currency' => 'INR', 'payment_capture' => 1));
                BookingPaymentsModel::create(['method' => 'razorpay', 'payment_status' => "pending",
                    'booking_id' => $booking->id,
                    'receipt_no' => $receipt_no,
                    'order_id' => $order['id'],
                ]);
                $row = Bookings::find($booking->id);
                // dd($row->vehicle_id);
                $record = array(
                    'id' => $row->id,
                    'customer_id' => $row->customer_id,
                    'vehicle_id' => $row->vehicle_id,
                    'user_id' => $row->user_id,
                    'pickup' => $row->pickup,
                    'dropoff' => $row->dropoff,
                    'pickup_addr' => $row->pickup_addr,
                    'dest_addr' => $row->dest_addr,
                    'travellers' =>  ($package->vehicle->types->seats)?? $row->travellers,
                    'status' => $row->status,
                    'driver_id' => $row->driver_id,
                    'note' => $row->note,
                    'is_booked' => $row->is_booked,
                    'custom_approved' => $row->custom_approved,
                    "meta_details" => $row->getMeta(),
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    'customer_name' => $row->customer->name,
                    'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->maker->make . "-" . $row->vehicle->vehiclemodel->model : "",
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    "return_date" => $row->return_date,
                    "return_time" => $row->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                    'receipt_no' => $receipt_no,
                    'order_id' => $order['id'],
                );
                $data['success'] = "1";
                $data['message'] = "Package booked successfully!";
                $data['data'] = $record;
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                BookingPaymentsModel::create(['method' => 'razorpay', 'payment_status' => "failed",
                    'booking_id' => $booking->id,
                    'receipt_no' => $receipt_no,
                    'reason' => $error_msg,
                ]);
                $data['success'] = "0";
                $data['message'] = $error_msg;
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function verify_payment(Request $request)
    {
        // $chk = NotifyVendor::online_payment_notification(1029);
        // return $chk;
        $validation = Validator::make($request->all(), [
            'razorpay_signature' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'booking_id' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $api = new Api(Hyvikk::payment('razorpay_key'), Hyvikk::payment('razorpay_secret'));
            $attributes = array('razorpay_signature' => $request->razorpay_signature, 'razorpay_payment_id' => $request->razorpay_payment_id, 'razorpay_order_id' => $request->razorpay_order_id);
            try {
                $order = $api->utility->verifyPaymentSignature($attributes);
                if ($order == null) {
                    $payment = $api->payment->fetch($request->razorpay_payment_id);
                    // dd($payment);
                    $order = $api->order->fetch($request->razorpay_order_id);
                    // dd($order);
                    $payment_info = array(
                        "razorpay_payment_id" => $payment->id,
                        "razorpay_order_id" => $request->razorpay_order_id,
                        "razorpay_signature" => $request->razorpay_signature,
                        "entity" => $payment->entity,
                        "amount" => $payment->amount,
                        "currency" => $payment->currency,
                        "status" => $payment->status,
                        "order_id" => $payment->order_id,
                        "invoice_id" => $payment->invoice_id,
                        "international" => $payment->international,
                        "method" => $payment->method,
                        "amount_refunded" => $payment->amount_refunded,
                        "refund_status" => $payment->refund_status,
                        "captured" => $payment->captured,
                        "description" => $payment->description,
                        "card_id" => $payment->card_id,
                        "bank" => $payment->bank,
                        "wallet" => $payment->wallet,
                        "vpa" => $payment->vpa,
                        "email" => $payment->email,
                        "contact" => $payment->contact,
                        "fee" => $payment->fee,
                        "tax" => $payment->tax,
                        "error_code" => $payment->error_code,
                        "error_description" => $payment->error_description,
                        "created_at" => $payment->created_at,
                    );
                    $booking_id = $request->booking_id;
                    $booking = Bookings::find($booking_id);
                    BookingPaymentsModel::where('booking_id', $booking_id)->where('receipt_no', $order->receipt)->update(['method' => 'razorpay', 'amount' => $booking->tax_total, 'payment_details' => json_encode($payment_info), 'transaction_id' => $payment['id'], 'payment_status' => "succeeded"
                        , 'sign' => $request->razorpay_signature, 'payment_id' => $payment->id,
                        'reason' => $payment->error_description,
                    ]);
                    $booking->receipt = 1;
                    $booking->payment = 1;
                    $booking->payment_method = "razorpay";
                    // $booking->status = 1;
                    // $booking->ride_status = "Completed";
                    $booking->save();
                    // if (in_array($booking->booking_option, ["offer"])) {
                    //     // send notification to drivers
                    //     $this->booking_notification($booking->id, $booking->vehicle_typeid, $booking->city, $booking->booking_option);
                    // }
                    if (in_array($booking->booking_option, ["Rental", "offer"])) {
                        // 29/08/2020
                        if ($booking->booking_option == "offer") {
                            $selected_offer = RideOffers::find($booking->offer_id);
                            $selected_offer->booking_id = $booking->id;
                            $selected_offer->save();
                            $booking->driver_id = $selected_offer->user_id;
                            $booking->vehicle_id = $selected_offer->vehicle_id;
                            $booking->save();
                        }
                        // send notification to drivers 29/08/2020
                        // $this->booking_notification($booking->id, $booking->vehicle_typeid, $booking->city, $booking->booking_option);
                        // 29/08/2020
                        $this->request_receive_mail_after_payment($booking->id);
                    }
                    Mail::to($booking->customer->email)->send(new PaymentReceived($booking, "C"));
                    $chk = NotifyVendor::online_payment_notification($booking->id);
                    // return $chk;
                    if ($booking->driver_id) {
                        Mail::to($booking->driver->email)->send(new PaymentReceived($booking, "D"));
                    }
                    Mail::to("services@mpcab.in")->send(new PaymentReceived($booking, "S"));
                    // $data['amount'] = $booking->tax_total;
                    $data['success'] = "1";
                    $data['message'] = "Payment verified successfully!";
                    $data['data'] = array('is_paid' => $booking->payment);
                }
            } catch (Exception $e) {
                // dd($e);
                // $booking = Bookings::find($request->booking_id);
                // BookingPaymentsModel::where('booking_id', $request->booking_id)->where('receipt_no', $_GET['receipt_no'])->update(['method' => 'razorpay', 'amount' => $booking->tax_total, 'payment_status' => "failed",
                //     'reason' => "Invalid signature passed!",
                // ]);
                $data['success'] = "0";
                $data['message'] = "Invalid signature passed!";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function request_receive_mail_after_payment($id)
    {
        $booking = Bookings::find($id);
        $type_id = $booking->vehicle_typeid;
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }
        if ($booking->booking_option == "Rental") {
            $drivers = User::where('user_type', 'D')->get();
            foreach ($drivers as $d) {
                if (in_array($d->vehicle_id, $vehicles)) {
                    // Mail::to($d->email)->send(new RequestReceived($booking, 'D'));
                }
            }
        }
        if ($booking->booking_option == "offer") {
            // Mail::to($booking->driver->email)->send(new RequestReceived($booking, "D"));
        }
    }
    public function request_receive_mail($id)
    {
        $booking = Bookings::find($id);
        $type_id = $booking->vehicle_typeid;
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }
        Mail::to("services@mpcab.in")->send(new RequestReceived($booking, 'S'));
        Mail::to($booking->customer->email)->send(new BookingSubmissionEmail($booking, 'C'));
        if (!in_array($booking->booking_option, ["Rental", "offer"])) {
            $drivers = User::where('user_type', 'D')->get();
            foreach ($drivers as $d) {
                if (in_array($d->vehicle_id, $vehicles)) {
                    // Mail::to($d->email)->send(new RequestReceived($booking, 'D'));
                }
            }
        }
    }
    public function booking_notification($id, $type_id, $city, $trip_type)
    {
        $booking = Bookings::find($id);
        $amount = $booking->getMeta('tax_total');
        if ($booking->booking_option == "offer") {
            // $amount = $booking->offer->total - $booking->offer->company_commission + $booking->offer->total_tax_charge_rs;
            $amount = $booking->offer->total - $booking->offer->company_commission;
        }
        $data['success'] = 1;
        $data['key'] = "booking_notification";
        $data['message'] = 'Data Received.';
        $data['title'] = "New Booking Request (" . $trip_type . ")";
        $data['description'] = "Do you want to Accept it ?";
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');
        // ///// /// /// //  fare calculation
        $commission_rs = 0;
        $discount_amount = ($booking->discount_amount) ? $booking->discount_amount : 0;
        $driver_amount = round($booking->total - ($booking->total * Hyvikk::extra('general_company_commission')) / 100, 2);
        if ($booking->booking_option == "OneWay") {
            $driver_amount = round($driver_amount - Hyvikk::extra('general_company_charges'), 2);
        }
        $fare_details = array(); //vehicletype,time,km,total ride amount, ride amount,company charges ,company commision,driver earnings
        if ($booking->booking_option == "offer") {
            // $tax_total = round($booking->offer->total - $booking->offer->company_commission, 2);
            // $ride_amount = round($booking->offer->total - $booking->offer->company_commission, 2);
            $tax_total = $booking->tax_total;
            $ride_amount = $booking->total;
            $driver_amount = round($booking->offer->total - $booking->offer->company_commission, 2);
            $company_charges = $booking->company_charges;
            $company_commission = $booking->company_commission;
            $commission_rs = $booking->company_commission;
        } else {
            $tax_total = $booking->tax_total;
            $ride_amount = $booking->total;
            // $driver_amount = $driver_amount;
            $driver_amount = ($booking->driver_amount) ? $booking->driver_amount : $driver_amount;
            $company_charges = ($booking->booking_option == "OneWay") ? Hyvikk::extra('general_company_charges') : 0;
            $company_commission = Hyvikk::extra('general_company_commission') . "%";
            if ($booking->booking_option == "offer request") {
                $company_charges = $booking->vendor_fee;
                $ride_amount = $booking->total + $booking->vendor_fee;
            }
            $commission_rs = round(($booking->total * Hyvikk::extra('general_company_commission')) / 100, 2);
        }
        $base_fare_value = null;
        if ($booking->booking_option != 'OneWay') {
            $base_fare_value = ($booking->vehicle_typeid) ? Hyvikk::fare(strtolower(str_replace(' ', '', $booking->types->vehicletype)) . '_base_fare') : null;
        }
        $fare_details = array(
            'vehicle_type' => ($booking->vehicle_typeid != null) ? $booking->types->displayname : null,
            'time' => $booking->driving_time,
            'total_kms' => $booking->total_kms . "kms",
            'total_ride_amount' => $tax_total,
            'ride_amount' => $ride_amount, //subtotal
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'driver_amount' => $driver_amount,
            'company_charges' => $company_charges,
            'company_commission' => $company_commission,
            'discount_amount' => "" . $discount_amount,
            'base_fare' => "" . $base_fare_value,
            'commission_rs' => "" . $commission_rs,
        );
        /// ./ //// //// // fare-calculation end
        $data['data'] = array('riderequest_info' => array(
            'booking_option' => $booking->booking_option,
            'user_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'source_address' => $booking->pickup_addr,
            'dest_address' => $booking->dest_addr,
            'book_date' => date('Y-m-d', strtotime($booking->created_at)),
            'book_time' => date('H:i:s', strtotime($booking->created_at)),
            'journey_date' => date('d-m-Y', strtotime($booking->journey_date)),
            'journey_time' => date('H:i:s', strtotime($booking->journey_time)),
            'accept_status' => $booking->accept_status,
            'tax_total' => $amount,
            'return_date' => ($booking->booking_option == "RoundTrip") ? date('d-m-Y', strtotime($booking->getMeta('return_date'))) : "",
            'return_time' => ($booking->booking_option == "RoundTrip") ? date('H:i:s', strtotime($booking->getMeta('return_time'))) : "",
            'travellers' => $booking->travellers,
            "vendor" => true,
            'fare_details' => $fare_details,
            'other_things_we_should_know' => $booking->other_things_we_should_know,
        ),
        );
        // dd($data);
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }
        // $testnote = PushNotification::app('appNameAndroid')
        //     ->to('fxDl0c8u-OQ:APA91bFFgTwmXjzD9kLZPvYdjtO2c26FmSznotrPi5WJRfyO4TvHYMUYW_a1sLs7ORS7d_Ugd48t3o1ajTp3-Ou3TignYkQx_D-sds0JWpHmdPS1XTzA0Ci1KorAWf4dIf5pf3AGeJ5G')
        //     ->send($data);
        // dd($testnote);
        if (in_array($trip_type, ["Local", "RoundTrip"])) {
            $drivers = User::meta()->where('users_meta.key', '=', 'city')->where('users_meta.value', 'like', '%' . $city . '%')->where('user_type', 'D')->get();
        } else if ($trip_type == "offer") {
            $drivers = User::where('id', $booking->offer->user_id)->where('user_type', 'D')->get();
        } else {
            $drivers = User::where('is_verified', 1)->where('user_type', 'D')->get();
        }
        foreach ($drivers as $d) {
            // if (in_array($d->vehicle_id, $vehicles)) {
                if ($d->getMeta('fcm_id')) {
                    PushNotification::app('appNameAndroid')
                        ->to($d->getMeta('fcm_id'))
                        ->send($data);
                }
            // }
        }
    }
    public function cash_payment(Request $request)
    {
        $booking = Bookings::find($request->get('booking_id'));
        // $booking->status = 1;
        // $booking->payment = 1;
        if ($booking != null) {
            $booking->customer_paid = 1;
            $booking->receipt = 1;
            $booking->payment = 1;
            $booking->payment_method = "cash";         
            $booking->pay_later = $request->pay_later;
            $booking->save();
            // if (isset($booking->driver_id)) {
            //     $this->cash_payment_notification($booking->id);
            // }
            $res = array(
                'booking_id' => $request->get('booking_id'),
                'paid_status' => 'waiting',
                // 'payment_status' => $booking->status,
                'payment_mode' => 'cash'
            );
            if($request->pay_later == 0){
                $chk = NotifyVendor::online_payment_notification($booking->id);
            }elseif(empty($request->pay_later) || $request->pay_later == 1){
                $res['payment_mode'] = 'pending';
                $res['paid_status'] = 'payment pending';
            }
            $data['success'] = 1;
            $data['message'] = "Payment Received.";
            $data['data'] = $res;
        } else {
            $data['success'] = 0;
            $data['message'] = "Unable to Process your Request. Please, Try again Later !";
            $data['data'] = null;
        }
        return $data;
    }
    public function add_payment(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $r1 = time();
            $r2 = rand(10, 99);
            $r3 = substr(time(), 7);
            $r4 = rand(100, 199);
            $random_num = $r1 . $r2 . $r3 . $r4 . $booking->id;
            $receipt_no = substr($random_num, -10);
            try {
                $api = new Api(Hyvikk::payment('razorpay_key'), Hyvikk::payment('razorpay_secret'));
                $order = $api->order->create(array('receipt' => $receipt_no, 'amount' => $booking->tax_total * 100, 'currency' => 'INR', 'payment_capture' => 1));
                BookingPaymentsModel::create(['method' => 'razorpay', 'payment_status' => "pending",
                    'booking_id' => $booking->id,
                    'receipt_no' => $receipt_no,
                    'order_id' => $order['id'],
                ]);
                $data['success'] = "1";
                $data['message'] = "Payment order added successfully!";
                $data['data'] = array(
                    'booking_id' => $booking->id,
                    'receipt_no' => $receipt_no,
                    'order_id' => $order['id'],
                );
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                BookingPaymentsModel::create(['method' => 'razorpay', 'payment_status' => "failed",
                    'booking_id' => $booking->id,
                    'receipt_no' => $receipt_no,
                    'reason' => $error_msg,
                ]);
                // $data['order_id'] = "";
                // $data['amount'] = $request->total_amount;
                // $data['receipt_no'] = $receipt_no;
                // $data['message'] = $error_msg;
                $data['success'] = "0";
                $data['message'] = $error_msg;
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function cash_payment_notification($id)
    {
        $booking = Bookings::find($id);
        $data['success'] = 1;
        $data['key'] = "confirm_payment_notification";
        $data['message'] = 'Payment Received.';
        //$data['title'] = "Payment Received CASH, id: " . $id;
        $data['title'] = "New Payment has been Received.";
        $data['description'] = $booking->pickup_addr . "-" . $booking->dest_addr;
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');
        $review = ReviewModel::where('booking_id', $id)->first();
        if ($review != null) {
            $r = array('user_id' => $review->user_id, 'booking_id' => $review->booking_id, 'ratings' => $review->ratings, 'review_text' => $review->review_text, 'date' => date('Y-m-d', strtotime($review->created_at)));
        } else {
            $r = new \stdClass;
        }
        if (Hyvikk::get('dis_format') == 'meter') {
            $unit = 'm';
        }if (Hyvikk::get('dis_format') == 'km') {
            $unit = 'km';
        }
        $data['data'] = array('riderequest_info' => array('user_id' => $booking->customer_id,
            'booking_id' => $id, 'source_address' => $booking->pickup_addr,
            'booking_option' => $booking->booking_option,
            'dest_address' => $booking->dest_addr,
            'source_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
            'dest_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('rideend_timestamp'))),
            'book_timestamp' => date('d-m-Y', strtotime($booking->created_at)),
            'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
            'journey_date' => date('d-m-Y', strtotime($booking->getMeta('journey_date'))),
            'journey_time' => date('H:i:s', strtotime($booking->getMeta('journey_time'))),
            'tax_total' => $booking->getMeta('tax_total'),
            'driving_time' => $booking->getMeta('driving_time'),
            'total_kms' => $booking->getMeta('total_kms') . " " . $unit,
            //'amount' => $booking->getMeta('tax_total'),
            'ride_status' => $booking->getMeta('ride_status'),
            'payment_status' => $booking->status,
            'payment_mode' => 'CASH',
        ),
            'driver_details' => array('driver_id' => $booking->driver_id,
                'driver_name' => $booking->driver->name,
                'profile_pic' => $booking->driver->getMeta('driver_image'),
            ),
            'fare_breakdown' => array('base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', $booking->vehicle->types->vehicletype)) . '_base_fare'), //done
                'ride_amount' => $booking->getMeta('tax_total'),
                'extra_charges' => '0',
            ),
            'review' => $r,
        );
        if ($booking->driver->getMeta('fcm_id') != null) {
            PushNotification::app('appNameAndroid')
                ->to($booking->driver->getMeta('fcm_id'))
                ->send($data);
        }
    }
    public function online_payment_notification($id)
    {
        $booking = Bookings::find($id);
        $data['success'] = 1;
        $data['key'] = "confirm_payment_notification";
        $data['message'] = 'Payment Received.';
        //$data['title'] = "Payment Received Online, id: " . $id;
        $data['title'] = "New Payment has been Received.";
        $data['description'] = $booking->pickup_addr . "-" . $booking->dest_addr;
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');
        $review = ReviewModel::where('booking_id', $id)->first();
        if ($review != null) {
            $r = array('user_id' => $review->user_id, 'booking_id' => $review->booking_id, 'ratings' => $review->ratings, 'review_text' => $review->review_text, 'date' => date('Y-m-d', strtotime($review->created_at)));
        } else {
            $r = new \stdClass;
        }
        if (Hyvikk::get('dis_format') == 'meter') {
            $unit = 'm';
        }if (Hyvikk::get('dis_format') == 'km') {
            $unit = 'km';
        }
        $amount = $booking->getMeta('tax_total');
        if ($booking->booking_option == "offer") {
            // $amount = $booking->offer->total - $booking->offer->company_commission + $booking->offer->total_tax_charge_rs;
            $amount = $booking->offer->total - $booking->offer->company_commission;
        }
        $data['data'] = array('riderequest_info' => array('user_id' => $booking->customer_id,
            'booking_id' => $id, 'source_address' => $booking->pickup_addr,
            'booking_option' => $booking->booking_option,
            'dest_address' => $booking->dest_addr,
            'source_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
            'dest_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('rideend_timestamp'))),
            'book_timestamp' => date('d-m-Y', strtotime($booking->created_at)),
            'ridestart_timestamp' => date('Y-m-d H:i:s', strtotime($booking->getMeta('ridestart_timestamp'))),
            'journey_date' => date('d-m-Y', strtotime($booking->getMeta('journey_date'))),
            'journey_time' => date('H:i:s', strtotime($booking->getMeta('journey_time'))),
            'tax_total' => $amount,
            'driving_time' => $booking->getMeta('driving_time'),
            'total_kms' => $booking->getMeta('total_kms') . " " . $unit,
            //'amount' => $amount,
            'ride_status' => $booking->getMeta('ride_status'),
            'payment_status' => $booking->status,
            'payment_mode' => 'Online',
        ),
            'driver_details' => array('driver_id' => $booking->driver_id,
                'driver_name' => ($booking->driver_id) ? $booking->driver->name : "",
                'profile_pic' => ($booking->driver_id) ? $booking->driver->getMeta('driver_image') : "",
            ),
            'fare_breakdown' => array('base_fare' => ($booking->vehicle_id) ? Hyvikk::fare(strtolower(str_replace(' ', '', $booking->vehicle->types->vehicletype)) . '_base_fare') : "", //done
                'ride_amount' => $booking->getMeta('tax_total'),
                'extra_charges' => '0',
            ),
            'review' => $r,
        );
        // dd($data);
        if ($booking->driver_id) {
            if ($booking->driver->getMeta('fcm_id') != null) {
                PushNotification::app('appNameAndroid')
                    ->to($booking->driver->getMeta('fcm_id'))
                    ->send($data);
            }
        }
    }
    public function apply_couponon_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
            'amount' => 'required|numeric',
            'coupon_code' => 'required|exists:coupons,code,code,' . $request->coupon_code,
            'driver_allowance' => 'nullable|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $check = CouponModel::where('code', $request->coupon_code)->exists();
            if ($check) {
                $coupon = CouponModel::where('code', $request->coupon_code)->first();
                if ($coupon->type == 1) {
                    // percentage
                    $total_amount = round($request->amount - ($request->amount * $coupon->value) / 100, 2);
                    $discount_amount = round((($request->amount * $coupon->value) / 100), 2);
                } else {
                    // amount
                    $total_amount = $request->amount - $coupon->value;
                    $discount_amount = $coupon->value;
                }
                // tax calculation
                if ($request->driver_allowance) {
                    $subtotal = $request->driver_allowance + $total_amount;
                } else {
                    $subtotal = $total_amount;
                }
                $count = 0;
                $tax_charges = array();
                $tax_percent = array();
                if (Hyvikk::get('tax_charge') != "null") {
                    $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                    foreach ($taxes as $key => $val) {
                        $count = $count + $val;
                        $tax_charges[$key] = round(($subtotal * $val) / 100, 2);
                        $tax_percent[$key] = $val;
                    }
                }
                $tax_total = round((($subtotal * $count) / 100) + $subtotal, 2);
                $total_tax_percent = $count;
                $total_tax_charge_rs = round(($subtotal * $count) / 100, 2);
                $booking = Bookings::find($request->booking_id);
                if ($booking) {
                    $driver_amount = null;
                    if ($booking->booking_option == "Local") {
                        $driver_amount = round($total_amount - ($total_amount * Hyvikk::extra('general_company_commission')) / 100, 2);
                    }
                    if ($booking->booking_option == "OneWay") {
                        $driver_amount = round($total_amount - (($total_amount * Hyvikk::extra('general_company_commission')) / 100) - Hyvikk::extra('general_company_charges'), 2);
                    }
                    if ($booking->booking_option == "offer request") {
                        $booking->driver_amount = $tax_total - Hyvikk::extra('custom_vendor_fee');
                    }
                    if ($booking->booking_option == "RoundTrip") {
                        $driver_amount = round($subtotal - ($subtotal * Hyvikk::extra('general_company_commission')) / 100, 2);
                    }
                    $booking->coupon_id = $coupon->id;
                    $booking->discount_amount = $discount_amount;
                    $booking->setMeta([
                        'total' => $total_amount,
                        'tax_total' => $tax_total,
                        'total_tax_charge_rs' => $total_tax_charge_rs,
                        'driver_amount' => $driver_amount,
                        'ride_amount' => $subtotal,
                    ]);
                    $booking->save();
                } else {
                    $data['success'] = "0";
                    $data['message'] = "Invalid booking details.";
                    $data['data'] = null;
                }
                $data['success'] = "1";
                $data['message'] = "Coupon Applied successfully!";
                $data['data'] = array(
                    'discount_amount' => $discount_amount,
                    'ride_amount' => $total_amount,
                    'subtotal' => $subtotal,
                    'tax_total' => $tax_total,
                    'total_tax_percent' => $total_tax_percent,
                    'total_tax_charge_rs' => $total_tax_charge_rs,
                    'tax_charges' => $tax_charges,
                    'tax_percent' => $tax_percent,
                    'coupon_id' => $coupon->id,
                );
            } else {
                $data['success'] = "0";
                $data['message'] = "The selected coupon code is invalid.";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function min_amount_calc(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'type_id' => 'required|integer',
            'total_kms' => 'required|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $index['success'] = "0";
            $index['message'] = implode(", ", $errors->all());
            $index['data'] = null;
        } else {
            $v_type = VehicleTypeModel::find($request->type_id);
            $type = strtolower(str_replace(" ", "", $v_type->vehicletype));
            $discount = Hyvikk::get('discount');
            $total_kms = str_replace(",", "", $request->total_kms);
            $std_fare = Hyvikk::fare($type . '_std_fare');
            $total_amount = $total_kms * $std_fare;
            $discount_amount = ($total_amount * $discount) / 100;
            $amount = round($total_amount - $discount_amount, 2) + Hyvikk::extra('custom_vendor_fee');
            $count = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $count = $count + $val;
                }
            }
            $tax_total = round((($amount * $count) / 100) + $amount, 2);
            $total_tax_percent = $count;
            $total_tax_charge_rs = round(($amount * $count) / 100, 2);
            $index['success'] = "1";
            $index['message'] = "Data Received Successfully !";
            $index['data'] = array(
                // 'map_info' => $response,
                // 'fare_details' => $fare_details,
                'amount' => round($amount, 2),
            );
        }
        return $index;
    }
    public function round_fare_calculation_api(Request $request)
    {
        $validation = Validator::make($request->all(), [
            // 'days' => 'required|integer|min:1',
            // 'nights' => 'required|integer|min:1',
            'journey_datetime' => 'required',
            'return_datetime' => 'required',
            'type_id' => 'required|integer',
            'total_kms' => 'required|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $index['success'] = "0";
            $index['message'] = implode(", ", $errors->all());
            $index['data'] = null;
        } else {
            // $days = $request->days;
            // $pickup = date('Y-m-d H:i:s', strtotime($request->journey_datetime));
            // $dropoff = date('Y-m-d H:i:s', strtotime($request->return_datetime));
            // $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pickup);
            // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dropoff);
            // $days = $to->diffInDays($from);
            // $hours = $to->diff($from)->format('%h');
            // $nights = $days;
            // if ($days > 1 && $hours >= 3) {
            //     $days++;
            // }
            $pickup_datetime = date('Y-m-d H:i:s', strtotime($request->journey_datetime));
            $dropoff_datetime = date('Y-m-d H:i:s', strtotime($request->return_datetime));
            $pickup = date('Y-m-d', strtotime($request->journey_datetime));
            $dropoff = date('Y-m-d', strtotime($request->return_datetime));
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $pickup);
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dropoff);
            $days = $to->diffInDays($from);
            $to_datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pickup_datetime);
            $from_datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dropoff_datetime);
            $hours = $to_datetime->diff($from_datetime)->format('%h');
            $nights = $days;
            // echo "days:" . $days . "<br>";
            if ($days > 1) {
                $days--;
                $t1 = date('H', strtotime($request->journey_datetime));
                $rh1 = 24 - $t1;
                $t2 = date('H', strtotime($request->return_datetime));
                $rh2 = 24 - $t2; //e.g t2=2 (2 am) => $rh2: 24-2= 22 (means 2 hour jouney of return date)
                // echo "rh1:" . $rh1 . " rh2:" . $rh2 . " " . $days . "<br>";
                if ($rh1 >= 3) {
                    $days++;
                }
                if ($rh2 <= 21) {
                    $days++;
                }
            }
            if ($days == 1) {
                $tjd = date('H', strtotime($request->journey_datetime));
                $rh = 24 - $tjd;
                // dd($rh);
                if ($rh >= 3) {
                    $days = 2;
                }
            }
            // dd($days);
            if ($days == 0 && $hours > 0) {
                $nights = 1;
                $days = 1;
            }
            if ($days == 0 && $hours == 0) {
                $index['success'] = "0";
                $index['message'] = "return date & time must be after journey date & time";
                $index['data'] = "";
                return $index;
            }
            $v_type = VehicleTypeModel::find($request->type_id);
            $type = strtolower(str_replace(" ", "", $v_type->vehicletype));
            $rmd = Hyvikk::extra('round_min_distance');
            $ark = $request->total_kms / $days;
            $std_fare = Hyvikk::fare($type . '_std_fare');
            $da = Hyvikk::extra('round_driver_allowance');
            $data['driver_allowance'] = $da * $nights;
            if ($ark <= $rmd && $days > 1) {
                $data['ride_amount'] = ($days * $rmd * $std_fare);
            }
            if ($ark > $rmd && $days > 1) {
                $data['ride_amount'] = ($days * $ark * $std_fare);
            }
            if ($ark <= $rmd && $days == 1) {
                $data['ride_amount'] = ($rmd * $std_fare);
            }
            if ($ark > $rmd && $days == 1) {
                $data['ride_amount'] = ($ark * $std_fare);
            }
            // $data['total'] = $data['ride_amount'];
            // $data['ride_amount'] = Hyvikk::extra('round_min_distance') * $days * Hyvikk::fare($type . '_std_fare');
            // $data['driver_allowance'] = Hyvikk::extra('round_driver_allowance') * $days;
            $data['ride_amount'] = round($data['ride_amount'], 2);
            $data['total'] = $data['ride_amount'] + $data['driver_allowance'];
            // calculate tax charges
            $count = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $count = $count + $val;
                }
            }
            $tax_total = round((($data['total'] * $count) / 100) + $data['total'], 2);
            $total_tax_percent = $count;
            $total_tax_charge_rs = round(($data['total'] * $count) / 100, 2);
            $index['success'] = "1";
            $index['message'] = "Data fetched";
            $index['data'] = array(
                'ride_amount' => $data['ride_amount'],
                'driver_allowance' => $data['driver_allowance'],
                'subtotal' => $data['total'],
                'tax_total' => $tax_total,
                'total_tax_percent' => $total_tax_percent,
                'total_tax_charge_rs' => $total_tax_charge_rs,
            );
        }
        return $index;
    }
    public function oneway_packages(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'total_kms' => 'required|numeric',
            // new
            'vehicletype_id' => 'required|numeric',
            'source_city' => 'required',
            'dest_city' => 'required',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $kms = $request->total_kms;
            $details = array();
            $types = VehicleTypeModel::get();
            foreach ($types as $row) {
                $price = ($kms * 2 * Hyvikk::fare(strtolower(str_replace(" ", "", $row->vehicletype)) . '_std_fare')) - ((Hyvikk::extra('oneway_discount') * ($kms * 2 * Hyvikk::fare(strtolower(str_replace(" ", "", $row->vehicletype)) . '_std_fare'))) / 100);
                $tax = 0;
                if (Hyvikk::get('tax_charge') != "null") {
                    $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                    foreach ($taxes as $key => $val) {
                        $tax = $tax + $val;
                    }
                }
                $price = round($price, 2);
                $total_amount = round((($price * $tax) / 100) + $price, 2);
                $total_tax_charge = round((($price * $tax) / 100), 2);
                $details[] = array(
                    'price' => $price,
                    'type' => $row->displayname,
                    'per_km' => Hyvikk::get('currency') . Hyvikk::fare(strtolower(str_replace(" ", "", $row->vehicletype)) . '_std_fare') . "/km",
                    'type_id' => $row->id,
                    'total' => $price,
                    'tax_total' => $total_amount,
                    'total_tax_percent' => $tax,
                    'total_tax_charge_rs' => $total_tax_charge,
                );
            }
            $all_offers = RideOffers::where('source', 'like', '%' . $request->source_city . '%')->where('destination', 'like', '%' . $request->dest_city . '%')->where('type_id', $request->vehicletype_id)->where('valid_till', '>=', date('Y-m-d H:i:s'))->where('booking_id', 0)->get();
            $offers = array();
            foreach ($all_offers as $offer) {
                $offers[] = array(
                    'offer_id' => $offer->id,
                    'source' => $offer->source,
                    'destination' => $offer->destination,
                    'distance' => $offer->distance,
                    'timing' => $offer->timing,
                    'valid_till' => date('Y-m-d H:i:s', strtotime($offer->valid_till)),
                    'vehicle_id' => $offer->vehicle_id,
                    'vehicle' => $offer->vehicle->maker->make . '-' . $offer->vehicle->vehiclemodel->model . '-' . $offer->vehicle->license_plate,
                    'base_fare' => Hyvikk::fare(strtolower(str_replace(' ', '', $offer->vehicle->types->vehicletype)) . '_base_fare'),
                    'total' => $offer->total,
                    'tax_total' => $offer->tax_total,
                    'total_tax_percent' => $offer->total_tax_percent,
                    'total_tax_charge_rs' => $offer->total_tax_charge_rs,
                    'driver_amount' => round($offer->total - $offer->company_commission, 2), // show this amount to driver everywhere
                    'company_commission' => $offer->company_commission,
                    'commission' => $offer->commission . "%",
                    'company_charges' => $offer->company_charges,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = array('packages' => $details, 'offers' => $offers);
        }
        return $data;
    }
    public function packages(Request $request)
    {
        $packages = PackagesModel::withTrashed()->get();
        if (isset($request->timestamp)) {
            $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
            $packages = PackagesModel::where("updated_at", ">", $time)->withTrashed()->get();
        }
        $details = array();
        $tax = 0;
        if (Hyvikk::get('tax_charge') != "null") {
            $taxes = json_decode(Hyvikk::get('tax_charge'), true);
            foreach ($taxes as $key => $val) {
                $tax = $tax + $val;
            }
        }
        foreach ($packages as $package) {
            if ($package->type->icon??'' != null) {
                $image = asset('uploads/' . $package->type->icon);
            } 
            else {
                $image = asset("assets/images/vehicle.jpeg");
            }
            // $color = "";
            // $code = "";
            // if ($package->vehicle->color_id) {
            //     $color = $package->vehicle->vehiclecolor->color;
            //     $code = $package->vehicle->vehiclecolor->code;
            // }
            $tax_total = ($package->package_rate * $tax) / 100 + $package->package_rate;
            $udfs = json_decode(Hyvikk::get('tax_charge'));
            $tax_charges = array();
            if ($udfs != null) {
                foreach ($udfs as $key => $value) {
                    $tax_charges[$key] = round(($package->package_rate * $value) / 100, 2);
                }
            }
            $details[] = array(
                'package_id' => $package->id,
                // 'vehicle_make' => $package->vehicle->maker->make,
                // 'vehicle_model' => $package->vehicle->vehiclemodel->model,
                // 'vehicle_number' => $package->vehicle->license_plate,
                'hourly_rate' => $package->hourly_rate,
                'km_rate' => $package->km_rate,
                'image' => $image,
                'vehicle_type' => $package->type->displayname??'',
                // 'vehicle_color' => $color,
                'package_hours' => $package->package_hours,
                'package_rate' => $package->package_rate,
                // 'color_code' => $code,
                'tax' => $tax . "%",
                'total_amount' => round($tax_total, 2),
                //'taxes' => $taxes,
                'tax_charges' => $tax_charges,
                'seats' => ($package->vehicle->types->seats)??"",
                'timestamp' => date('Y-m-d H:i:s', strtotime($package->updated_at)),
                "delete_status" => (isset($package->deleted_at)) ? 1 : 0,
            );
        }
        $data['success'] = "1";
        $data['message'] = "Data fetched!";
        $data['data'] = $details;
        return $data;
    }
    public function apply_coupon(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'coupon_code' => 'required|exists:coupons,code,code,' . $request->coupon_code,
            'driver_allowance' => 'nullable|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $check = CouponModel::where('code', $request->coupon_code)->exists();
            if ($check) {
                $coupon = CouponModel::where('code', $request->coupon_code)->first();
                if ($coupon->type == 1) {
                    // percentage
                    $total_amount = round($request->amount - ($request->amount * $coupon->value) / 100, 2);
                    $discount_amount = round((($request->amount * $coupon->value) / 100), 2);
                } else {
                    // amount
                    $total_amount = $request->amount - $coupon->value;
                    $discount_amount = $coupon->value;
                }
                // tax calculation
                if ($request->driver_allowance) {
                    $subtotal = $request->driver_allowance + $total_amount;
                } else {
                    $subtotal = $total_amount;
                }
                $count = 0;
                $tax_charges = array();
                $tax_percent = array();
                if (Hyvikk::get('tax_charge') != "null") {
                    $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                    foreach ($taxes as $key => $val) {
                        $count = $count + $val;
                        $tax_charges[$key] = round(($subtotal * $val) / 100, 2);
                        $tax_percent[$key] = $val;
                    }
                }
                $tax_total = round((($subtotal * $count) / 100) + $subtotal, 2);
                $total_tax_percent = $count;
                $total_tax_charge_rs = round(($subtotal * $count) / 100, 2);
                $data['success'] = "1";
                $data['message'] = "Coupon Applied successfully!";
                $data['data'] = array(
                    'discount_amount' => $discount_amount,
                    'ride_amount' => $total_amount,
                    'subtotal' => $subtotal,
                    'tax_total' => $tax_total,
                    'total_tax_percent' => $total_tax_percent,
                    'total_tax_charge_rs' => $total_tax_charge_rs,
                    'tax_charges' => $tax_charges,
                    'tax_percent' => $tax_percent,
                    'coupon_id' => $coupon->id,
                );
            } else {
                $data['success'] = "0";
                $data['message'] = "The selected coupon code is invalid.";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function fare_calculation(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'total_kms' => 'required|numeric',
            'vehicletype' => 'required|exists:vehicle_types,vehicletype,vehicletype,' . $request->vehicletype,
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $km_base = Hyvikk::fare(strtolower(str_replace(' ', '', $request->vehicletype)) . '_base_km');
            $base_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $request->vehicletype)) . '_base_fare');
            $std_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $request->vehicletype)) . '_std_fare');
            $base_km = Hyvikk::fare(strtolower(str_replace(' ', '', $request->vehicletype)) . '_base_km');
            if ($request->total_kms <= $km_base) {
                $total_fare = $base_fare;
            } else {
                $total_fare = round($base_fare + (($request->total_kms - $km_base) * $std_fare), 2);
            }
            // calculate tax charges
            $count = 0;
            if (Hyvikk::get('tax_charge') != "null") {
                $taxes = json_decode(Hyvikk::get('tax_charge'), true);
                foreach ($taxes as $key => $val) {
                    $count = $count + $val;
                }
            }
            $tax_total = round((($total_fare * $count) / 100) + $total_fare, 2);
            $total_tax_percent = $count;
            $total_tax_charge_rs = round(($total_fare * $count) / 100, 2);
            $data['success'] = "1";
            $data['message'] = "Fare calculated successfully!";
            $data['data'] = array(
                'total_amount' => $tax_total,
                'total_tax_percent' => $total_tax_percent,
                'total_tax_charge_rs' => $total_tax_charge_rs,
                'ride_amount' => $total_fare,
                'base_fare' => $base_fare,
                'base_km' => $base_km,
                'fare_per_km' => $std_fare,
            );
        }
        return $data;
    }
    public function round_trip_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            // 'booking_option' => 'required',
            'booking_type' => 'required|integer', //0 => book now, 1 => book later
            'source' => 'required',
            'destination' => 'required',
            'user_id' => 'required|integer',
            // 'journey_date' => 'required',
            // 'journey_time' => 'required',
            'journey_datetime' => 'required',
            'total_kms' => 'required|numeric',
            // 'amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'total_tax_percent' => 'required|numeric',
            'total_tax_charge_rs' => 'required|numeric',
            'ride_amount' => 'required|numeric',
            'no_of_persons' => 'required|integer',
            'vehicle_typeid' => 'required|integer',
            'discount_amount' => 'nullable|numeric',
            'coupon_id' => 'nullable|integer',
            'return_datetime' => 'required',
            'driver_allowance' => 'required|numeric',
            'subtotal' => 'required|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::create([
                'customer_id' => $request->user_id,
                'user_id' => $request->user_id,
                'pickup_addr' => $request->source,
                'dest_addr' => $request->destination,
                'status' => 0,
                'travellers' => $request->no_of_persons,
                'is_booked' => 1,
                'pickup' => date('Y-m-d H:i:s', strtotime($request->journey_datetime)),
                // 'dropoff' => date('Y-m-d H:i:s', strtotime($request->return_datetime)),
            ]);
            if ($request->coupon_id) {
                $booking->coupon_id = $request->coupon_id;
                $booking->discount_amount = $request->discount_amount;
            }
            // $booking->source_lat = $request->source_lat;
            // $booking->source_long = $request->source_long;
            // $booking->dest_lat = $request->dest_lat;
            // $booking->dest_long = $request->dest_long;
            $booking->city = $request->city;
            $booking->vehicle_typeid = $request->vehicle_typeid;
            $booking->booking_option = "RoundTrip";
            $booking->journey_date = date('d-m-Y', strtotime($request->journey_datetime));
            $booking->journey_time = date('H:i:s', strtotime($request->journey_datetime));
            $booking->return_date = date('d-m-Y', strtotime($request->return_datetime));
            $booking->return_time = date('H:i:s', strtotime($request->return_datetime));
            $booking->booking_type = $request->booking_type;
            $booking->accept_status = 0; // 0 = yet to accept, 1 = accept
            $booking->ride_status = null;
            $booking->total_kms = $request->total_kms;
            $booking->approx_timetoreach = $request->approx_timetoreach;
            $booking->driving_time = $request->approx_timetoreach;
            $booking->ride_amount = $request->ride_amount;
            $booking->driver_allowance = $request->driver_allowance;
            $driver_amount = round($request->subtotal - ($request->subtotal * Hyvikk::extra('general_company_commission')) / 100, 2);
            $booking->setMeta([
                'customerId' => $request->user_id,
                'mileage' => $request->total_kms,
                'date' => date('Y-m-d'),
                'total' => $request->subtotal,
                'total_kms' => $request->total_kms,
                'tax_total' => $request->total_amount,
                'total_tax_percent' => $request->total_tax_percent,
                'total_tax_charge_rs' => $request->total_tax_charge_rs,
                'driver_amount' => $driver_amount,
                'general_company_commission' => Hyvikk::extra('general_company_commission'),
            ]);
            $booking->save();
            // browser notification
            $this->push_notification($booking->id);
            $this->send_sms($booking->id);
            // send notification to drivers
            $this->booking_notification($booking->id, $booking->vehicle_typeid, $booking->city, 'RoundTrip');
            $this->request_receive_mail($booking->id);
            $row = Bookings::find($booking->id);
            // NotifyVendor::status_notification($booking->id, "pending_booking_notification");
            $record = array(
                'id' => $row->id,
                'customer_id' => $row->customer_id,
                'vehicle_id' => $row->vehicle_id,
                'user_id' => $row->user_id,
                'pickup' => $row->pickup,
                'dropoff' => $row->dropoff,
                'pickup_addr' => $row->pickup_addr,
                'dest_addr' => $row->dest_addr,
                'travellers' => $row->travellers,
                'status' => $row->status,
                'driver_id' => $row->driver_id,
                'note' => $row->note,
                'is_booked' => $row->is_booked,
                'custom_approved' => $row->custom_approved,
                "meta_details" => $row->getMeta(),
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                'customer_name' => $row->customer->name,
                'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->maker->make . "-" . $row->vehicle->vehiclemodel->model : "",
                'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                "return_date" => $row->return_date,
                "return_time" => $row->return_time,
                "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
            );
            $data['success'] = "1";
            $data['message'] = "Booking added successfully!";
            $data['data'] = $record;
        }
        return $data;
    }
    public function new_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_option' => 'required',
            'booking_type' => 'required|integer', //0 => book now, 1 => book later
            'source' => 'required',
            'destination' => 'required',
            'user_id' => 'required|integer',
            // 'journey_date' => 'required',
            // 'journey_time' => 'required',
            'journey_datetime' => 'required',
            'total_kms' => 'required|numeric',
            // 'amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'total_tax_percent' => 'required|numeric',
            'total_tax_charge_rs' => 'required|numeric',
            'ride_amount' => 'required|numeric',
            'no_of_persons' => 'required|integer',
            'vehicle_typeid' => 'required|integer',
            'discount_amount' => 'nullable|numeric',
            'coupon_id' => 'nullable|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::create([
                'customer_id' => $request->user_id,
                'user_id' => $request->user_id,
                'pickup_addr' => $request->source,
                'dest_addr' => $request->destination,
                'status' => 0,
                'travellers' => $request->no_of_persons,
                'is_booked' => 1,
                'pickup' => date('Y-m-d H:i:s', strtotime($request->journey_datetime)),
            ]);
            if ($request->coupon_id) {
                $booking->coupon_id = $request->coupon_id;
                $booking->discount_amount = $request->discount_amount;
            }
            // $booking->source_lat = $request->source_lat;
            // $booking->source_long = $request->source_long;
            // $booking->dest_lat = $request->dest_lat;
            // $booking->dest_long = $request->dest_long;
            $booking->city = $request->city;
            $booking->vehicle_typeid = $request->vehicle_typeid;
            $booking->booking_option = $request->booking_option;
            $booking->journey_date = date('d-m-Y', strtotime($request->journey_datetime));
            $booking->journey_time = date('H:i:s', strtotime($request->journey_datetime));
            $booking->booking_type = $request->booking_type;
            $booking->accept_status = 0; // 0 = yet to accept, 1 = accept
            $booking->ride_status = null;
            $booking->total_kms = $request->total_kms;
            $booking->approx_timetoreach = $request->approx_timetoreach;
            $booking->driving_time = $request->approx_timetoreach;
            $driver_amount = null;
            if ($request->booking_option == "Local") {
                $driver_amount = round($request->ride_amount - ($request->ride_amount * Hyvikk::extra('general_company_commission')) / 100, 2);
            }
            if ($request->booking_option == "OneWay") {
                $driver_amount = round($request->ride_amount - (($request->ride_amount * Hyvikk::extra('general_company_commission')) / 100) - Hyvikk::extra('general_company_charges'), 2);
            }
            $booking->setMeta([
                'customerId' => $request->user_id,
                'mileage' => $request->total_kms,
                'date' => date('Y-m-d'),
                'total' => $request->ride_amount,
                'total_kms' => $request->total_kms,
                'tax_total' => $request->total_amount,
                'total_tax_percent' => $request->total_tax_percent,
                'total_tax_charge_rs' => $request->total_tax_charge_rs,
                'driver_amount' => $driver_amount,
                'general_company_commission' => Hyvikk::extra('general_company_commission'),
                'general_company_charges' => ($request->booking_option == "OneWay") ? Hyvikk::extra('general_company_charges') : 0,
            ]);
            $booking->save();
            // browser notification
            $this->push_notification($booking->id);
            $this->send_sms($booking->id);
            // send notification to drivers
            $this->booking_notification($booking->id, $booking->vehicle_typeid, $booking->city, $booking->booking_option);
            $this->request_receive_mail($booking->id);
            $row = Bookings::find($booking->id);
            // NotifyVendor::status_notification($booking->id, "pending_booking_notification");
            $record = array(
                'id' => $row->id,
                'customer_id' => $row->customer_id,
                'vehicle_id' => $row->vehicle_id,
                'user_id' => $row->user_id,
                'pickup' => $row->pickup,
                'dropoff' => $row->dropoff,
                'pickup_addr' => $row->pickup_addr,
                'dest_addr' => $row->dest_addr,
                'travellers' => $row->travellers,
                'status' => $row->status,
                'driver_id' => $row->driver_id,
                'note' => $row->note,
                'is_booked' => $row->is_booked,
                'custom_approved' => $row->custom_approved,
                "meta_details" => $row->getMeta(),
                'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                'customer_name' => $row->customer->name,
                'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->maker->make . "-" . $row->vehicle->vehiclemodel->model : "",
                'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                "return_date" => $row->return_date,
                "return_time" => $row->return_time,
                "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
            );
            $data['success'] = "1";
            $data['message'] = "Booking added successfully!";
            $data['data'] = $record;
        }
        return $data;
    }
    public function send_sms($id)
    {
    }
    public function send_sms1($id)
    {
        $booking = Bookings::find($id);
        $customer_msg = "Your Booking Req. %23" . $booking->id . " has been Received. Our Support Team will contact you soon. Thank You - MP Cab";
        $admin_msg = "New Ride Request %23" . $booking->id . " : " . $booking->booking_option . ", From - " . explode(",", $booking->pickup_addr)[0] . ", To - " . explode(",", $booking->dest_addr)[0] . " ON " . date('M d,Y', strtotime($booking->journey_date)) . " " . date('g:ia', strtotime($booking->journey_time)) . ", Cus Mobno: " . $booking->customer->mobno . " | MPCab";
        $url = "http://msg.pnpuniverse.com/api/sendhttp.php?authkey=324488AdmUuZL1Gdq5fcdca22P1&mobiles=" . $booking->customer->mobno . "&message=" . str_replace(" ", "+", $customer_msg) . "&sender=MPCABZ&route=4&country=91&DLT_TE_ID=1207161820890135476";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . "/cookies.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . "/cookies.txt");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);
        $url1 = "http://msg.pnpuniverse.com/api/sendhttp.php?authkey=324488AdmUuZL1Gdq5fcdca22P1&mobiles=9826701177&message=" . str_replace(" ", "+", $admin_msg) . "&sender=MPCABZ&route=4&country=91&DLT_TE_ID=1207161866320887948";
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, $url1);
        curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch1, CURLOPT_COOKIEJAR, getcwd() . "/cookies.txt");
        curl_setopt($ch1, CURLOPT_COOKIEFILE, getcwd() . "/cookies.txt");
        curl_setopt($ch1, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_HEADER, false);
        curl_exec($ch1);
        curl_close($ch1);
        // dd($admin_msg, $customer_msg);
    }
    public function push_notification($id)
    {
        $booking = Bookings::find($id);
        $auth = array(
            'VAPID' => array(
                'subject' => 'Alert about new post',
                'publicKey' => 'BKt+swntut+5W32Psaggm4PVQanqOxsD5PRRt93p+/0c+7AzbWl87hFF184AXo/KlZMazD5eNb1oQVNbK1ti46Y=',
                'privateKey' => 'NaMmQJIvddPfwT1rkIMTlgydF+smNzNXIouzRMzc29c=', // in the real world, this would be in a secret file
            ),
        );
        // $select1 = DB::table('push_notification')->select('*')->whereIn('user_type', ["S", "O", "V"])->get()->toArray();
        // if ($booking->booking_option == "offer request") {
        //     $select1 = DB::table('push_notification')->select('*')->whereIn('user_type', ["S", "O"])->get()->toArray();
        // }
        $select1 = DB::table('push_notification')->select('*')->whereIn('user_type', ["S", "O"])->get()->toArray();
        $webPush = new WebPush($auth);
        foreach ($select1 as $fetch) {
            $sub = Subscription::create([
                'endpoint' => $fetch->endpoint, // Firefox 43+,
                'publicKey' => $fetch->publickey, // base 64 encoded, should be 88 chars
                'authToken' => $fetch->authtoken, // base 64 encoded, should be 24 chars
                'contentEncoding' => $fetch->contentencoding,
            ]);
            $user = User::find($fetch->user_id);
            $title = __('fleet.new_booking');
            // $body = __('fleet.customer') . ": " . $booking->customer->name . ", " . __('fleet.pickup') . ": " . date('d/m/Y', strtotime($booking->journey_date)) . " " . date('g:i A', strtotime($booking->journey_time)) . ", " . __('fleet.pickup_addr') . ": " . $booking->pickup_addr . ", " . __('fleet.dropoff_addr') . ": " . $booking->dest_addr;
            // if ($booking->booking_option == "Rental") {
            //     $body = __('fleet.customer') . ": " . $booking->customer->name . ", " . __('fleet.pickup') . ": " . date('d/m/Y', strtotime($booking->journey_date)) . " " . date('g:i A', strtotime($booking->journey_time)) . ", " . __('fleet.pickup_addr') . ": " . $booking->pickup_addr;
            // }
            $body = 'New Booking Request[' . $booking->booking_option . '] By "' . $booking->customer->name . '" From Address : "' . $booking->pickup_addr . '" On Journey Date Time :' . date('d/m/Y', strtotime($booking->journey_date)) . " " . date('g:i A', strtotime($booking->journey_time));
            $url = url('admin/bookings/' . $booking->id . '/edit');
            if ($booking->booking_option == "offer request") {
                $url = url('admin/view-custom-ride-request/' . $booking->id);
            }
            $array = array(
                'title' => $title ?? "",
                'body' => $body ?? "",
                'img' => url('assets/images/' . Hyvikk::get('icon_img')),
                'url' => $url ?? url('admin/'),
            );
            $object = json_encode($array);
            if ($fetch->user_id == $user->id) {
                $test = $webPush->sendNotification($sub, $object);
            }
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
            }
        }
    }
    public function reject_req_notification($id, $type_id, $city, $trip_type)
    {
        $booking = Bookings::find($id);
        $user_details = new \stdClass();
        $fare_details = array(); //vehicletype,time,km,total ride amount, ride amount,company charges ,company commision,driver earnings
        $tax_total = $booking->tax_total;
        $ride_amount = $booking->total;
        // $driver_amount = $booking->tax_total;
        $driver_amount = $booking->total;
        $company_charges = $booking->vendor_fee;
        $company_commission = 0;
        if ($booking->accept_status == 0) {
            $accept_status = "pending";
        }
        if ($booking->accept_status == 1) {
            $accept_status = "accepted";
        }
        if ($booking->ride_status == "Cancelled") {
            $accept_status = "cancelled";
        }
        $fare_details = array(
            'vehicle_type' => ($booking->vehicle_typeid != null) ? $booking->types->displayname : null,
            'time' => $booking->driving_time,
            'total_kms' => $booking->total_kms . "kms",
            'total_ride_amount' => $tax_total,
            'ride_amount' => $ride_amount,
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'driver_amount' => $driver_amount,
            'company_charges' => $company_charges,
            'company_commission' => $company_commission,
        );
        $data['success'] = 1;
        $data['key'] = "rejection_notification";
        $data['message'] = 'Data Received.';
        $data['title'] = "Ride Request (" . $trip_type . ") Rejected by Admin";
        $data['description'] = "Admin rejected custom ride request";
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['data'] = array('riderequest_info' => array(
            'booking_option' => $booking->booking_option,
            'user_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'source_address' => $booking->pickup_addr,
            'dest_address' => $booking->dest_addr,
            'book_date' => date('d-m-Y', strtotime($booking->created_at)),
            'book_time' => date('H:i:s', strtotime($booking->created_at)),
            'journey_date' => date('d-m-Y', strtotime($booking->journey_date)),
            'journey_time' => date('H:i:s', strtotime($booking->journey_time)),
            'accept_status' => $accept_status,
            'tax_total' => $booking->tax_total,
            'return_date' => ($booking->booking_option == "RoundTrip") ? date('d-m-Y', strtotime($booking->getMeta('return_date'))) : "",
            'return_time' => ($booking->booking_option == "RoundTrip") ? date('H:i:s', strtotime($booking->getMeta('return_time'))) : "",
            // new 05-10-2020
            'fare_details' => $fare_details,
            'timetoreach' => $booking->approx_timetoreach,
            'amount' => $booking->tax_total,
            'total_kms' => $booking->total_kms,
            'journey_date' => $booking->journey_date . " " . $booking->journey_time,
            // 'journey_time' => $booking->journey_time,
            'customer_name' => $booking->customer->name,
            'customer_mobile' => $booking->customer->mobno,
            'booked_on' => date('d-m-Y', strtotime($booking->created_at)),
            "ride_amount" => round($booking->total, 2),
            "vendor_fee" => ($booking->vendor_fee) ? $booking->vendor_fee : 0,
            // "driver_amount" => round($booking->tax_total, 2),
            "driver_amount" => round($booking->total, 2),
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'admin_approved' => $booking->custom_approved,
            'driver_id' => ($booking->driver_id) ? $booking->driver_id : "",
            'user_details' => $user_details,
            'travellers' => $booking->travellers,
        ),
        );
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }
        // PushNotification::app('appNameAndroid')
        //     ->to("fUuFZmuOQcWoYVkJIY2NAI:APA91bH-boKDFv-EIxMf8k9fOzWbpRTRG7vXEtzCU_AABuUodLm3GliNAlN37K5PMUuPKgAmmkLNMZ17XyP7jZZ61BIsYipq9fM0oXr3dVzCyZ3sqpWA62SjdudqVvbRpSDOKyUo2tkn")
        //     ->send($data);
        // dd($data);
        // $drivers = User::where('user_type', 'D')->get();
        $d = User::find($booking->driver_id);
        if ($d->getMeta('fcm_id')) {
            PushNotification::app('appNameAndroid')
                ->to($d->getMeta('fcm_id'))
                ->send($data);
        }
        // dd($d, $data);
    }
    public function reject_approval(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $rejection_list = array();
            if ($booking->rejection_list != null) {
                $rejection_list = json_decode($booking->rejection_list);
            }
            array_push($rejection_list, $booking->driver_id);
            $booking->accept_status = 0;
            $booking->ride_status = null;
            $booking->custom_approved = 0;
            $booking->rejection_list = json_encode($rejection_list);
            $booking->save();
            $this->reject_req_notification($booking->id, $booking->vehicle_typeid, $booking->city, 'offer request');
            $booking->driver_id = null;
            $booking->vehicle_id = null;
            $booking->is_booked = 0;
            $booking->save();
            $data['success'] = "1";
            $data['message'] = "Custom Ride Request has been Rejected successfully!";
            $data['data'] = array(
                'booking_id' => $booking->id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                'custom_approved' => $booking->custom_approved,
            );
        }
        return $data;
    }
    public function confirm_approval(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $booking->custom_approved = 1;
            $booking->ride_status = "Upcoming";
            $booking->save();
            $this->approved_req_notification($booking->id, $booking->vehicle_typeid, $booking->city, 'offer request');
            $data['success'] = "1";
            $data['message'] = "Custom Ride Request has been Approved successfully!";
            $data['data'] = array(
                'booking_id' => $booking->id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                'custom_approved' => $booking->custom_approved,
            );
        }
        return $data;
    }
    public function approved_req_notification($id, $type_id, $city, $trip_type)
    {
        $booking = Bookings::find($id);
        $user_details = array();
        $url = null;
        if ($booking->customer->getMeta('profile_pic')) {
            $url = asset('uploads/' . $booking->customer->getMeta('profile_pic'));
        }
        $user_details = array('user_id' => $booking->customer_id, 'user_name' => $booking->customer->name, 'mobno' => $booking->customer->getMeta('mobno'), 'profile_pic' => $url);
        $fare_details = array(); //vehicletype,time,km,total ride amount, ride amount,company charges ,company commision,driver earnings
        $tax_total = $booking->tax_total;
        $ride_amount = $booking->total;
        // $driver_amount = $booking->tax_total;
        $driver_amount = $booking->total;
        $company_charges = $booking->vendor_fee;
        $company_commission = 0;
        if ($booking->accept_status == 0) {
            $accept_status = "pending";
        }
        if ($booking->accept_status == 1) {
            $accept_status = "accepted";
        }
        if ($booking->ride_status == "Cancelled") {
            $accept_status = "cancelled";
        }
        $fare_details = array(
            'vehicle_type' => ($booking->vehicle_typeid != null) ? $booking->types->displayname : null,
            'time' => $booking->driving_time,
            'total_kms' => $booking->total_kms . "kms",
            'total_ride_amount' => $tax_total,
            'ride_amount' => $ride_amount,
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'driver_amount' => $driver_amount,
            'company_charges' => $company_charges,
            'company_commission' => $company_commission,
            "base_fare" => null,
            "discount_amount" => null,
            "commission_rs" => null,
        );
        $data['success'] = 1;
        $data['key'] = "custom_req_notification";
        $data['message'] = 'Data Received.';
        //$data['title'] = "Ride Request (" . $trip_type . ") Approved by Admin";
        $data['title'] = "New Offer Request (" . $trip_type . ") Offer";
        $data['description'] = "Admin approved custom ride request";
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['data'] = array('riderequest_info' => array(
            'booking_option' => $booking->booking_option,
            'user_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'source_address' => $booking->pickup_addr,
            'dest_address' => $booking->dest_addr,
            'book_date' => date('d-m-Y', strtotime($booking->created_at)),
            'book_time' => date('H:i:s', strtotime($booking->created_at)),
            // 'journey_date' => date('d-m-Y', strtotime($booking->journey_date)),
            // 'journey_time' => date('H:i:s', strtotime($booking->journey_time)),
            'accept_status' => $accept_status,
            'tax_total' => $booking->tax_total,
            'return_date' => ($booking->booking_option == "RoundTrip") ? date('d-m-Y', strtotime($booking->getMeta('return_date'))) : "",
            'return_time' => ($booking->booking_option == "RoundTrip") ? date('H:i:s', strtotime($booking->getMeta('return_time'))) : "",
            // new 05-10-2020
            'fare_details' => $fare_details,
            'timetoreach' => $booking->approx_timetoreach,
            'amount' => $booking->tax_total,
            'total_kms' => $booking->total_kms,
            'journey_date' => $booking->journey_date . " " . $booking->journey_time,
            'journey_time' => $booking->journey_time,
            'customer_name' => $booking->customer->name,
            'customer_mobile' => $booking->customer->mobno,
            'booked_on' => date('d-m-Y', strtotime($booking->created_at)),
            "ride_amount" => round($booking->total, 2),
            "vendor_fee" => ($booking->vendor_fee) ? $booking->vendor_fee : 0,
            // "driver_amount" => round($booking->tax_total, 2),
            "driver_amount" => round($booking->total, 2),
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'admin_approved' => "" . $booking->custom_approved,
            'driver_id' => ($booking->driver_id) ? $booking->driver_id : "",
            'user_details' => $user_details,
            'travellers' => $booking->travellers,
        ),
        );
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }
        // PushNotification::app('appNameAndroid')
        //     ->to("fUuFZmuOQcWoYVkJIY2NAI:APA91bH-boKDFv-EIxMf8k9fOzWbpRTRG7vXEtzCU_AABuUodLm3GliNAlN37K5PMUuPKgAmmkLNMZ17XyP7jZZ61BIsYipq9fM0oXr3dVzCyZ3sqpWA62SjdudqVvbRpSDOKyUo2tkn")
        //     ->send($data);
        // dd($data);
        // $drivers = User::where('user_type', 'D')->get();
        $d = User::find($booking->driver_id);
        if ($d->getMeta('fcm_id')) {
            PushNotification::app('appNameAndroid')
                ->to($d->getMeta('fcm_id'))
                ->send($data);
        }
        // dd($d, $data);
    }
    public function reject_custom_req(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $booking->is_approved = 2;
            $booking->save();
            $data['success'] = "1";
            $data['message'] = "Custom Ride Request has been Rejected successfully!";
            $data['data'] = array(
                'booking_id' => $booking->id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                'is_approved' => $booking->is_approved,
            );
        }
        return $data;
    }
    public function accept_custom_req(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            $booking->is_approved = 1;
            $booking->user_id = $request->user_id;
            $booking->save();
            // \Artisan::call('queue:restart');
            // \Artisan::call('queue:work');
            // \Artisan::call('queue:flush');
            // \Artisan::call('queue:forget');
            $this->run_queue($booking->id);
            // return $booking->id;
            $data['success'] = "1";
            $data['message'] = "Custom offer accepted successfully!";
            $data['data'] = array(
                'booking_id' => $booking->id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                'is_approved' => $booking->is_approved,
            );
        }
        return $data;
    }
    public function run_queue($id)
    {
        // dd($id);
        $booking = Bookings::find($id);
        $job = new SendPushNotification($booking->id, $booking->vehicle_typeid, $booking->city, 'offer request');
        $send = $this->dispatch($job);
        // dd($send);
        // return $id;
    }
    public function custom_offers_approval(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $records = array();
            $bookings = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')->where('bookings_meta.value', '=', 'offer request')
            ->where('custom_approved', 0)
            ->where('customer_id', '!=', $request->user_id)
            ->where('driver_id', '!=', null);
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $bookings = $bookings->where("bookings.updated_at", ">", $time);
            }
            $bookings = $bookings->orderBy('id', 'desc')->withTrashed()->get();
            foreach ($bookings as $row) {
                if (strtotime($row->journey_date . " " . $row->journey_time) >= strtotime("now")) {
                    $records[] = array(
                        'id' => $row->id,
                        'customer_id' => $row->customer_id,
                        'vehicle_id' => $row->vehicle_id,
                        'user_id' => $row->user_id,
                        'pickup' => $row->pickup,
                        'dropoff' => $row->dropoff,
                        'pickup_addr' => $row->pickup_addr,
                        'dest_addr' => $row->dest_addr,
                        'travellers' => $row->travellers,
                        'status' => $row->status,
                        'driver_id' => $row->driver_id,
                        'note' => $row->note,
                        'is_booked' => $row->is_booked,
                        'custom_approved' => $row->custom_approved,
                        "meta_details" => $row->getMeta(),
                        'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                        'customer_name' => $row->customer->name,
                        'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->maker->make . "-" . $row->vehicle->vehiclemodel->model : "",
                        'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                        'customer_name' => ($row->customer_id) ? $row->customer->name : "",
                        'customer_phone' => ($row->customer_id) ? $row->customer->mobno : "",
                        "return_date" => $row->return_date,
                        "return_time" => $row->return_time,
                        "is_approved" => $row->is_approved,
                        "created_at" => date('d-m-Y H:i:s', strtotime($row->created_at)),
                        "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                        'vehicle_number' => ($row->vehicle_id) ? $row->vehicle->license_plate : "",
                        'driver_phone' => ($row->driver_id) ? $row->driver->phone : "",
                        "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                        "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                        "type_name" => ($row->vehicle_typeid) ? $row->types->displayname : null,
                    );
                }
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $records;
        }
        return $data;
    }
    public function custom_offers(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $records = array();
            $bookings = Bookings::meta()->where('bookings_meta.key', '=', 'booking_option')
            ->where('bookings_meta.value', '=', 'offer request')
            ->where('customer_id', '!=', $request->user_id)
            ->where('driver_id', null);
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $bookings = $bookings->where("bookings.updated_at", ">", $time);
            }
            $bookings = $bookings->orderBy('id', 'desc')->withTrashed()->get();
            foreach ($bookings as $row) {
                $records[] = array(
                    'id' => $row->id,
                    'customer_id' => $row->customer_id,
                    'vehicle_id' => $row->vehicle_id,
                    'user_id' => $row->user_id,
                    'pickup' => $row->pickup,
                    'dropoff' => $row->dropoff,
                    'pickup_addr' => $row->pickup_addr,
                    'dest_addr' => $row->dest_addr,
                    'travellers' => $row->travellers,
                    'status' => $row->status,
                    'driver_id' => $row->driver_id,
                    'note' => $row->note,
                    'is_booked' => $row->is_booked,
                    'custom_approved' => $row->custom_approved,
                    "meta_details" => $row->getMeta(),
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    'customer_name' => $row->customer->name,
                    'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->maker->make . "-" . $row->vehicle->vehiclemodel->model : "",
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    'customer_name' => ($row->customer_id) ? $row->customer->name : "",
                    'customer_phone' => ($row->customer_id) ? $row->customer->mobno : "",
                    "return_date" => $row->return_date,
                    "return_time" => $row->return_time,
                    "is_approved" => $row->is_approved,
                    "created_at" => date('d-m-Y H:i:s', strtotime($row->created_at)),
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                    "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                    "type_name" => ($row->vehicle_typeid) ? $row->types->displayname : null,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $records;
        }
        return $data;
    }
    public function add_offer(Request $request)
    {
        $validation = Validator::make($request->all(), [
            // add new vehicle
            // 'make_id' => 'required_if:vehicle_id,|nullable|integer',
            // 'model_id' => 'required_if:vehicle_id,|nullable|integer',
            'make_id' => 'required_if:vehicle_id,|nullable',
            'model_id' => 'required_if:vehicle_id,|nullable',
            'type_id' => 'required_if:vehicle_id,|nullable|integer',
            // 'color_id' => 'required_if:vehicle_id,|nullable|integer',
            'vehicle_number' => 'required_if:vehicle_id,|nullable',
            // 'vehicle_number' => 'required_if:vehicle_id,|nullable|unique:vehicles,license_plate',
            'vehicle_id' => 'required_if:make_id,|integer|nullable',
            'user_id' => 'required|integer', //vendor id
            'driver_id' => 'required|integer',
            'source' => 'required',
            'destination' => 'required',
            'valid_from' => 'required',
            'valid_till' => 'required',
            'distance' => 'required|numeric',
            'timing' => 'required',
            // charges
            'ride_amount' => 'required|numeric',
            'total' => 'required|numeric',
            'tax_percent' => 'required|numeric',
            'tax_charges' => 'required|numeric',
            'company_charges' => 'required|numeric',
            'company_commission' => 'required|numeric',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user = User::find($request->driver_id);
            // if vehicle already exists and offer expired then add that vehicle to offer
            $old_vehicle = VehicleModel::where('license_plate', $request->vehicle_number)->first();
            if ($old_vehicle && $request->vehicle_id == null) {
                $active_offers = RideOffers::where('vehicle_id', $old_vehicle->id)->where('valid_till', '>=', date('Y-m-d H:i:s'))->get();
                if ($active_offers->count() > 0) {
                    $data['success'] = "0";
                    $data['message'] = "vehicle already booked for offer";
                    $data['data'] = null;
                    return $data;
                } else {
                    $v_id = $old_vehicle->id;
                    $type_id = $old_vehicle->type_id;
                }
            } else {
                if ($request->make_id) {
                    $old_vehicle = VehicleModel::where('license_plate', $request->vehicle_number)->first();
                    if ($old_vehicle) {
                        $data['success'] = "0";
                        $data['message'] = "vehicle number already taken";
                        $data['data'] = null;
                        return $data;
                    }
                    $make = VehicleMake::where('make', $request->make_id)->first();
                    if ($make != null) {
                        $make_id = $make->id;
                        $model = Vehicle_Model::where('make_id', $make_id)->where('model', $request->model_id)->first();
                        if ($model != null) {
                            $model_id = $model->id;
                        } else {
                            $model_id = Vehicle_Model::create([
                                'make_id' => $make_id,
                                'model' => $request->model_id,
                            ])->id;
                        }
                    } else {
                        $make_id = VehicleMake::create(['make' => $request->make_id])->id;
                        $model_id = Vehicle_Model::create([
                            'make_id' => $make_id,
                            'model' => $request->model_id,
                        ])->id;
                    }
                    // dd($make_id, $model_id);
                    $vehicle = VehicleModel::create([
                        'make_id' => $make_id,
                        'model_id' => $model_id,
                        // 'make_id' => $request->make_id,
                        // 'model_id' => $request->model_id,
                        'type_id' => $request->type_id,
                        'color_id' => $request->color_id,
                        'user_id' => $request->user_id,
                        'license_plate' => $request->vehicle_number,
                        'in_service' => 1,
                    ]);
                    $v_id = $vehicle->id;
                    $type_id = $request->type_id;
                } else {
                    $active_offers = RideOffers::where('vehicle_id', $request->vehicle_id)->where('valid_till', '>=', date('Y-m-d H:i:s'))->get();
                    if ($active_offers->count() > 0) {
                        $data['success'] = "0";
                        $data['message'] = "vehicle already booked for offer.";
                        $data['data'] = null;
                        return $data;
                    } else {
                        $v_id = $request->vehicle_id;
                        $vcle = VehicleModel::find($request->vehicle_id);
                        $type_id = $vcle->type_id;
                    }
                }
            }
            $vehicle = VehicleModel::find($v_id);
            $dname = str_replace(" ", "", $user->name);
            $uid = substr($dname, 0, 3) . "" . substr(str_replace(" ", "", $vehicle->license_plate), -7) . "" . date('dmy');
            // dd($uid);
            $offer = RideOffers::create([
                'uid' => $uid,
                'vendor_id' => $request->user_id,
                'source' => $request->source,
                'destination' => $request->destination,
                'vehicle_id' => $v_id,
                'valid_from' => $request->valid_from,
                'valid_till' => $request->valid_till,
                'user_id' => $request->driver_id,
                'distance' => $request->distance,
                'timing' => $request->timing,
                'total' => $request->ride_amount,
                'tax_total' => $request->total,
                'total_tax_percent' => $request->tax_percent,
                'total_tax_charge_rs' => $request->tax_charges,
                'type_id' => $type_id,
                'company_charges' => $request->company_charges,
                'company_commission' => $request->company_commission,
                'commission' => Hyvikk::extra('company_commission'),
            ]);
            $offer_details = array(
                'id' => $offer->id,
                'uid' => $offer->uid,
                'vendor_id' => $request->user_id,
                'source' => $request->source,
                'destination' => $request->destination,
                'vehicle_id' => $v_id,
                'valid_from' => $request->valid_from,
                'valid_till' => $request->valid_till,
                'user_id' => $request->driver_id,
                'distance' => $request->distance,
                'timing' => $request->timing,
                'total' => $request->ride_amount,
                'tax_total' => $request->total,
                'total_tax_percent' => $request->tax_percent,
                'total_tax_charge_rs' => $request->tax_charges,
                'type_id' => $type_id,
                'company_charges' => $request->company_charges,
                'company_commission' => $request->company_commission,
                'commission' => Hyvikk::extra('company_commission'),
                'timestamp' => date('Y-m-d H:i:s', strtotime($offer->updated_at)),
            );
            $vehicle = VehicleModel::find($v_id);
            $vehicle_details = array(
                'vehicle_id' => $vehicle->id,
                'make_id' => $vehicle->make_id,
                'model_id' => $vehicle->model_id,
                'color_id' => $vehicle->color_id,
                'type_id' => $vehicle->type_id,
                'vehicle_number' => $vehicle->license_plate,
                'group_id' => $vehicle->group_id,
                'user_id' => $vehicle->user_id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($vehicle->updated_at)),
            );
            $data['success'] = "1";
            $data['message'] = "Ride Offer added successfully!";
            $data['data'] = array(
                'offer' => $offer_details,
                'vehicle' => $vehicle_details,
            );
        }
        return $data;
    }
    public function transactions(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $records = array();
            $list = BookingPaymentsModel::whereNotNull('id');
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $list = $list->where("updated_at", ">", $time);
            }
            $list = $list->orderBy('id', 'desc')->get();
            foreach ($list as $row) {
                $records[] = array(
                    "id" => $row->id,
                    "customer" => $row->booking->customer->name,
                    "receipt_no" => $row->receipt_no,
                    "order_id" => $row->order_id,
                    "payment_id" => $row->payment_id,
                    "payment_status" => $row->payment_status,
                    "amount" => $row->amount,
                    "booking_id" => $row->booking_id,
                    "method" => ($row->booking->payment_method) ?? $row->method,
                    "sign" => $row->sign,
                    "reason" => $row->reason,
                    "timestamp" => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    "date" => date('Y-m-d', strtotime($row->created_at)),
                    "time" => date('g:i A', strtotime($row->created_at)),
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched!";
            $data['data'] = $records;
        }
        return $data;
    }
    public function update_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'booking_id' => 'required|integer',
            // 'customer_id' => 'required|integer',
            'pickup' => 'required|date',
            'dropoff' => 'required|date',
            // 'booking_option' => 'required|in:Local,RoundTrip,Rental,OneWay',
            'travellers' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'pickup_addr' => 'required',
            'dest_addr' =>  'required',
            'note' => 'nullable',
            // 'package_id' => 'required_if:booking_option,Rental',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->booking_id);
            if ($booking) {
                // $old_booking_option = $booking->booking_option;
                // $booking->booking_option = $request->booking_option;
                // $booking->package_id = $request->package_id;
                // $booking->route_id = $request->route_id;
                $booking->accept_status = 1;
                // $booking->is_booked = 1;
                $booking->vehicle_id = $request->vehicle_id;
                $booking->user_id = $request->user_id;
                $booking->driver_id = $request->driver_id;
                $booking->travellers = $request->travellers;
                $booking->pickup = $request->pickup;
                $booking->dropoff = $request->dropoff;
                $booking->pickup_addr = $request->pickup_addr;
                $booking->dest_addr = $request->dest_addr;
                if ($booking->ride_status == null && $request->vehicle_id && $request->driver_id) {
                    $booking->ride_status = "Upcoming";
                }
                $booking->note = $request->note;
                $booking->journey_date = date('d-m-Y', strtotime($request->pickup));
                $booking->journey_time = date('H:i:s', strtotime($request->pickup));
                $booking->udf = serialize($request->udf);
                $booking->save();
                if ($booking->vehicle_id) {
                    $vehicle_data = VehicleModel::find($booking->vehicle_id);
                    // dd($booking->vehicle_id);
                    // dd( $vehicle_data );
                    $booking->vehicle_typeid = $vehicle_data->type_id;
                    $booking->save();
                }
                if ($booking->booking_option == "RoundTrip") {
                    $booking->return_date = date('d-m-Y', strtotime($request->dropoff));
                    $booking->return_time = date('H:i:s', strtotime($request->dropoff));
                    $booking->save();
                }
                // if ($old_booking_option != $booking->booking_option) {
                //     // dd("new");
                //     if ($booking->booking_option == "Local") {
                //         $this->local_fare_calc($booking->id);
                //     }
                //     if ($booking->booking_option == "RoundTrip") {
                //         $this->round_fare_calculation($booking->id);
                //     }
                //     if ($booking->booking_option == "OneWay") {
                //         $this->oneway_calc($booking->id);
                //     }
                //     if ($booking->booking_option == "Rental") {
                //         $this->rental_calc($booking->id);
                //     }
                // }
                $booking = Bookings::find($booking->id);
                // dd($booking->getMeta());
                $record = array(
                    'id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'user_id' => $booking->user_id,
                    'pickup' => $booking->pickup,
                    'dropoff' => $booking->dropoff,
                    'pickup_addr' => $booking->pickup_addr,
                    'dest_addr' => $booking->dest_addr,
                    'travellers' => $booking->travellers,
                    'status' => $booking->status,
                    'driver_id' => $booking->driver_id,
                    'note' => $booking->note,
                    // 'is_booked' => $booking->is_booked,
                    'custom_approved' => $booking->custom_approved,
                    "meta_details" => $booking->getMeta(),
                    'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                    'customer_name' => $booking->customer->name,
                    'vehicle_name' => ($booking->vehicle_id) ? $booking->vehicle->make_name . "-" . $booking->vehicle->model_name : "",
                    'driver_name' => ($booking->driver_id) ? $booking->driver->name : "",
                    "return_date" => $booking->return_date,
                    "return_time" => $booking->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($booking->journey_date . " " . $booking->journey_time)),
                );
                $data['success'] = "1";
                $data['message'] = "Data updated!";
                $data['data'] = $record;
            } else {
                $data['success'] = "0";
                $data['message'] = "Invalid booking id passed.";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function delete_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            Bookings::find($request->booking_id)->delete();
            IncomeModel::where('income_id', $request->booking_id)->where('income_cat', 1)->delete();
            $data['success'] = "1";
            $data['message'] = "Booking deleted successfully!";
            $data['data'] = array('booking_id' => $request->booking_id, 'timestamp' => date('Y-m-d H:i:s'));
        }
        return $data;
    }
    public function cancel_booking(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'cancel_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $booking = Bookings::find($request->cancel_id);
            $booking->ride_status = "Cancelled";
            $booking->reason = $request->reason;
            $booking->save();
            // if booking->status != 1 then delete income record
            IncomeModel::where('income_id', $request->cancel_id)->where('income_cat', 1)->delete();
            $booking = Bookings::find($request->cancel_id);
                // dd($booking->getMeta());
                $record = array(
                    'id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'user_id' => $booking->user_id,
                    'pickup' => $booking->pickup,
                    'dropoff' => $booking->dropoff,
                    'pickup_addr' => $booking->pickup_addr,
                    'dest_addr' => $booking->dest_addr,
                    'travellers' => $booking->travellers,
                    'status' => $booking->status,
                    'driver_id' => $booking->driver_id,
                    'note' => $booking->note,
                    // 'is_booked' => $booking->is_booked,
                    'custom_approved' => $booking->custom_approved,
                    "meta_details" => $booking->getMeta(),
                    'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                    'customer_name' => $booking->customer->name,
                    'vehicle_name' => ($booking->vehicle_id) ? $booking->vehicle->make_name . "-" . $booking->vehicle->model_name : "",
                    'driver_name' => ($booking->driver_id) ? $booking->driver->name : "",
                    "return_date" => $booking->return_date,
                    "return_time" => $booking->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($booking->journey_date . " " . $booking->journey_time)),
                );
                $data['success'] = "1";
                $data['message'] = "Data updated!";
                $data['data'] = $record;
            }    
            return $data;
    }
    public function generate_invoice(Request $request)
    {
       // dd($request->all());
        $validation = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } 
        else 
        {
            $booking = Bookings::find($request->get("booking_id"));
            $booking->setMeta([
                'customerId' => $request->get('customerId'),
                'vehicleId' => $request->get('vehicleId'),
                'day' => $request->get('day'),
                'mileage' => $request->get('mileage'),
                'waiting_time' => $request->get('waiting_time'),
                'date' => $request->get('date'),
                'total' => round($request->get('total'), 2),
                'total_kms' => $request->get('mileage'),
                // 'ride_status' => 'Completed',
                'tax_total' => round($request->get('tax_total'), 2),
                'total_tax_percent' => round($request->get('total_tax_charge'), 2),
                'total_tax_charge_rs' => round($request->total_tax_charge_rs, 2),
            ]);
            $booking->save();
            $id = IncomeModel::create([
                "vehicle_id" => $request->get("vehicleId"),
                // "amount" => $request->get('total'),
                "amount" => $request->get('tax_total'),
                "user_id" => $request->get("customerId"),
                "date" => $request->get('date'),
                "mileage" => $request->get("mileage"),
                "income_cat" => $request->get("income_type"),
                "income_id" => $booking->id,
                "tax_percent" => $request->get('total_tax_charge'),
                "tax_charge_rs" => $request->total_tax_charge_rs,
            ])->id;
            BookingIncome::create(['booking_id' => $request->get("booking_id"), "income_id" => $id]);
            $xx = Bookings::whereId($request->get("booking_id"))->first();
            // $xx->status = 1;
            $xx->receipt = 1;
            $xx->save();
            if (Hyvikk::email_msg('email') == 1) {
                Mail::to($booking->customer->email)->send(new CustomerInvoice($booking));
            }
            $booking = Bookings::find($request->booking_id);
            // dd($booking->getMeta());
            $record = array(
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'vehicle_id' => $booking->vehicle_id,
                'user_id' => $booking->user_id,
                'pickup' => $booking->pickup,
                'dropoff' => $booking->dropoff,
                'pickup_addr' => $booking->pickup_addr,
                'dest_addr' => $booking->dest_addr,
                'travellers' => $booking->travellers,
                'status' => $booking->status,
                'driver_id' => $booking->driver_id,
                'note' => $booking->note,
                // 'is_booked' => $booking->is_booked,
                'custom_approved' => $booking->custom_approved,
                "meta_details" => $booking->getMeta(),
                'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                'customer_name' => $booking->customer->name,
                'vehicle_name' => ($booking->vehicle_id) ? $booking->vehicle->make_name . "-" . $booking->vehicle->model_name : "",
                'driver_name' => ($booking->driver_id) ? $booking->driver->name : "",
                "return_date" => $booking->return_date,
                "return_time" => $booking->return_time,
                "journey_date_time" => date('Y-m-d H:i:s', strtotime($booking->journey_date . " " . $booking->journey_time)),
            );
            $data['success'] = "1";
            $data['message'] = "Data updated!";
            $data['data'] = $record;
        }    
        return $data;
    }
    public function add_booking(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'pickup' => 'required|date',
            'dropoff' => 'required|date',
            // 'booking_option' => 'required|in:Local,RoundTrip,Rental,OneWay',
            'travellers' => 'required|integer',
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'pickup_addr' => 'required',
            'dest_addr' => 'required',
            'note' => 'nullable',
            // 'package_id' => 'required_if:booking_option,Rental',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $xx = $this->check_booking($request->pickup, $request->dropoff, $request->vehicle_id);
            if ($xx) {
                $id = Bookings::create([
                    'user_id' => $request->user_id,
                    'status' => 0,
                    'customer_id' => $request->customer_id,
                    'pickup' => date('Y-m-d H:i:s', strtotime($request->pickup)),
                    'dropoff' => date('Y-m-d H:i:s', strtotime($request->dropoff)),
                    'travellers' => $request->travellers,
                    'vehicle_id' => $request->vehicle_id,
                    'driver_id' => $request->driver_id,
                    'pickup_addr' => $request->pickup_addr,
                    'dest_addr' => $request->dest_addr,
                    'note' => $request->note,
                ])->id;
                $booking = Bookings::find($id);
                $booking->booking_option = $request->booking_option;
                $booking->package_id = $request->package_id;
                $booking->route_id = $request->route_id;
                // if ($request->package_id) {
                //     $package = PackagesModel::find($request->package_id);
                //     $booking->vehicle_id = $package->vehicle_id;
                // }
                // $dropoff = Carbon::parse($booking->dropoff);
                // $pickup = Carbon::parse($booking->pickup);
                // $diff = $pickup->diffInMinutes($dropoff);
                // $booking->duration = $diff;
                $booking->is_booked = 1; // is_booked = 0 => booking request by customer
                $booking->note = $request->note;
                $booking->accept_status = 1; //0=yet to accept, 1= accept
                $booking->ride_status = "Upcoming";
                // if ($booking->booking_option == "Local") {
                //     $booking->booking_type = 0; // 1 = book later, 0 = book now
                //     $booking->journey_date = date('d-m-Y');
                //     $booking->journey_time = date('H:i:s');
                // } else {
                $booking->booking_type = 1; // 1 = book later, 0 = book now
                $booking->journey_date = date('d-m-Y', strtotime($booking->pickup));
                $booking->journey_time = date('H:i:s', strtotime($booking->pickup));
                // }
                $booking->save();
                if ($booking->vehicle_id) {
                    $vehicle_data = VehicleModel::find($booking->vehicle_id);
                    // dd($vehicle_data);
                    $booking->vehicle_typeid = $vehicle_data->type_id;
                    $booking->save();
                }
                if ($booking->booking_option == "Local") {
                    $this->local_fare_calc($booking->id);
                }
                if ($booking->booking_option == "RoundTrip") {
                    $booking->return_date = date('d-m-Y', strtotime($request->dropoff));
                    $booking->return_time = date('H:i:s', strtotime($request->dropoff));
                    $booking->save();
                    $this->round_fare_calculation($booking->id);
                }
                if ($booking->booking_option == "OneWay") {
                    $this->oneway_calc($booking->id);
                }
                if ($booking->booking_option == "Rental") {
                    $this->rental_calc($booking->id);
                }
                $booking = Bookings::find($booking->id);
                //$this->customer_booking_notification($booking->id, $booking->vehicle_typeid,  $booking->booking_option);
                // dd($booking->getMeta());
                $record = array(
                    'id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'user_id' => $booking->user_id,
                    'pickup' => $booking->pickup,
                    'dropoff' => $booking->dropoff,
                    'pickup_addr' => $booking->pickup_addr,
                    'dest_addr' => $booking->dest_addr,
                    'travellers' => $booking->travellers,
                    'status' => $booking->status,
                    'driver_id' => $booking->driver_id,
                    'note' => $booking->note,
                    'is_booked' => $booking->is_booked,
                    'custom_approved' => $booking->custom_approved,
                    "meta_details" => $booking->getMeta(),
                    'timestamp' => date('Y-m-d H:i:s', strtotime($booking->updated_at)),
                    'customer_name' => $booking->customer->name,
                    'vehicle_name' => ($booking->vehicle_id) ? $booking->vehicle->make_name . "-" . $booking->vehicle->model_name : "",
                    'driver_name' => ($booking->driver_id) ? $booking->driver->name : "",
                    "return_date" => $booking->return_date,
                    "return_time" => $booking->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($booking->journey_date . " " . $booking->journey_time)),
                );
                $data['success'] = "1";
                $data['message'] = "Data fetched!";
                $data['data'] = $record;
            } else {
                $data['success'] = "0";
                $data['message'] = "Selected Vehicle is not Available in Given Timeframe";
                $data['data'] = null;
            }
        }
        return $data;
    }
    public function rental_calc($id)
    {
        $booking = Bookings::find($id);
        $package = PackagesModel::find($booking->package_id);
        $driver_amount = round($package->package_rate - ($package->package_rate * Hyvikk::extra('general_company_commission')) / 100, 2);
        $tax = 0;
        $udfs = json_decode(Hyvikk::get('tax_charge'));
        if ($udfs != null) {
            foreach ($udfs as $key => $value) {$tax = $tax + $value;
            }
        }
        $tax_total = (($package->package_rate * $tax) / 100 + $package->package_rate);
        $total_tax_charge_rs = ($package->package_rate * $tax) / 100;
        // payment details
        $booking->setMeta([
            'mileage' => 0,
            'waiting_time' => 0,
            'date' => date('Y-m-d'),
            'total' => $package->package_rate,
            'total_kms' => 0,
            'tax_total' => round($tax_total, 2),
            'total_tax_percent' => $tax,
            'total_tax_charge_rs' => round($total_tax_charge_rs, 2),
            'driver_amount' => round($driver_amount, 2),
            'general_company_commission' => Hyvikk::extra('general_company_commission'),
        ]);
        $booking->mileage = 0;
        $booking->total_kms = 0;
        $booking->driving_time = $package->package_hours . "hr";
        $booking->approx_timetoreach = $package->package_hours . "hr";
        $booking->receipt = 1;
        $booking->save();
    }
    public function local_fare_calc($id)
    {
        $booking = Bookings::find($id);
        $vehicletype = $booking->vehicle->types->vehicletype;
        $key = Hyvikk::api('api_key');
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . str_replace(" ", "", $booking->pickup_addr) . "&destination=" . str_replace(" ", "", $booking->dest_addr) . "&mode=driving&units=metric&sensor=false&key=" . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        // dd($ch);
        curl_close($ch);
        $response = json_decode($data, true);
        $time = $response['routes'][0]['legs'][0]['duration']['text'];
        $distance = $response['routes'][0]['legs'][0]['distance']['text'];
        $total_kms = explode(" ", $distance)[0];
        $km_base = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicletype)) . '_base_km');
        $base_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicletype)) . '_base_fare');
        $std_fare = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicletype)) . '_std_fare');
        $base_km = Hyvikk::fare(strtolower(str_replace(' ', '', $vehicletype)) . '_base_km');
        if ($total_kms <= $km_base) {
            $total_fare = $base_fare;
        } else {
            $total_fare = $base_fare + (($total_kms - $km_base) * $std_fare);
        }
        // calculate tax charges
        $count = 0;
        if (Hyvikk::get('tax_charge') != "null") {
            $taxes = json_decode(Hyvikk::get('tax_charge'), true);
            foreach ($taxes as $key => $val) {
                $count = $count + $val;
            }
        }
        $tax_total = (($total_fare * $count) / 100) + $total_fare;
        $total_tax_percent = $count;
        $total_tax_charge_rs = ($total_fare * $count) / 100;
        $booking->setMeta([
            'mileage' => $total_kms,
            'date' => date('Y-m-d'),
            'total' => round($total_fare, 2),
            'total_kms' => $total_kms,
            'tax_total' => round($tax_total, 2),
            'total_tax_percent' => $total_tax_percent,
            'total_tax_charge_rs' => round($total_tax_charge_rs, 2),
            'driving_time' => $time,
            'approx_timetoreach' => $time,
        ]);
        $booking->save();
    }
    public function oneway_calc($id)
    {
        $booking = Bookings::find($id);
        $vehicletype = $booking->vehicle->types->vehicletype;
        $key = Hyvikk::api('api_key');
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . str_replace(" ", "", $booking->pickup_addr) . "&destination=" . str_replace(" ", "", $booking->dest_addr) . "&mode=driving&units=metric&sensor=false&key=" . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        // dd($ch);
        curl_close($ch);
        $response = json_decode($data, true);
        $time = $response['routes'][0]['legs'][0]['duration']['text'];
        $distance = $response['routes'][0]['legs'][0]['distance']['text'];
        $total_kms = explode(" ", $distance)[0];
        $total_kms = str_replace(',','',$total_kms);
        // dd($vehicletype,$total_kms, strtolower(str_replace(" ", "", $vehicletype)));
        $total_fare = ($total_kms * 2 * Hyvikk::fare(strtolower(str_replace(" ", "", $vehicletype)) . '_std_fare')) - ((Hyvikk::extra('oneway_discount') * ($total_kms * 2 * Hyvikk::fare(strtolower(str_replace(" ", "", $vehicletype)) . '_std_fare'))) / 100);
        // calculate tax charges
        $count = 0;
        if (Hyvikk::get('tax_charge') != "null") {
            $taxes = json_decode(Hyvikk::get('tax_charge'), true);
            foreach ($taxes as $key => $val) {
                $count = $count + $val;
            }
        }
        $tax_total = (($total_fare * $count) / 100) + $total_fare;
        $total_tax_percent = $count;
        $total_tax_charge_rs = ($total_fare * $count) / 100;
        $booking->setMeta([
            'mileage' => $total_kms,
            'date' => date('Y-m-d'),
            'total' => round($total_fare, 2),
            'total_kms' => $total_kms,
            'tax_total' => round($tax_total, 2),
            'total_tax_percent' => $total_tax_percent,
            'total_tax_charge_rs' => round($total_tax_charge_rs, 2),
            'driving_time' => $time,
            'approx_timetoreach' => $time,
        ]);
        $booking->save();
    }
    public function round_fare_calculation($id)
    {
        $booking = Bookings::find($id);
        $key = Hyvikk::api('api_key');
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . str_replace(" ", "", $booking->pickup_addr) . "&destination=" . str_replace(" ", "", $booking->dest_addr) . "&mode=driving&units=metric&sensor=false&key=" . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $index = curl_exec($ch);
        // dd($ch);
        curl_close($ch);
        $response = json_decode($index, true);
        $time = $response['routes'][0]['legs'][0]['duration']['text'];
        $distance = $response['routes'][0]['legs'][0]['distance']['text'];
        $total_kms = explode(" ", $distance)[0] * 2;
        // $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $booking->pickup);
        // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $booking->dropoff);
        // $days = $to->diffInDays($from);
        // $hours = $to->diff($from)->format('%h');
        // // dd($hours);
        // $nights = $days;
        // if ($days > 1 && $hours >= 3) {
        //     $days++;
        // }
        $pickup_datetime = date('Y-m-d', strtotime($booking->journey_date)) . " " . date('H:i:s', strtotime($booking->journey_time));
        $dropoff_datetime = date('Y-m-d', strtotime($booking->return_date)) . " " . date('H:i:s', strtotime($booking->return_time));
        $pickup = date('Y-m-d', strtotime($booking->journey_date));
        $dropoff = date('Y-m-d', strtotime($booking->return_date));
        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $pickup);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dropoff);
        $days = $to->diffInDays($from);
        $to_datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pickup_datetime);
        $from_datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dropoff_datetime);
        $hours = $to_datetime->diff($from_datetime)->format('%h');
        $nights = $days;
        // echo "days:" . $days . "<br>";
        if ($days > 1) {
            $days--;
            $t1 = date('H', strtotime($booking->journey_time));
            $rh1 = 24 - $t1;
            $t2 = date('H', strtotime($booking->return_time));
            $rh2 = 24 - $t2; //e.g t2=2 (2 am) => $rh2: 24-2= 22 (means 2 hour jouney of return date)
            // echo "rh1:" . $rh1 . " rh2:" . $rh2 . " " . $days . "<br>";
            if ($rh1 >= 3) {
                $days++;
            }
            if ($rh2 <= 21) {
                $days++;
            }
        }
        if ($days == 1) {
            $tjd = date('H', strtotime($booking->journey_time));
            $rh = 24 - $tjd;
            // dd($rh);
            if ($rh >= 3) {
                $days = 2;
            }
        }
        if ($days == 0) {
            $nights = 1;
            $days = 1;
        }
        // dd($days);
        // $date_pickup = new \DateTime($booking->pickup);
        // $date_dropoff = new \DateTime($booking->dropoff);
        // $nights = $date_pickup
        //     ->setTime(0, 0)
        //     ->diff($date_dropoff)
        //     ->format("%a");
        $type = strtolower(str_replace(' ', '', $booking->vehicle->types->vehicletype));
        // new
        $rmd = Hyvikk::extra('round_min_distance');
        $ark = $total_kms / $days;
        $std_fare = Hyvikk::fare($type . '_std_fare');
        $da = Hyvikk::extra('round_driver_allowance');
        $data['driver_allowance'] = $da * $nights;
        if ($ark <= $rmd && $days > 1) {
            $data['ride_amount'] = ($days * $rmd * $std_fare);
        }
        if ($ark > $rmd && $days > 1) {
            $data['ride_amount'] = ($days * $ark * $std_fare);
        }
        if ($ark <= $rmd && $days == 1) {
            $data['ride_amount'] = ($rmd * $std_fare);
        }
        if ($ark > $rmd && $days == 1) {
            $data['ride_amount'] = ($ark * $std_fare);
        }
        // $data['total'] = $data['ride_amount'];
        // new
        // $data['ride_amount'] = Hyvikk::extra('round_min_distance') * $days * Hyvikk::fare($type . '_std_fare');
        // $data['driver_allowance'] = Hyvikk::extra('round_driver_allowance') * $days;
        $data['total'] = $data['ride_amount'] + $data['driver_allowance'];
        // calculate tax charges
        $count = 0;
        if (Hyvikk::get('tax_charge') != "null") {
            $taxes = json_decode(Hyvikk::get('tax_charge'), true);
            foreach ($taxes as $key => $val) {
                $count = $count + $val;
            }
        }
        $tax_total = (($data['total'] * $count) / 100) + $data['total'];
        $total_tax_percent = $count;
        $total_tax_charge_rs = ($data['total'] * $count) / 100;
        $booking->setMeta([
            'mileage' => $total_kms,
            'date' => date('Y-m-d'),
            'total' => round($data['total'], 2),
            'total_kms' => $total_kms,
            'tax_total' => round($tax_total, 2),
            'total_tax_percent' => $total_tax_percent,
            'total_tax_charge_rs' => round($total_tax_charge_rs, 2),
            'driving_time' => $time,
            'approx_timetoreach' => $time,
            'ride_amount' => round($data['ride_amount'], 2),
            'driver_allowance' => round($data['driver_allowance'], 2),
        ]);
        $booking->save();
    }
    protected function check_booking($pickup, $dropoff, $vehicle)
    {
        $chk = DB::table("bookings")
            ->where("status", 0)
            ->where("vehicle_id", $vehicle)
            ->whereNull("deleted_at")
            ->where("pickup", ">=", $pickup)
            ->where("dropoff", "<=", $dropoff)
            ->get();
        if (count($chk) > 0) {
            return false;
        } else {
            return true;
        }
    }
    public function oldbookings(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user = User::find($request->user_id);
            $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
            $ids1 = Bookings::
            //where('user_id', $request->user_id)->
            pluck('id')->toArray();
            $ids2 = Bookings::where('driver_id', null)->pluck('id')->toArray();
            if ($user->group_id == null || $user->user_type == "S") {
                $bookings = Bookings::with(['vehicle.metas','vehicle.types','driver', 'customer'])->whereIn('id', array_merge($ids1, $ids2));
            }
            else{
                $bookings = Bookings::with(['vehicle.metas','vehicle.types','driver', 'customer'])->whereIn('vehicle_id', $vehicle_ids);
            }
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $bookings = $bookings->where("updated_at", ">", $time);
            }
            // dd($bookings);
            // $bookings = $bookings->whereHas('metas',function($q){
                // $q->where('key', 'is_approved')
                // ->where('value', 1);
                // });
            $bookings = $bookings->orderBy('id', 'desc')->withTrashed()->get(['id', 'customer_id', 'vehicle_id', 'user_id', 'pickup', 'dropoff', 'pickup_addr', 'dest_addr', 'travellers', 'status', 'driver_id', 'note', 'created_at', 'updated_at','deleted_at']);
            // dd($bookings);
            $records = array();
            //$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
            foreach ($bookings as $row) {
                $metas = $row->getMeta();
                $metas['date'] = $row->date ?? date('Y-m-d',strtotime($row->journey_date));
                $records[] = array(
                    'id' => $row->id,
                    'customer_id' => $row->customer_id,
                    'vehicle_id' => $row->vehicle_id,
                    'user_id' => $row->user_id,
                    'pickup' => $row->pickup,
                    'dropoff' => $row->dropoff,
                    // 'pickup' =>date($date_format_setting . ' h:i A', strtotime($row->pickup)) ,
                    // 'dropoff' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
                    'pickup_addr' => $row->pickup_addr,
                    'dest_addr' => $row->dest_addr,
                    'travellers' => $row->travellers,
                    'status' => $row->status,
                    'driver_id' => $row->driver_id,
                    'note' => $row->note,
                    // 'is_booked' => $row->is_booked,
                    // 'custom_approved' => $row->custom_approved,
                    "meta_details" => $metas,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    'customer_name' => $row->customer->name,
                    'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->make_name . "-" . $row->vehicle->model_name : "",
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    "return_date" => $row->return_date,
                    "return_time" => $row->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                    "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                    // "type_name" => ($row->types->displayname) ?? null,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched";
            $data['data'] = $records;
        }
        //dd($data);
        return $data;
    }
    public function bookings(Request $request)
    {
        //dd($request->all());
        $validation = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);
        $errors = $validation->errors();
        if (count($errors) > 0) {
            $data['success'] = "0";
            $data['message'] = implode(", ", $errors->all());
            $data['data'] = null;
        } else {
            $user = User::find($request->user_id);
            $vehicle_ids = VehicleModel::where('group_id', $user->group_id)->pluck('id')->toArray();
            $ids1 = Bookings::
            //where('user_id', $request->user_id)->
            pluck('id')->toArray();
            $ids2 = Bookings::where('driver_id', null)->pluck('id')->toArray();
            //if ($user->group_id == null || $user->user_type == "S") {
                $bookings = Bookings::with(['vehicle.metas','vehicle.types','driver', 'customer'])->whereIn('id', array_merge($ids1, $ids2));
                
            if (isset($request->timestamp)) {
                $time = date('Y-m-d H:i:s', strtotime($request->timestamp));
                $bookings = $bookings->where("updated_at", ">", $time);
            }
            // dd($bookings);
            // $bookings = $bookings->whereHas('metas',function($q){
                // $q->where('key', 'is_approved')
                // ->where('value', 1);
                // });
            $bookings = $bookings->orderBy('id', 'desc')->withTrashed()->get(['id', 'customer_id', 'vehicle_id', 'user_id', 'pickup', 'dropoff', 'pickup_addr', 'dest_addr', 'travellers', 'status', 'driver_id', 'note', 'created_at', 'updated_at','deleted_at']);
            // dd($bookings);
            $records = array();
            //$date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y';
            foreach ($bookings as $row) {
                $metas = $row->getMeta();
                $metas['date'] = $row->date ?? date('Y-m-d',strtotime($row->journey_date));
                $records[] = array(
                    'id' => $row->id,
                    'customer_id' => $row->customer_id,
                    'vehicle_id' => $row->vehicle_id,
                    'user_id' => $row->user_id,
                    'pickup' => $row->pickup,
                    'dropoff' => $row->dropoff,
                    // 'pickup' =>date($date_format_setting . ' h:i A', strtotime($row->pickup)) ,
                    // 'dropoff' => date($date_format_setting . ' h:i A', strtotime($row->dropoff)),
                    'pickup_addr' => $row->pickup_addr,
                    'dest_addr' => $row->dest_addr,
                    'travellers' => $row->travellers,
                    'status' => $row->status,
                    'driver_id' => $row->driver_id,
                    'note' => $row->note,
                    // 'is_booked' => $row->is_booked,
                    // 'custom_approved' => $row->custom_approved,
                    "meta_details" => $metas,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($row->updated_at)),
                    'customer_name' => $row->customer->name,
                    'vehicle_name' => ($row->vehicle_id) ? $row->vehicle->make_name . "-" . $row->vehicle->model_name : "",
                    'driver_name' => ($row->driver_id) ? $row->driver->name : "",
                    "return_date" => $row->return_date,
                    "return_time" => $row->return_time,
                    "journey_date_time" => date('Y-m-d H:i:s', strtotime($row->journey_date . " " . $row->journey_time)),
                    "delete_status" => (isset($row->deleted_at)) ? 1 : 0,
                    // "type_name" => ($row->types->displayname) ?? null,
                );
            }
            $data['success'] = "1";
            $data['message'] = "Data fetched";
            $data['data'] = $records;
        }
        //dd($data);
        return $data;
    }
    public function customere_booking_notification($id, $type_id, $trip_type)
    {
        $booking = Bookings::find($id);
        $amount = $booking->getMeta('tax_total');
        if ($booking->booking_option == "offer") {
            // $amount = $booking->offer->total - $booking->offer->company_commission + $booking->offer->total_tax_charge_rs;
            $amount = $booking->offer->total - $booking->offer->company_commission;
        }
        $data['success'] = 1;
        $data['key'] = "booking_notification";
        $data['message'] = 'Data Received.';
        $data['title'] = "New Booking Request (" . $trip_type . ")";
       // $data['description'] = "Do you want to Accept it ?";
        $data['customer_name'] = $booking->customer->name;
        $data['timestamp'] = date('Y-m-d H:i:s');

        // ///// /// /// //  fare calculation
        $commission_rs = 0;
        $discount_amount = ($booking->discount_amount) ? $booking->discount_amount : 0;
        $driver_amount = round($booking->total - ($booking->total * Hyvikk::extra('general_company_commission')) / 100, 2);
        if ($booking->booking_option == "OneWay") {
            $driver_amount = round($driver_amount - Hyvikk::extra('general_company_charges'), 2);
        }
        

        $fare_details = array(); //vehicletype,time,km,total ride amount, ride amount,company charges ,company commision,driver earnings
        if ($booking->booking_option == "offer") {
            // $tax_total = round($booking->offer->total - $booking->offer->company_commission, 2);
            // $ride_amount = round($booking->offer->total - $booking->offer->company_commission, 2);
            $tax_total = $booking->tax_total;
            $ride_amount = $booking->total;
            $driver_amount = round($booking->offer->total - $booking->offer->company_commission, 2);
            $company_charges = $booking->company_charges;
            $company_commission = $booking->company_commission;
            $commission_rs = $booking->company_commission;
        } else {
            $tax_total = $booking->tax_total;
            $ride_amount = $booking->total;
            // $driver_amount = $driver_amount;
            $driver_amount = ($booking->driver_amount) ? $booking->driver_amount : $driver_amount;
            $company_charges = ($booking->booking_option == "OneWay") ? Hyvikk::extra('general_company_charges') : 0;
            $company_commission = Hyvikk::extra('general_company_commission') . "%";
            if ($booking->booking_option == "offer request") {
                $company_charges = $booking->vendor_fee;
                $ride_amount = $booking->total + $booking->vendor_fee;
            }
            $commission_rs = round(($booking->total * Hyvikk::extra('general_company_commission')) / 100, 2);
        }

        $base_fare_value = null;
        if ($booking->booking_option != 'OneWay') {
            $base_fare_value = ($booking->vehicle_typeid) ? Hyvikk::fare(strtolower(str_replace(' ', '', $booking->types->vehicletype)) . '_base_fare') : null;
        }

        $fare_details = array(
            'vehicle_type' => ($booking->vehicle_typeid != null) ? $booking->types->displayname : null,
            'time' => $booking->driving_time,
            'total_kms' => $booking->total_kms . "kms",
            'total_ride_amount' => $tax_total,
            'ride_amount' => $ride_amount, //subtotal
            'gst' => $booking->total_tax_charge_rs,
            'gst_in_percent' => $booking->total_tax_percent,
            'driver_amount' => $driver_amount,
            'company_charges' => $company_charges,
            'company_commission' => $company_commission,
            'discount_amount' => "" . $discount_amount,
            'base_fare' => "" . $base_fare_value,
            'commission_rs' => "" . $commission_rs,
        );
        /// ./ //// //// // fare-calculation end

        $data['data'] = array('riderequest_info' => array(
            'booking_option' => $booking->booking_option,
            'user_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'source_address' => $booking->pickup_addr,
            'dest_address' => $booking->dest_addr,
            'book_date' => date('Y-m-d', strtotime($booking->created_at)),
            'book_time' => date('H:i:s', strtotime($booking->created_at)),
            'journey_date' => date('d-m-Y', strtotime($booking->journey_date)),
            'journey_time' => date('H:i:s', strtotime($booking->journey_time)),
            'accept_status' => $booking->accept_status,
            'tax_total' => $amount,
            'return_date' => ($booking->booking_option == "RoundTrip") ? date('d-m-Y', strtotime($booking->getMeta('return_date'))) : "",
            'return_time' => ($booking->booking_option == "RoundTrip") ? date('H:i:s', strtotime($booking->getMeta('return_time'))) : "",
            'travellers' => $booking->travellers,
            "vendor" => true,
            'fare_details' => $fare_details,
            'other_things_we_should_know' => $booking->other_things_we_should_know,
        ),
        );

        // dd($data);
        if ($type_id == null) {
            $vehicles = VehicleModel::get()->pluck('id')->toArray();
        } else {
            $vehicles = VehicleModel::where('type_id', $type_id)->get()->pluck('id')->toArray();
        }

        // $testnote = PushNotification::app('appNameAndroid')
        //     ->to('fxDl0c8u-OQ:APA91bFFgTwmXjzD9kLZPvYdjtO2c26FmSznotrPi5WJRfyO4TvHYMUYW_a1sLs7ORS7d_Ugd48t3o1ajTp3-Ou3TignYkQx_D-sds0JWpHmdPS1XTzA0Ci1KorAWf4dIf5pf3AGeJ5G')
        //     ->send($data);
        // dd($testnote);
        // if (in_array($trip_type, ["Local", "RoundTrip"])) {
        //     $drivers = User::meta()->where('users_meta.key', '=', 'city')->where('users_meta.value', 'like', '%' . $city . '%')->where('user_type', 'D')->get();
        // } else if ($trip_type == "offer") {
        //     $drivers = User::where('id', $booking->offer->user_id)->where('user_type', 'D')->get();
        // } else {
        //     $drivers = User::where('is_verified', 1)->where('user_type', 'D')->get();
        // }
       // $c = User::find($booking->customer_id)->first();
        if ($booking->customer->getMeta('fcm_id') != null) {
            // PushNotification::app('appNameAndroid')
            //     ->to($booking->customer->getMeta('fcm_id'))
            //     ->send($data);

            $push = new PushNotification('fcm');
            $push->setMessage($data)
                ->setApiKey(env('server_key'))
                ->setDevicesToken([$booking->customer->getMeta('fcm_id')])
                ->send();
        }
    }
}
