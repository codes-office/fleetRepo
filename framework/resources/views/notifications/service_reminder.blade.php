@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.serviceReminders')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.serviceReminders')</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th></th>
              <th>@lang('fleet.vehicle')</th>
              <th>@lang('fleet.notification')</th>
              <th>@lang('fleet.remaining_days')</th>
            </tr>
          </thead>
          <tbody>

          @php
          $user = App\Model\User::find(Auth::id());
          @endphp

          @if ($type = "service-reminder")
          @php ($type = "App\Notifications\ServiceReminderNotification")
          @endif
          @foreach ($user->unreadNotifications as $notification)
          @if($notification->type==$type)
          @php($notification->markAsRead())
          @endif
          @endforeach

          @foreach($reminder as $data)

          @foreach ($user->notifications as $notification)

          @if($data->id == $notification->data['vid'])

          @if($notification->type == $type)

          @php ($to = \Carbon\Carbon::now())

          @php ($from = \Carbon\Carbon::createFromFormat('Y-m-d', $notification->data['date']))

          @php ($diff_in_days = $to->diffInDays($from))

            <tr>
              <td>
              @if($data->vehicle['vehicle_image'] != null)
                <img src="{{asset('uploads/'.$data->vehicle['vehicle_image'])}}" height="70px" width="70px">
              @else
                <img src="{{ asset("assets/images/vehicle.jpeg")}}" height="70px" width="70px">
              @endif
              </td>
              <td>
              {{$data->vehicle->year}} {{$data->vehicle->make_name}} {{$data->vehicle->model_name}}
              <br>
              @lang('fleet.vin'):{{$data->vehicle->vin}}
              <br>
              @lang('fleet.plate'):{{$data->vehicle->license_plate}}
              </td>
              <td>
              {{ $notification->data['msg'] }}
              </td>
              <td>
              @if(strtotime($notification->data['date'])>strtotime("now"))
                {{$diff_in_days}}
              @else
                <span class="text-danger">Expired</span>
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