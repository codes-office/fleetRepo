@extends('frontend.layouts.app')

@section('content')
        <section class="mt-120 mb-4"></section>
        <section class="booking-section py-5 my-5 text-white" id="edit_profile">
            <h1 class="text-center">@lang('frontend.change_details')</h1>
            <div class="container">
                <div class="row">
                    @if(session('success'))
                        <div class="alert alert-success col-sm-6 offset-sm-3 text-center">
                            {{  session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger col-sm-4 offset-sm-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-sm-12 flex-col-center">
                        <form action="{{ url('edit_profile') }}" class="mt-4 w-100" method="POST" id="profile_form">
                            {!! csrf_field() !!}
                            {!! Form::hidden('id',$detail->id) !!}
                            <div class="form-inputs mt-5 w-100">
                                <div class="row w-100 m-0 p-0">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="label-animate">@lang('frontend.first_name')</label>
                                            <input type="text" class="text-input" name="first_name" value="{{ $detail->first_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="label-animate">@lang('frontend.last_name')</label>
                                            <input type="text" class="text-input" name="last_name" value="{{ $detail->last_name }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="checkboxes form-group">
                                            <div class="pretty p-default p-round">
                                                <input type="radio" name="gender" value="1" checked>
                                                <div class="state custom-state">
                                                    <label class="text-white">@lang('frontend.male')</label>
                                                </div>
                                            </div>
                                            <div class="pretty p-default p-round">
                                                <input type="radio" name="gender" value="0" {{ ($detail->gender == "0") ? "checked" : "" }}>
                                                <div class="state custom-state">
                                                    <label class="text-white">@lang('frontend.female')</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="label-animate">@lang('frontend.email')</label>
                                            <input type="text" class="text-input" name="email" value="{{ $detail->email }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="label-animate">@lang('frontend.phone')</label>
                                            <input type="text" class="text-input" name="phone" value="{{ $detail->mobno }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <textarea class="text-input" cols="30" rows="4" name="address" placeholder="@lang('frontend.your_address')">{{ $detail->address }}</textarea>
                                    </div>
                                    
                                    <button class="tab-button mx-auto mt-3" type="submit" id="booking">@lang('frontend.update')</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
@endsection
