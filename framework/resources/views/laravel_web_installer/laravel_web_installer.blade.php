@extends('layouts.master')

@section('title', trans('installer_messages.welcome.title'))
@section('style')
    <link href="{{ asset('assets/css/installer-foriden-helper.css') }}" rel="stylesheet"/>
    <style>
        .form-control{
            height: 14px;
            width: 100%;
        }
        .has-error{
            color: red;
        }
        .has-error input{
            color: black;
            border:1px solid red;
        }
    </style>
@endsection
@section('container')

@if(session('message')!="" || session('response')!="" || session('database')!="" )
    <ul style="list-style-type: none;">
        <div class="alert alert-danger">
                <li> {{session('message')}} </li>
        </div>
    </ul>
@endif
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif
    <p class="paragraph" style="text-align: center;">{{ trans('installer_messages.welcome.message') }}</p>
       <form method="post" action="{{ url('installed') }}" id="env-form">
        {!! csrf_field() !!}
        <div class="form-group">
            <label class="col-sm-2 control-label">Purchase Code:</label>

            <div class="col-sm-10">
                <input type="text" name="purchase_code" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Hostname</label>

            <div class="col-sm-10">
                <input type="text" name="hostname" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
                <input type="text" name="username" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" name="password">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Database</label>
            <div class="col-sm-10">
                <input type="text" name="database" class="form-control" required>
            </div>
        </div>

        <div class="modal-footer">
            <div class="buttons">
                <button class="button" type="submit">
                    {{ trans('installer_messages.next') }}
                </button>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/installer-jQuery-2.2.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/installer-foriden-helper.js')}}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection