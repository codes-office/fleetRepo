@extends('frontend.layouts.app')
@section('css')
    <style>
        .label_top{
           
    top: -15px;
    -webkit-transform: 0;
    transform: 0;
    font-size: 12px;
    opacity: 1;
    color: rgba(2, 0, 28, 0.5);
    color: rgba(2, 0, 28, 0.5);
}
        
        @media screen and (max-width:1003px) {
            .vehicle-section .custom-controls-container {
                top: unset;
                bottom: 10px;
                
            }
        }

        @media screen and (max-width:390px) {
            .vehicle-slider {
                min-height: 300px;
            }
        }

        @media screen and (max-width: 410px) {
            #image_text {
                color: white;
            }


        }

        @media (max-width: 991px) {
            .hero-section--home {
                background-position-x: 0px !important;
            }
        }

        @media screen and (max-width: 450px) {


            .hero-section--home {
                height: auto;
                width: 100%;
                /* background-size: 100% 100%; */
                background-position: unset;
                /* background-size: 100% 100%;  */
                /* background-repeat: no-repeat */
                /* background-image:none; */
            }

            .book-now-radio-button {
                margin-bottom: 5px;
            }

            .checkboxes {
                display: block;
                text-align: center;
            }
        }

        @media screen and (max-width: 318px) {


            #note_textarea {
                height: 61px;
            }
        }
    </style>
@endsection

@section('content')
    <section class="hero-section--home">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="hero-content--home w-100 text-center mt-4">
                        <h5 class="light primary">{{ Hyvikk::frontend('contact_phone') }}</h5>
                        <h1 class="mb-3" id="image_text">@lang('frontend.reliable_way')</h1>
                        <a href="#book_now"><button class="btn mx-auto form-submit-button">@lang('frontend.book_now')</button></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Ends hero section -->
    <!-- Booking section -->
    <section class="booking-section py-5 my-5 text-white" id="book_now">
        <h1 class="text-center">@lang('frontend.book_a_cab')</h1>
        <div class="container">
            <div class="row">
                @if (session('success'))
                    <div class="alert alert-primary col-sm-10 offset-sm-1">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger col-sm-10 offset-sm-1">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-sm-12 flex-col-center">
                    <form action="{{ url('book') }}" class="mt-4 w-100" method="POST" id="booking_form">
                        {!! csrf_field() !!}
                        <div class="checkboxes flex-row-center">
                            <div class="pretty p-default p-round" id="book-now-radio-button">
                                <input type="radio" name="radio1" id="book-now" value="book_now" checked>
                                <div class="state custom-state">
                                    <label>@lang('frontend.book_for_now')</label>
                                </div>
                            </div>
                            <div class="pretty p-default p-round">
                                <input type="radio" name="radio1" id="book-later" value="book_later"
                                    {{ old('radio1') == 'book_later' ? 'checked' : '' }}>
                                <div class="state custom-state">
                                    <label>@lang('frontend.book_for_later')</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-inputs mt-5 w-100">
                            <div class="row w-100 m-0 p-0">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="" class="label-animate">@lang('frontend.pickup_address')</label>
                                        <input type="text" class="text-input" name="pickup_address" id="pickup_address"
                                            value="{{ old('pickup_address') }}" required>
                                        <span class="input-addon">
                                            <img src="{{ asset('assets/images/frontend-icons-fleet-pickup.png') }}" alt="pickup">
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="" class="label-animate">@lang('frontend.dropoff_address')</label>
                                        <input type="text" class="text-input" name="dropoff_address" id="dropoff_address"
                                            value="{{ old('dropoff_address') }}" required>
                                        <span class="input-addon">
                                            <img src="{{ asset('assets/images/frontend-icons-fleet-drop.png') }}" alt="drop">
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label for="" class="label-animate">@lang('frontend.no_of_person')</label>
                                        <input type="number" class="text-input" name="no_of_person"
                                            value="{{ old('no_of_person') }}" min="1" required>
                                        <span class="input-addon">
                                            <img src="{{ asset('assets/images/frontend-icons-fleet-person.png') }}">
                                        </span>
                                    </div>
                                </div>
                                <!-- imaginery row 2 -->
                                <div class="col-lg-6 col-md-6">
                                    <select class="form-group wide col-sm-12 text-input" name="vehicle_type"
                                        id="vehicle_type" required>
                                        <option value="" disabled selected>@lang('frontend.vehicle_type')</option>
                                        @foreach ($vehicle_type as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $type->id == old('vehicle_type') ? 'selected' : '' }}>
                                                {{ $type->vehicletype }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 hide-book-later">
                                    <div class="form-group">
                                        <label for="" class="label-animate">@lang('frontend.pickup_date')</label>
                                        <input type="text" class="text-input" id="datepicker" name="pickup_date"
                                            value="{{ old('pickup_date') }}">
                                        <span class="input-addon">
                                            <img src="{{ asset('assets/images/frontend-icons-fleet-date.png') }}" alt="">
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 hide-book-later">
                                    <div class="form-group">
                                        <label for="" id="timepicker_label"
                                            class="label-animate">@lang('frontend.pickup_time')</label>
                                        <input type="text" class="text-input" id="timepicker" name="pickup_time"
                                            value="{{ old('pickup_time') }}">
                                        <span class="input-addon">
                                            <img src="{{ asset('assets/images/frontend-icons-fleet-date.png') }}"
                                                alt="">
                                        </span>
                                    </div>
                                </div>
                                <!-- Row 2 ends -->
                                <div class="col-sm-12 ">
                                    <div class="form-group">
                                        <textarea id="note_textarea" placeholder="@lang('frontend.other_things')" cols="10" rows="1" class="text-input"
                                            name="note">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                                @php($methods = json_decode(Hyvikk::payment('method')))
                                @if (Hyvikk::frontend('admin_approval') == 0 && Hyvikk::api('api_key') != null)
                                    <div class="col-lg-12">
                                        <div class="checkboxes flex-row-center">
                                            <label class="state custom-state">@lang('frontend.select_payment_method'): &nbsp;</label>
                                            @foreach ($methods as $method)
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="method" id="method"
                                                        value="{{ $method }}"
                                                        @if ($method == 'cash') checked @endif>
                                                    <div class="state custom-state">
                                                        <label>{{ $method }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                @endif
                                <button class="tab-button mx-auto mt-3" type="submit"
                                    id="booking">@lang('frontend.book_now')</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Ends booking section -->
    <!-- Vechicles Section -->
    <!-- *Note* : there are two sliders one for vehicle details and one for vehicle images, they both are synchronized -->
    <section class="vehicle-section my-5">
        <!-- Section title -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="text-center">@lang('frontend.our_vehicle')</h2>
                </div>
            </div>
        </div>
        <!-- Ends Section title -->
        <div class="vehicle-details-container vehicle-details-slider">
            <!-- Vehicle detail Slides starts -->
            <!-- Slide -->
            @foreach ($vehicle as $v)
                <div class="vehicle-detail animated">
                    <div class="vehicle_name w-100">{{ $v->year }} {{ $v->make_name }} {{ $v->model_name }}</div>
                    <div class="vehicle_details">
                        <div class="passengers"> {{ $v->types->seats }} Passengers </div>
                        <div class="vehicle-class">
                            <img src="{{ asset('assets/images/frontend-icons-fleet-luxurious.png') }}" alt="">
                        </div>
                        <div class="vehicle-data">{{ $v->average }}/100 MPG</div>
                    </div>
                </div>
            @endforeach
            <!-- Slide -->
            <!-- Vehicle image Slides ends -->
        </div>
        <div class="vehicle-container mt-5">
            <div class="row vehicle-slider">
                <!-- Vehicle image Slides starts -->

                @foreach ($vehicle as $v)
                    <div class="col-sm-4 justify-content-center d-flex" id="vehicle_image">
                        @if ($v->vehicle_image)
                            <img src="{{ url('uploads/' . $v->vehicle_image) }}" alt="Vehicle Image"
                                class="img-fluid vehicle-image">
                        @else
                            <img src="{{ asset('assets/images/vehicle.jpeg') }}" alt="Vehicle Image" class="img-fluid">
                        @endif
                    </div>
                @endforeach
                
                <!-- Vehicle image Slides ends -->
            </div>
        </div>
        <!-- Slide dots and current / total slides -->
        <div class="custom-controls-container" id="custom_dots">
            <h6 class="js-vehicle-slide-current"> 1 </h6>
            <div class="custom-dots">
                <!-- Dots will be automatically appended here by js -->
            </div>
            <h6 class="js-vehicle-slide-total">{{ $vehicle->count() }}</h6>
        </div>
    </section>
    <!-- Ends vehicles section -->
    <!-- Services section -->
    <section class="my-5 relative">
        <!-- Section title -->
        <div class="container my-5">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="text-center">@lang('frontend.our_service')</h2>
                </div>
            </div>
        </div>
        <!-- Ends Section title -->
        <div class="container my-0 my-lg-5">
            <div class="row js-service-slider">
                @foreach ($company_services as $service)
                    <div class="col-sm-6 py-5 ">
                        <div class="row w-100 m-0 p-0">
                            <div class="col-sm-4">
                                <div class="service-round-element">
                                    @if ($service->image != null)
                                        <img src="{{ url('uploads/' . $service->image) }}" alt="Service Image">
                                    @else
                                        <img src="" alt="Service Image">
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <h6>{{ $service->title }}</h6>
                                <p class="mt-3">{{ $service->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Slider arrows -->
        <div class="service-slide-prev">
            <img src="{{ asset('assets/images/frontend-icons-fleet-arrow-left.png') }}" alt="">
        </div>
        <div class="service-slide-next">
            <img src="{{ asset('assets/images/frontend-icons-fleet-arrow-right.png') }}" alt="">
        </div>
    </section>
    <!-- Ends services section -->
    <!-- Testimonial section -->
    <section class="pb-5 pt-0">
        <div class="container text-center no-padding-mobile relative">
            <!-- Section title -->
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-center">@lang('frontend.testimonials')</h2>
                    </div>
                </div>
            </div>
            <!-- Ends Section title -->
            <div class="js-testimonial-slider">
                <!-- Slide -->
                @foreach ($testimonial as $t)
                    <div class="col-sm-12">
                        <div class="row mt-5">
                            <div class="col-lg-4 flex-col-center">
                                <div class="testimonial-image-block">
                                    <div class="shadow-overlay"></div>
                                    @if ($t->image != null)
                                        <img src="{{ url('uploads/' . $t->image) }}" alt="Testimonial Image"
                                            class="testimonial-image">
                                    @else
                                        <img src="{{ url('assets/images/no-user.jpg') }}" alt="Testimonial Image"
                                            class="testimonial-image">
                                    @endif
                                    <div class="quote-round">
                                        <img src="{{ asset('assets/images/frontend-icons-fleet-quote.png') }}" alt="quote">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 d-flex flex-column align-items-center">
                                <div class="testimonial-content w-100 text-center text-lg-left">
                                    {{ $t->details }}
                                    <br><br>
                                    <i> - {{ $t->name }}</i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- Slide -->
                <!-- Slides end -->
            </div>
            <div class="testimonial-dots mx-auto">
            </div>
        </div>
    </section>
    <!-- Ends wrapper  -->
@endsection

@section('scripts')
    <script>var google_api = "{{ Hyvikk::api('google_api') }}"; </script>
    <script src="{{asset('assets/js/fleet-frontend.js?v=1.0.0')}}"></script>
    @if(session('success') || $errors->any())
        <script>
            var scrollToDiv = $("#book_now");
            $('html, body').animate({
                scrollTop: scrollToDiv.offset().top
            }, 1000);
        </script>
    @endif

    @if (Hyvikk::api('google_api') == '1')
        <script>
            function initMap() {
                $('#pickup_address').attr("placeholder", "");
                $('#dropoff_address').attr("placeholder", "");
                // var input = document.getElementById('searchMapInput');
                var pickup_addr = document.getElementById('pickup_address');
                new google.maps.places.Autocomplete(pickup_addr);

                var dest_addr = document.getElementById('dropoff_address');
                new google.maps.places.Autocomplete(dest_addr);

                // autocomplete.addListener('place_changed', function() {
                //     var place = autocomplete.getPlace();
                //     document.getElementById('pickup_addr').innerHTML = place.formatted_address;
                // });
            }
        </script>
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script>
    @endif

@endsection
