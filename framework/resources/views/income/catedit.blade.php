@extends('layouts.app')
@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('incomecategories.index')}}">@lang('fleet.incomeCategories')</a></li>
<li class="breadcrumb-item active">@lang('fleet.editIncomeType')</li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">@lang('fleet.editIncomeType')</h3>
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

                {!! Form::open(['route' => ['incomecategories.update',$incomecategory->id],'method'=>'PATCH']) !!}
                {!! Form::hidden('id',$incomecategory->id) !!}
                <div class="row">
                    <div class="form-group col-md-4">
                        {!! Form::label('name', __('fleet.incomeType'), ['class' => 'form-label']) !!}
                        {!! Form::text('name', $incomecategory->name,['class' => 'form-control','required']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit(__('fleet.update'), ['class' => 'btn btn-warning']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection