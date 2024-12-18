@extends('layouts.app')
@section("extra_css")

<style type="text/css">
  .modal-body {
    height: 400px;
    overflow-y: auto;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('bookings.index')}}">@lang('menu.bookings')</a></li>
<li class="breadcrumb-item active">@lang('menu.calendar')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.calendar')</h3>
      </div>
      <div class="card-body">
        <div id='calendar'></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="booking_detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">@lang('fleet.event_details')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="my_event" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">@lang('fleet.my_events')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section("script")
@if(Hyvikk::get('language')!="English-en")
@php($lg = explode('-',Hyvikk::get('language')))
@if($lg[1]!='al')
<script src="{{asset('assets/js/cdn/calendar/'.$lg[1].'.js')}}"></script>
@endif
@endif
<script>
  $(document).ready(function() {
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
      },

      defaultDate: '{{date("Y-m-d")}}',
      navLinks: true, // can click day/week names to navigate views
      editable: false,
      events: "{{ url('admin/calendar')}}",
      eventLimit: true,
      eventClick: function(calEvent, jsEvent, view) {
         $.ajax({

            url: '{{url("/admin/calendar/event/")}}/'+calEvent.type+'/'+calEvent.id,

          })
          .then(function(content){
            $("#booking_detail .modal-body").empty();
            $("#booking_detail .modal-body").html(content);
            $("#booking_detail").modal("show");

          },
          function(xhr, status, error) {

            api.set('content.text', status + ': ' + error);
          });
        }
    });
  });
</script>
@endsection