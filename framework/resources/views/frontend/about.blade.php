
@extends('frontend.layouts.app')

@section('content')
        <section class="hero-section--about d-flex ">
            <div class="container-fluid mt-auto d-flex flex-column p-0">
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <div class="hero-content--about w-100">
                            <div class="inner-content">
                                <h1 class="mb-0">@lang('frontend.about') {{ Hyvikk::get('app_name') }}</h1>
                                <h5 class="karla">@lang('frontend.vehicle_mgmt')</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Ends hero section -->
        <!-- Fleet introduction -->
        <section class="about-fleet">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-center">{{ Hyvikk::frontend('about_title') }}</h2>
                        <p> {{ Hyvikk::frontend('about_description') }}</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- / Fleet introduction -->
        <!-- Fleet features -->
        <section class="py-5">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-4 col-md-6">
                        <img src="{{ asset('assets/images/fleet-about-city.png')}}" alt="about-city" class="d-block ml-5 ml-sm-0 mx-sm-auto">
                    </div>
                    <div class="col-lg-8 col-md-6 d-flex flex-column justify-content-center pl-5 about-feature">
                        <h3 class="primary karla font-weight-bold mb-0"> {{ Hyvikk::frontend('cities') }}+</h3>
                        <p>@lang('frontend.city_desc')</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 ">
                        <img src="{{ asset('assets/images/fleet-about-vehicles.png')}}" alt="about-vehicle" class="d-block ml-auto">
                    </div>
                    <div class="col-lg-8 col-md-6 d-flex flex-column justify-content-center pl-5 about-feature">
                        <h3 class="primary karla font-weight-bold mb-0">{{ Hyvikk::frontend('vehicles') }}+</h3>
                        <p>@lang('frontend.vehicle_desc')</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- / End Fleet features -->
        <!-- Fleet Team -->
        <section class="my-5 py-5">
            <div class="container">
                <h2 class="mb-0 text-center mb-5 pb-3">@lang('frontend.minds_behind') {{ Hyvikk::get('app_name') }}</h2>
                @foreach($team as $key=>$teams)
                    @if ($key % 2 == 0) 
                        <div class="row fleet-member">
                            <div class="col-lg-3 col-12 col fleet-shape">
                                <div class="fleet-member_image">
                                    @if ($teams->image != null)
                                        <img src="{{ url('uploads/' . $teams->image) }}" alt="Image">
                                    @else
                                        <img src="{{ url('assets/images/no-user.jpg') }}" alt="no-user">
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-9 col-12 flex-col-center col">
                                <div class="fleet-member_content">
                                    <h5 class="mb-0">{{ $teams->name }}</h5>
                                    <span class="primary d-block">{{ $teams->designation }}</span>
                                    <p class="mb-0 mt-2">{{$teams->details}}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row fleet-member--flip">
                            <div class="col-lg-9 col-12 flex-col-center col order-2 order-lg-0">
                                <div class="fleet-member_content">
                                    <h5 class="mb-0">{{ $teams->name }}</h5>
                                    <span class="primary d-block">{{ $teams->designation }}</span>
                                    <p class="mb-0 mt-2">{{$teams->details}}</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12 col fleet-shape">
                                <div class="fleet-member_image">
                                    @if ($teams->image != null) 
                                        <img src="{{ url('uploads/' . $teams->image) }}" alt="Image">
                                    @else
                                        <img src="{{ url('assets/images/no-user.jpg') }}" alt="no-user">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
               {{--  <div class="row fleet-member">
                    <div class="col-lg-3 col-12 col fleet-shape">
                        <div class="fleet-member_image">
                            <!-- Member image here -->
                        </div>
                    </div>
                    <div class="col-lg-9 col-12 flex-col-center col">
                        <div class="fleet-member_content">
                            <h5 class="mb-0"> Nency Steven </h5>
                            <span class="primary d-block"> Owner </span>
                            <p class="mb-0 mt-2"> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sed fuga similique quis atque quibusdam, possimus vero expedita quo, iure velit. Asperiores sit quo ut et rerum fuga soluta, corporis dolorem pariatur. Nam aliquam, atque, commodi quis soluta nulla quibusdam delectus? Nemo ipsa nostrum dolore perferendis eum, quis provident consectetur error vitae cumque fugiat similique at fuga ipsam consequuntur? Ipsum, eveniet. </p>
                        </div>
                    </div>
                </div> --}}
            </div>
        </section>
        <!-- /End Fleet team -->
@endsection