@extends('layouts.app')


@section("breadcrumb")
<li class="breadcrumb-item active">@lang('menu.notifications')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.notifications')</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.driverImage')</th>
              <th>@lang('fleet.name')</th>
              <th>@lang('fleet.notification')</th>
              <th>@lang('fleet.remaining_days')</th>
            </tr>
          </thead>
          <tbody>
            @php
            $user = App\Model\User::find(Auth::id());
            
            @endphp
            @if ($type = "renew-driving-licence")
            @php ($type = "App\Notifications\RenewDriverLicence")
            @endif
            @foreach ($user->unreadNotifications as $notification)
            @if($notification->type==$type)
            @php($notification->markAsRead())
            @endif
            @endforeach
            @foreach($driver as $data)
            @foreach ($user->notifications as $notification)
            @if($notification->type == $type)
            @php   ($to = \Carbon\Carbon::now())

            @php ($from = \Carbon\Carbon::createFromFormat('Y-m-d', $notification->data['date']))

            @php ($diff_in_days = $to->diffInDays($from))

            @if($data->id == $notification->data['vid'])
            <tr>
              <td>
                @if($data->getMeta('driver_image') == null)
                  <img src="{{ asset("assets/images/no-user.jpg")}}" height="70px" width="70px">
                @else
                  <img src="{{asset('uploads/'.$data->getMeta('driver_image'))}}" height="70px" width="70px">
                @endif
              </td>
              <td>
                {{$data->getMeta('first_name')}} {{$data->getMeta('last_name')}}
              </td>
              <td> @lang('fleet.driver_licence') {{ date(Hyvikk::get('date_format'),strtotime($notification->data['msg'])) }}</td>
              <td>
              @if(strtotime($notification->data['msg'])>strtotime("now"))
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