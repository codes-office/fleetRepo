@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Create Company Team</h3>
            </div>

            <div class="card-body">
                {!! Form::open(['url' => 'admin/companyteam/store', 'method' => 'POST']) !!}

                @if(Auth::user()->user_type === 'S') <!-- Check if user is super admin -->
                    <div class="form-group">
                        {!! Form::label('company_id', 'Company Name') !!}
                        {!! Form::select('company_id', 
                            $customers->pluck('name', 'id')->toArray(), 
                            null, 
                            ['class' => 'form-control', 'placeholder' => 'Select a Company']) !!}
                    </div>
                @endif

                <div class="form-group">
                    {!! Form::label('team_name', 'Team Name') !!}
                    {!! Form::text('team_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Team Name']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('manager', 'Manager(Optional)') !!}
                    {!! Form::text('manager', null, ['class' => 'form-control', 'placeholder' => 'Enter Manager Name']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                    <a href="{{ url('admin/companyteam') }}" class="btn btn-default">Cancel</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
