@extends('layouts.app')
@section('extra_css')
<style type="text/css">
.height{
  height: 200px;
}
</style>
@endsection
@section('heading')
<h1> @lang('fleet.yourAddress')
  <small class="text-muted">{{Auth::user()->name}}</small>
</h1>
@endsection
@section("breadcrumb")

<li class="breadcrumb-item active">@lang('fleet.addresses')</li>
@endsection
@section('content')

<div class="row">
  @foreach($bookings as $booking)
    <div class="col-md-4">
      <div class="card card-info card-outline">
        <div class="card-body height">
          {!! nl2br(e($booking->address)) !!}
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection