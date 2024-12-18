@extends('layouts.master')

@section('title', 'database migration')
@section('container')

    <div class="buttons">
        <a href="{{ url('migrate') }}" class="button">migrate & seed</a>
    </div>
@stop
