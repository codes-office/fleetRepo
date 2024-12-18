@extends('layouts.master')

@section('title', trans('installer_messages.final.title'))
@section('container')
    <p class="paragraph" style="text-align: center;">
    THANK YOU
	</p>
    <div class="buttons">
        <a href="{{ url('admin/') }}" class="button">{{ trans('installer_messages.final.exit') }}</a>
    </div>
@stop
