@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item">{{ link_to_route('expensecategories.index', __('fleet.expenseCategories'))}}</li>
<li class="breadcrumb-item active">@lang('fleet.addExpenseType')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.addExpenseType')</h3>
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

        {!! Form::open(['route' => 'expensecategories.store','method'=>'post']) !!}
        <div class="row">
          <div class="form-group col-md-4">
            {!! Form::label('name', __('fleet.expenseType'), ['class' => 'form-label']) !!}
            {!! Form::text('name', null,['class' => 'form-control','required']) !!}
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="form-group col-md-4">
            {!! Form::submit(__('fleet.save'), ['class' => 'btn btn-success']) !!}
          </div>
        </div>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endsection