@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section('extra_css')
<style type="text/css">
.nav-tabs-custom>.nav-tabs>li.active{border-top-color:#3c8dbc !important;}
.custom_color.active
{
  color: #fff;
  background-color: #02bcd1 !important;
}
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ url('admin/')}}">@lang('fleet.home')</a></li>
<li class="breadcrumb-item active">@lang('fleet.myProfile')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-3">
  <!-- Profile Image -->
    <div class="card card-info card-outline">
      <div class="card-body box-profile">
        <div class="text-center">
          @if($data->getMeta('driver_image') != null)
            @if(starts_with($data->getMeta('driver_image'),'http'))
              @php($src = $data->getMeta('driver_image'))
            @else
              @php($src=asset('uploads/'.$data->getMeta('driver_image')))
            @endif
            <img src="{{$src}}" class="profile-user-img img-responsive img-circle"  alt="User profile picture">
          @else
            <img src="{{ asset("assets/images/no-user.jpg")}}" alt="User profile picture" class="profile-user-img img-responsive img-circle">
          @endif
        </div>
        <h3 class="profile-username text-center"> {{$data->getMeta('first_name')}} {{ $data->getMeta('last_name')}}</h3>
        <ul class="list-group list-group-unbordered">
          <li class="list-group-item">
            <b>
            @lang('fleet.total')
            @lang('fleet.bookings')</b> <a class="pull-right"> {{$total}} </a>
          </li>
        </ul>
        <a href="{{ url('admin/change-details/'.Auth::user()->id) }}" class="btn btn-info btn-block"><b>@lang('fleet.editProfile')</b></a>
      </div>
    </div>
    <!-- About Me Box -->
    <div class="card card-info">
      <div class="card-header">
      <h3 class="card-title">@lang('fleet.about_me')</h3>
      </div>
      <!-- /.box-header -->
      <div class="card-body">
        <strong><i class="fa fa-user margin-r-5"></i> @lang('fleet.personal_info')</strong>
        <p class="text-muted">
          @lang('fleet.driver') @lang('fleet.name'): {{$data->getMeta('first_name')}} {{$data->getMeta('middle_name')}} {{$data->getMeta('last_name')}}
          <br>
          @lang('fleet.phone'): {{$data->getMeta('phone')}}
          <br>
          @lang('fleet.email'): {{$data->email}}
          <br>
          @lang('fleet.address'): {{$data->getMeta('address')}}
        </p>
        <hr>
        <strong><i class="fa fa-file margin-r-5"></i> @lang('fleet.doc_info')</strong>

        <p class="text-muted">
          @lang('fleet.licenseNumber'): {{$data->getMeta('license_number')}}
          <br>
          @lang('fleet.issueDate'): {{$data->getMeta('issue_date')}}
          <br>
          @lang('fleet.expirationDate'): {{$data->getMeta('exp_date')}}
          <br>
          @lang('fleet.employee_id'): {{$data->getMeta('emp_id')}}
          <br>
          @lang('fleet.contract'): {{$data->getMeta('contract_number')}}
        </p>
        <hr>
        <p>
          @if($data->getMeta('license_image') != null)
          <a href="{{asset('uploads/'.$data->getMeta('license_image'))}}" target="_blank" class="btn btn-info">
          @lang('fleet.lic_photo')
          </a>
          @endif
        </p>
        <p>
          @if($data->getMeta('documents') != null)
            <a href="{{asset('uploads/'.$data->getMeta('documents'))}}" target="_blank" class="btn btn-info">
            @lang('fleet.documents')
            </a>
          @endif
        </p>
      </div>
    </div>
    <!-- /.box -->
  </div>

  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link custom_color active" href="#activity" data-toggle="tab">@lang('fleet.activity')</a></li>
          <li class="nav-item"><a class="nav-link custom_color" href="#upcoming" data-toggle="tab">@lang('fleet.upcoming')</a></li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <div class="active tab-pane" id="activity">
            <h4>@lang('menu.my_bookings')</h4>
            <div class="table-responsive">
              <table class="table driver_table">
                <thead class="thead-inverse">
                  <tr>
                    <th>@lang('fleet.customer')</th>
                    <th>@lang('fleet.vehicle')</th>
                    <th>@lang('fleet.pickup')</th>
                    <th>@lang('fleet.dropoff')</th>
                    <th>@lang('fleet.pickup_addr')</th>
                    <th>@lang('fleet.dropoff_addr')</th>
                    <th>@lang('fleet.passengers')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bookings as $row)
                  @if($row->getMeta('ride_status') == "Completed" || $row->status == 1)
                  <tr>
                    <td>{{$row->customer->name}}</td>
                    <td>{{$row->vehicle->make_name}} - {{$row->vehicle->model_name}} - {{$row->vehicle['license_plate']}}</td>
                    <td>
                    @if($row->pickup != null)
                    {{date($date_format_setting.' g:i A',strtotime($row->pickup))}}
                    @endif
                    </td>
                    <td>
                    @if($row->dropoff != null)
                    {{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}
                    @endif
                    </td>
                    <td>{{$row->pickup_addr}}</td>
                    <td>{{$row->dest_addr}}</td>
                    <td>{{$row->travellers}}</td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane" id="upcoming">
            <h4>@lang('menu.my_bookings')</h4>
            <div class="table-responsive">
              <table class="table driver_table">
                <thead class="thead-inverse">
                  <tr>
                    <th>@lang('fleet.customer')</th>
                    <th>@lang('fleet.vehicle')</th>
                    <th>@lang('fleet.pickup')</th>
                    <th>@lang('fleet.dropoff')</th>
                    <th>@lang('fleet.pickup_addr')</th>
                    <th>@lang('fleet.dropoff_addr')</th>
                    <th>@lang('fleet.passengers')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bookings as $row)
                  {{-- @if($row->getMeta('ride_status') == "Upcoming" || $row->status != 1) --}}
                  @if($row->getMeta('ride_status') == "Upcoming")
                  <tr>
                    <td>{{$row->customer->name}}</td>
                    <td>{{$row->vehicle->make_name}} - {{$row->vehicle->model_name}} - {{$row->vehicle['license_plate']}}</td>
                    <td>
                    @if($row->pickup != null)
                    {{date($date_format_setting.' g:i A',strtotime($row->pickup))}}
                    @endif
                    </td>
                    <td>
                    @if($row->dropoff != null)
                    {{date($date_format_setting.' g:i A',strtotime($row->dropoff))}}
                    @endif
                    </td>
                    <td>{{$row->pickup_addr}}</td>
                    <td>{{$row->dest_addr}}</td>
                    <td>{{$row->travellers}}</td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
  $('.driver_table').DataTable({
    "language": {
        "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
    }
  });
});

</script>
@endsection