@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Edit Company Team</h3>
            </div>

            <div class="card-body">
                {!! Form::model($team, ['url' => 'admin/companyteam/update/'.$team->id, 'method' => 'POST']) !!}

                <div class="form-group">
                    {!! Form::label('company_id', 'Company Name') !!}
                    {!! Form::select('company_id', 
                        $customers->pluck('name', 'id')->toArray(), 
                        $team->Company_id, 
                        ['class' => 'form-control', 'placeholder' => 'Select a Company']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('team_name', 'Team Name') !!}
                    {!! Form::text('team_name', $team->Team_Name, ['class' => 'form-control', 'placeholder' => 'Enter Team Name']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('manager', 'Manager') !!}
                    {!! Form::text('manager', $team->Manager, ['class' => 'form-control', 'placeholder' => 'Enter Manager Name']) !!}
                </div>

                <div class="form-group">
                    {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
                    <a href="{{ url('admin/companyteam') }}" class="btn btn-default">Cancel</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
