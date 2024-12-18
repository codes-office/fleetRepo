@extends('frontend.layouts.app')

@section('content')
        <section class="mt-120 mb-4">
            <h2 class="primary text-center bg-strip">@lang('frontend.booking_history')</h2>
        </section>
        <!-- Start bookings -->
        <section>
            <div class="container pb-5">
                {{-- <div class="booking-search mb-5">
                    <input type="text" placeholder="Search Bookings here.." id="bookingSearch">
                    <span class="search-icon" data-toggle="bookingSearch">
                        <img src="{{ asset('assets/frontend/icons/fleet-search-box.png')}}" alt="" class="js-iconChange" data-one="{{ asset('assets/frontend/icons/fleet-close-black.png')}}" data-two="{{ asset('assets/frontend/icons/fleet-search-box.png')}}">
                    </span>
                </div> --}}
                @if($bookings->count() > 0)
                    @foreach($bookings as $booking)
                    <div class="booking">
                        <span class="booking-date">
                            {{ date((Hyvikk::get('date_format')) ? Hyvikk::get('date_format') : 'd-m-Y', strtotime($booking->journey_date)) }}
                        </span>
                        <span class="booking-status pill danger filled"> {{ ($booking->ride_status)?$booking->ride_status:"Pending" }} </span>
                        <div class="row">
                            <div class="col-lg-4">
                                <h6 class="primary">@lang('frontend.from')</h6>
                                <p> {{ $booking->pickup_addr }}</p>
                            </div>
                            <div class="col-lg-4">
                                <h6 class="primary">@lang('frontend.to')</h6>
                                <p> {{ $booking->dest_addr }}</p>
                            </div>
                            <div class="col-lg-4">
                                <h6 class="primary">@lang('frontend.payment')</h6>
                                <p> {{ ($booking->payment == 1)?"Success":"Pending" }}</p>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <div class="pills-container">
                                    @if($booking->driving_time)
                                    <span class="pill dark">
                                        <img src="{{ asset('assets/images/frontend-icons-fleet-booking-time.png')}}" alt="booking-time">
                                        {{ $booking->driving_time }}
                                    </span>
                                    @endif

                                    @if($booking->tax_total)<span class="pill dark"> <span class="rupees"> {{ Hyvikk::get('currency') }} </span>{{ $booking->tax_total }}</span>@endif
                                    @if($booking->total_kms)<span class="pill dark"> <img src="{{ asset('assets/images/frontend-icons-fleet-kilometer.png')}}" alt="fleet-kilometer"> {{ $booking->total_kms }} {{ Hyvikk::get('dis_format') }} </span>@endif
                                    
                                </div>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <div class="pills-container">
                                    @php($methods = json_decode(Hyvikk::payment('method')))
                                    @if($booking->receipt == 1 && $booking->payment == 0)
                                    {!! Form::open(['route' => 'redirect-payment','method'=>'post']) !!}
                                    {!! Form::hidden('booking_id',$booking->id) !!}
                                    <div class="form-group">
                                        @foreach($methods as $method)
                                        <div class="pretty p-default p-round">
                                            <input type="radio" name="method" value="{{ $method }}" checked>
                                            <div class="state custom-state">
                                                <label class="">{{ $method }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                        <button type="submit" class="btn btn-success">@lang('frontend.pay_now')</button>
                                    </div>                                   
                                    {!! Form::close() !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <h4 class="text-center">No Record Found.</h4>    
                @endif
            </div>
        </section>
        <!-- End bookings -->
        <!-- Contact tiles -->
@endsection