@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.inquiries')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.inquiries')
        </h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.user')</th>
              <th>@lang('fleet.email')</th>
              <th>@lang('fleet.message')</th>
            </tr>
          </thead>
          <tbody>
          @foreach($messages as $msg)
            <tr>
              <td>{{$msg->name}}</td>
              <td>{{ $msg->email }}</td>
              <td>{{$msg->message}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection