@extends('layouts.app')
@section('breadcrumb')
    <li class="breadcrumb-item">@lang('menu.settings')</li>
    <li class="breadcrumb-item active">@lang('menu.traccar_settings')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">@lang('fleet.traccar_settings')
                    </h3>
                </div>

                <div class="card-body">
                    {!! Form::open(['route' => 'traccar_settings.store', 'method' => 'post']) !!}
                    <div class="row">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <h5>  @lang('fleet.traccar_enable')
                                <a data-toggle="modal" data-target="#myModal9"><i class="fa fa-info-circle fa-lg" aria-hidden="true" style="color: #8639dd"></i></a>
                            </h5>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-2">
                          <label class="switch">
                              <input type="checkbox" name="traccar_enable" value="1" id="traccar_enable"
                                  @if (Hyvikk::get('traccar_enable') == 1) checked @endif>
                              <span class="slider round"></span>
                          </label>
                        </div>

                        {{-- <div class="col-md-12 text-center">
                            <div class="form-group">
                                {!! Form::label('traccar_enable', __('fleet.traccar_enable'), ['class' => 'form-label']) !!}
                                <a data-toggle="modal" data-target="#myModal9"><i class="fa fa-info-circle fa-lg" aria-hidden="true" style="color: #8639dd"></i></a>
                                <br>
                                <label class="switch">
                                    <input type="checkbox" name="traccar_enable" value="1" id="traccar_enable"
                                        @if (Hyvikk::get('traccar_enable') == 1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div> --}}
                        <div class="traccar_settings_enable col-md-12 col-lg-12"
                            @if (Hyvikk::get('traccar_enable') != 1) style="display:none;" @endif>
                            <div class="row">
                                <div class="form-group col-md-6 col-lg-6">
                                    {!! Form::label('traccar_server_link', __('fleet.traccar_server_link'), ['class' => 'form-label']) !!}
                                    {!! Form::text('traccar_server_link', Hyvikk::get('traccar_server_link') ?? null, [
                                        'class' => 'form-control',
                                        'id' => 'traccar_server_link',
                                        'required' => 'required',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-6 col-lg-6">
                                    {!! Form::label('traccar_username', __('fleet.traccar_username'), ['class' => 'form-label']) !!}
                                    {!! Form::text('traccar_username', Hyvikk::get('traccar_username') ?? null, [
                                        'class' => 'form-control',
                                        'id' => 'traccar_username',
                                        'required' => 'required',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-6 col-lg-6">
                                    {!! Form::label('traccar_password', __('fleet.traccar_password'), ['class' => 'form-label']) !!}
                                    {!! Form::text('traccar_password', Hyvikk::get('traccar_password') ?? null, [
                                        'class' => 'form-control',
                                        'id' => 'traccar_password',
                                        'required' => 'required',
                                    ]) !!}
                                </div>

                                <div class="form-group col-md-6 col-lg-6">
                                    {!! Form::label('traccar_map_key', __('fleet.traccar_map_key'), ['class' => 'form-label']) !!}
                                    {!! Form::text('traccar_map_key', Hyvikk::get('traccar_map_key') ?? null, [
                                        'class' => 'form-control',
                                        'id' => 'traccar_map_key',
                                        'required' => 'required',
                                    ]) !!}
                                </div>

                                <div class="form-group btn-sm col-md-2 mt-3">
                                    <input type="submit" class="form-control btn btn-success" id="save_button"
                                        value="@lang('fleet.save')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                    <div id="myModal9" class="modal fade" role="dialog">
                        <div class="modal-dialog" role="document">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">@lang('fleet.traccar_enable')</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>@lang('fleet.traccar_enable_info')</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default"
                                        data-dismiss="modal">@lang('fleet.close')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#traccar_enable').on('change', function() {
            if ($(this).is(':checked')) {
                $('.traccar_settings_enable').fadeIn();
                $('form').submit();
            } else {
                $('.traccar_settings_enable').fadeOut();
                $('form').submit();
            }
        });
        @if (session()->has('message'))
            new PNotify({
                title: 'Success!',
                text: "{{ session()->get('message') }}",
                type: 'info'
            });
        @endif
    </script>
@endsection
