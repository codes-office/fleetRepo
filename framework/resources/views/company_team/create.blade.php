@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{  url('admin/company_teams')}}">@lang('fleet.teams')</a></li>
<li class="breadcrumb-item active">@lang('fleet.add_new')</li>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.add_new')</h3>
      </div>
      <div class="card-body">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form action="{{ route('company_teams.store') }}" method="POST">
        @csrf
        <div class="row">
          <!-- Team Name Field -->
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('team_name', __('fleet.team_name'), ['class' => 'form-label']) !!}
              {!! Form::text('team_name', null, ['class' => 'form-control', 'required']) !!}
            </div>
          </div>

          <!-- Manager Name Field -->
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('manager_name', __('fleet.manager_name'), ['class' => 'form-label']) !!}
              {!! Form::text('manager_name', null, ['class' => 'form-control', 'required']) !!}
            </div>
          </div>

          <!-- Auto-generated Team ID (Hidden Field) -->
          {{-- {!! Form::hidden('team_id', uniqid('team_')) !!} --}}

          <!-- Employee Count (This will be set to the number of employees in the team) -->
          {{-- <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('employee_count', __('fleet.employee_count'), ['class' => 'form-label']) !!}
              {!! Form::number('employee_count', 0, ['class' => 'form-control', 'readonly', 'required']) !!}
            </div>
          </div>
        </div> --}}

        <div class="col-md-12">
          {!! Form::submit(__('fleet.add_new'), ['class' => 'btn btn-success']) !!}
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
// Here, we can add logic to update the employee count if needed
// For now, we can assume that you will dynamically set the employee count via backend.
</script>
@endsection
