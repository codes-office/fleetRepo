
@extends('layouts.app')
@section("breadcrumb")

<li class="breadcrumb-item active">@lang('menu.notifications')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('menu.notifications')
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.vehicleImage')</th>
              <th>@lang('fleet.vehicles')</th>
              <th>@lang('fleet.notification')</th>
              <th>@lang('fleet.remaining_days')</th>
            </tr>
          </thead>
          <tbody>
          @php
          $user = App\Model\User::find(Auth::id());
          @endphp
          @if($type == "renew-registrations")
          @php ($type = "App\Notifications\RenewRegistration")
          @php ($msg = __('fleet.reg_certificate'))
          @elseif($type == "renew-insurance")
          @php ($type = "App\Notifications\RenewInsurance")
          @php ($msg = __('fleet.vehicle_insurance'))
          @elseif ($type = "renew-licence")
          @php ($type = "App\Notifications\RenewVehicleLicence")
          @php ($msg = __('fleet.vehicle_licence'))
          @else
          @php ($type = "App\Notifications\RenewalCertificate")
          @endif
          @foreach ($user->unreadNotifications as $notification)
          @if($notification->type==$type)
          {{-- @dump($notification) --}}
          @php($notification->markAsRead())
          @endif
          @endforeach

          @foreach($vehicle as $data)

          @foreach ($user->notifications as $notification)
          @if($notification->type == $type)
          @php   ($to = \Carbon\Carbon::now())

          @php ($from = \Carbon\Carbon::createFromFormat('Y-m-d', $notification->data['date']))

          @php ($diff_in_days = $to->diffInDays($from))

          @if($data->id == $notification->data['vid'])
          {{-- @dd($notification) --}}
            <tr>
              <td>
              @if($data->vehicle_image != null)
                <img src="{{asset('uploads/'.$data->vehicle_image)}}" height="70px" width="70px">
              @else
                <img src="{{ asset("assets/images/vehicle.jpeg")}}" height="70px" width="70px">
              @endif
              </td>
              <td>
                {{$data->make_name}} -
                {{$data->model_name}} - {{ $data->license_plate }}
              </td>
              <td>{{ $msg }} {{ date(Hyvikk::get('date_format'),strtotime($notification->data['msg'])) }}
              </td>
              <td>
              @if(strtotime($notification->data['msg'])>strtotime("now"))
                {{$diff_in_days}}
              @else
                <span class="text-danger">@lang('fleet.expired')</span>
              @endif
              </td>
            </tr>
          @endif
          @endif
          @endforeach
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection