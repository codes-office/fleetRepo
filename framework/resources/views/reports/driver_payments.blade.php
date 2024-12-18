@extends('layouts.app')
@php $currency = Hyvikk::get('currency') @endphp
@php $date_format_setting = (Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y'; @endphp
@section("breadcrumb")
<li class="breadcrumb-item"><a href="#">@lang('menu.reports')</a></li>
<li class="breadcrumb-item active">@lang('fleet.paymentReport')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add') @lang('fleet.driverPayment')
        </h3>
      </div>
      <div class="card-body">

        {!! Form::open(['route'=>'reports.payments', 'method'=>'POST']) !!}
        <div class="row">
          <div class="col-md-4 form-group">
            {!! Form::label('driver', trans('fleet.driver')) !!}
            <div class="form-group" style="margin-left:5px;">
            {!!
            Form::select('driver',$drivers??[],null,['class'=>'form-control','placeholder'=>trans('fleet.select')],$driver_booking_amount)
            !!}
            {!! Form::hidden('remaining_amount_hidden', null, ['id'=>'remaining_amount_hidden']) !!}</div>
            <small id="remaining_amount" style="display: none;"></small>
          </div>
          <div class="col-md-4 form-group">
            {!! Form::label('amount', trans('fleet.amount')) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::number('amount',null,['class'=>'form-control','step'=>'0.01','min'=>0]) !!}</div>
          </div>
          <div class="col-md-4 form-group">
            {!! Form::label('notes', trans('fleet.notes')) !!}
            <div class="form-group" style="margin-left:5px;">
            {!! Form::textarea('notes',null,['class'=>'form-control','rows'=>2]) !!}</div>
          </div>
          <div class="col-md-12">
            {!! Form::submit(trans('fleet.submit'), ['class'=>'btn btn-primary']) !!}
          </div>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.paymentReport')
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table w-100" id="data_table">
                <thead>
                  <tr>
                    <th></th>
                    {{-- <th style="width: 7%;">Sr No.</th> --}}
                    <th>@lang('fleet.driver') @lang('fleet.id')</th>
                    <th>@lang('fleet.driver')</th>
                    <th>@lang('fleet.description')</th>
                    <th>@lang('fleet.amount')</th>
                    <th>@lang('fleet.datetime')</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($driver_payments as $key => $payment)
                  @if($payment instanceof App\Model\Bookings)
                  <tr>
                    <td></td>
                    {{-- <td>{{ $payment->id }}</td> --}}
                    <td>{{ $payment->driver_id }}</td>
                    <td>{{ $payment->driver->name }}</td>
                    <td>@lang('fleet.booking_id'): {{ $payment->id }}</td>
                    <td>{{ $currency }}{{ $payment->driver_amount??$payment->total }}</td>
                    <td>{{ date($date_format_setting.' h:i A',strtotime($payment->updated_at)) }}</td>
                  </tr>
                  @else
                  <tr>
                    <td></td>
                    {{-- <td>{{ $payment->id }}</td> --}}
                    <td>{{ $payment->driver_id }}</td>
                    <td>{{ $payment->driver->name }}</td>
                    <td>@lang('fleet.payment')</td>
                    <td>{{ $currency }}{{ $payment->amount }}</td>
                    <td>{{ date($date_format_setting.' h:i A',strtotime($payment->updated_at)) }}</td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th></th>
                    {{-- <th>Sr No.</th> --}}
                    <th>@lang('fleet.driver') @lang('fleet.id')</th>
                    <th>@lang('fleet.driver')</th>
                    <th>@lang('fleet.description')</th>
                    <th>@lang('fleet.amount')</th>
                    <th>@lang('fleet.datetime')</th>
                  </tr>
                </tfoot>
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
<script>
  $(function(){
        $('#driver').on('change', function(){
          var remaining_amount = $(this).find('option:selected').attr('data-remaining-amount') || ($(this).find('option:selected').data('amount') || 0);
          
          if(Number.parseInt(remaining_amount) >= 0){
            $('#remaining_amount').text('{{ trans("fleet.remaining_amount") }}: {{ $currency }}'+remaining_amount).show();
            $('#remaining_amount_hidden').val(remaining_amount);
            $('#amount').attr('max',remaining_amount);
          }else{
            $('#remaining_amount').hide();
          }
        });
      });
</script>
@endsection