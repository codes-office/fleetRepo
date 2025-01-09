<!DOCTYPE html>
@if(Auth::user()->getMeta('language')!= null)
@php ($language = Auth::user()->getMeta('language'))
@else
@php($language = Hyvikk::get("language"))
@endif

<html>
<!--
@copyright

Fleet Manager v6.1

Copyright (C) 2017-2022 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>  -->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="manifest" href="{{ asset('web-manifest.json?v2')}}">

  <title>{{ Hyvikk::get('app_name') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/css/plugins-fa-all.min.css') }}">
  <!-- Ionicons -->
  {{-- <link rel="stylesheet" href="{{asset('assets/css/ionicons.min.css')}}"> --}}
  <!-- fullCalendar 2.2.5-->
  <link rel="stylesheet" href="{{asset('assets/css/plugins-fullcalendar.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/plugins-fullcalendar.print.css')}}" media="print">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('assets/css/plugins-dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-buttons.dataTables.min.css')}}">
  <!-- Select2 -->
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/select2/select2.min.css')}}"> --}}
  <link rel="stylesheet" href="{{asset('assets/css/select2.min.css')}}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/css/dist-adminlte.min.css')}}">
  <!-- iCheck -->
  {{-- <link rel="stylesheet" href="{{asset('assets/css/plugins-icheck-flat-blue.css')}}"> --}}
  <!-- iCheck for checkboxes and radio inputs -->
  {{-- <link rel="stylesheet" href="{{asset('assets/css/plugins-icheck-all.css')}}"> --}}
  <!-- Morris chart -->
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/morris/morris.css')}}"> --}}
  <!-- jvectormap -->
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}"> --}}
  <!-- bootstrap wysihtml5 - text editor -->
  {{-- <link rel="stylesheet" href="{{asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}"> --}}
  <!-- Google Font: Source Sans Pro -->
  <link href="{{ asset('assets/css/font-fonts.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/css/pnotify.custom.min.css')}}" media="all" rel="stylesheet" type="text/css" />
  <style>
    [data-toggle='modal']{
      cursor: pointer;
    }
    /* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

  </style>
  @yield("extra_css")
  <script>
    window.Laravel = {!! json_encode([
'csrfToken' => csrf_token(),
'subscription_url' => asset('assets/push_notification/push_subscription.php'),
'serviceWorkerUrl' => asset("serviceWorker.js")
]) !!};
  </script>
  <!-- browser notification -->
  <script src="{{asset('assets/push_notification/app.js')}}"></script>
  <style>
    tfoot input {
      width: 100%;
      padding: 3px;
      box-sizing: border-box;
      font-size: 0.6em;
      height: 35px !important;
    }

    .error {
      font-weight: 400 !important;
      color: red;
    }

    .input-group input {
      width: 65% !important;
    }
  </style>
  @if($language == "Arabic-ar")
  <style type="text/css">
    .sidebar {
      text-align: right;
    }

    .nav-sidebar .nav-link>p>.right {
      position: absolute;
      right: 0rem;
      top: 12px;
    }

    .nav-sidebar>.nav-item {
      margin-right: -20px;
    }
  </style>
  @endif
</head>

<body class="hold-transition {{auth()->user()->theme}} layout-fixed sidebar-mini" @if($language=="Arabic-ar" ) dir="rtl"
  @endif>
  {!! Form::hidden('loggedinuser',Auth::user()->id,['id'=>'loggedinuser']) !!}
  {!! Form::hidden('user_type',Auth::user()->user_type,['id'=>'user_type']) !!}
  <div class="wrapper">
    <!-- Navbar -->
    <nav
      class="main-header navbar navbar-expand  @if(auth()->user()->theme=='dark-mode') navbar-dark @else bg-white navbar-light @endif border-bottom">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="https://fleetdocs.hyvikk.space" class="nav-link">@lang('fleet.help')</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <!-- Notifications Dropdown Menu -->
        @if(Auth::user()->user_type=="S" || Auth::user()->user_type=="M")
        @php($r = 0)
        @php($i = 0)
        @php($l = 0)
        @php($d = 0)
        @php($s = 0)
        @php($user= Auth::user())
        @foreach ($user->unreadNotifications as $notification)
        @if($notification->type == "App\Notifications\RenewRegistration")
        @php($r++)
        @elseif($notification->type == "App\Notifications\RenewInsurance")
        @php($i++)
        @elseif($notification->type == "App\Notifications\RenewVehicleLicence")
        @php($l++)
        @elseif($notification->type == "App\Notifications\RenewDriverLicence")
        @php($d++)
        @elseif($notification->type == "App\Notifications\ServiceReminderNotification")
        @php($s++)
        @endif
        @endforeach
        @php($n = $r + $i +$l + $d + $s)
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">@if($n>0) {{$n}} @endif</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            @if($n>0)<span class="dropdown-item dropdown-header"> {{$n}} Notifications </span>
            <div class="dropdown-divider"></div>@endif
            <a href="{{url('admin/vehicle_notification',['type'=>'renew-registrations'])}}" class="dropdown-item">
              <i class="fa fa-newspaper mr-2"></i> @lang('fleet.renew_registration')
              <span class="float-right text-muted text-sm">@if($r>0) {{$r}} @endif</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{url('admin/vehicle_notification',['type'=>'renew-insurance'])}}" class="dropdown-item">
              <i class="fa fa-file-text mr-2"></i> @lang('fleet.renew_insurance')
              <span class="float-right text-muted text-sm">@if($i>0) {{$i}} @endif</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{url('admin/vehicle_notification',['type'=>'renew-licence'])}}" class="dropdown-item">
              <i class="fa fa-file mr-2"></i> @lang('fleet.renew_licence')
              <span class="float-right text-muted text-sm">@if($l>0) {{$l}} @endif</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{url('admin/driver_notification',['type'=>'renew-driving-licence'])}}" class="dropdown-item">
              <i class="fa fa-vcard mr-2"></i> @lang('fleet.renew_driving_licence')
              <span class="float-right text-muted text-sm">@if($d>0) {{$d}} @endif</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{url('admin/reminder',['type'=>'service-reminder'])}}" class="dropdown-item">
              <i class="fa fa-clock-rotate-left mr-2"></i> @lang('fleet.serviceReminders')
              <span class="float-right text-muted text-sm">@if($s>0) {{$s}} @endif</span>
            </a>
          </div>
        </li>
        @endif
        <!-- logout -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-user-circle"></i>
            <span class="badge badge-danger navbar-badge"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                @if(Auth::user()->user_type == 'D' && Auth::user()->getMeta('driver_image') != null)
                @if(starts_with(Auth::user()->getMeta('driver_image'),'http'))
                @php($src = Auth::user()->getMeta('driver_image'))
                @else
                @php($src=asset('uploads/'.Auth::user()->getMeta('driver_image')))
                @endif
                <img src="{{$src}}" class="img-size-50 mr-3 img-circle" alt="User Image">
                @elseif(Auth::user()->user_type == 'S' || Auth::user()->user_type == 'M')
                @if(Auth::user()->getMeta('profile_image') == null)
                <img src="{{ asset("assets/images/no-user.jpg")}}" class="img-size-50 mr-3 img-circle"
                  alt="User Image">
                @else
                <img src="{{asset('uploads/'.Auth::user()->getMeta('profile_image'))}}"
                  class="img-size-50 mr-3 img-circle" alt="User Image">
                @endif
                @elseif(Auth::user()->user_type == 'C' && Auth::user()->getMeta('profile_pic') != null)
                @if(starts_with(Auth::user()->getMeta('profile_pic'),'http'))
                @php($src = Auth::user()->getMeta('profile_pic'))
                @else
                @php($src=asset('uploads/'.Auth::user()->getMeta('profile_pic')))
                @endif
                <img src="{{$src}}" class="img-size-50 mr-3 img-circle" alt="User Image">
                @else
                <img src="{{ asset("assets/images/no-user.jpg")}}" class="img-size-50 mr-3 img-circle"
                  alt="User Image">
                @endif

                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    {{Auth::user()->name}}

                    <span class="float-right text-sm text-danger">

                    </span>
                  </h3>
                  <p class="text-sm text-muted">{{Auth::user()->email}}</p>
                  <p class="text-sm text-muted"></p>

                </div>
              </div>
            </a>
            <div>
              <div style="margin: 5px;">
                <a href="{{ url('admin/change-details/'.Auth::user()->id)}}" class="btn btn-secondary btn-flat"><i
                    class="fa fa-edit"></i> @lang('fleet.editProfile')</a>

                <a href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                  class="btn btn-secondary btn-flat pull-right"> <i class="fa fa-sign-out"></i>
                  @lang('menu.logout')
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                </form>

              </div>
              <div class="clear"></div>
            </div>
            <!-- Message End -->

          </div>
        </li>
        {{-- <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
              class="fa fa-th-large"></i></a>
        </li> --}}
        <!-- logout -->
      </ul>
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

      <a href="{{ url('admin/')}}" class="brand-link">
        <img src="{{ asset('assets/images/'. Hyvikk::get('icon_img') ) }}" alt="Fleet Logo" class="brand-image"
          style="opacity: .8">
        <span class="brand-text font-weight-light">{{ Hyvikk::get('app_name') }}</span>
      </a>

      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            @if(Auth::user()->user_type == 'D' && Auth::user()->getMeta('driver_image') != null)
            @if(starts_with(Auth::user()->getMeta('driver_image'),'http'))
            @php($src = Auth::user()->getMeta('driver_image'))
            @else
            @php($src=asset('uploads/'.Auth::user()->getMeta('driver_image')))
            @endif
            <img src="{{$src}}" class="img-circle elevation-2" alt="User Image">
            @elseif(Auth::user()->user_type == 'S' || Auth::user()->user_type == 'M')
            @if(Auth::user()->getMeta('profile_image') == null)
            <img src="{{ asset("assets/images/no-user.jpg")}}" class="img-circle elevation-2" alt="User Image">
            @else
            <img src="{{asset('uploads/'.Auth::user()->getMeta('profile_image'))}}" class="img-circle elevation-2"
              alt="User Image">
            @endif
            @elseif(Auth::user()->user_type == 'C' && Auth::user()->getMeta('profile_pic') != null)
            @if(starts_with(Auth::user()->getMeta('profile_pic'),'http'))
            @php($src = Auth::user()->getMeta('profile_pic'))
            @else
            @php($src=asset('uploads/'.Auth::user()->getMeta('profile_pic')))
            @endif
            <img src="{{$src}}" class="img-circle elevation-2" alt="User Image">
            @else
            <img src="{{ asset("assets/images/no-user.jpg")}}" class="img-circle elevation-2" alt="User Image">
            @endif

          </div>
          <div class="info">
            <a href="{{ url('admin/change-details/'.Auth::user()->id)}}" class="d-block">{{Auth::user()->name}}</a>
          </div>
        </div>
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            
            <input class="form-control form-control-sidebar" type="search" placeholder=" @lang('fleet.search')" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>
          <div class="sidebar-search-results">
            <div class="list-group">
              <a href="#" class="list-group-item">
                <div class="search-title">
                  <div class="search-path"></div>
                </div>
              </a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar nav-flat flex-column" data-widget="treeview" role="menu"
              data-accordion="false">
              <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
              <!-- customer -->
              @if(Auth::user()->user_type=="C")

              @if(Request::is('admin/bookings*'))
              @php($class="menu-open")
              @php($active="active")
              @else
              @php($class="")
              @php($active="")
              @endif

              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-address-card"></i>
                  <p>
                    @lang('menu.bookings')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('bookings.create')}}"
                      class="nav-link @if(Request::is('admin/bookings/create')) active @endif">
                      <i class="fa fa-address-book nav-icon "></i>
                      <p>
                        @lang('menu.newbooking')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('bookings.index')}}"
                      class="nav-link @if((Request::is('admin/bookings*')) && !(Request::is('admin/bookings/create')) && !(Request::is('admin/bookings_calendar'))) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>
                        @lang('menu.manage_bookings')</p>
                    </a>
                  </li>
                </ul>
              </li>

              <li class="nav-item">
                <a href="{{ url('admin/change-details/'.Auth::user()->id)}}"
                  class="nav-link @if(Request::is('admin/change-details*')) active @endif">
                  <i class="nav-icon fa fa-edit"></i>
                  <p>
                    @lang('fleet.editProfile')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/addresses') }}"
                  class="nav-link @if(Request::is('admin/addresses*')) active @endif">
                  <i class="nav-icon fa fa-map-marker"></i>
                  <p>
                    @lang('fleet.addresses')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{url('admin/') }}" class="nav-link @if(Request::is('admin')) active @endif">
                  <i class="nav-icon fa fa-money-bill-alt"></i>
                  <p>
                    @lang('fleet.expenses')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif
              <!-- customer -->
              <!-- user-type S or O -->

              {{-- @if(Auth::user()->user_type=="S" || Auth::user()->user_type=="O")
              <li class="nav-item">
                <a href="{{ url('admin/')}}" class="nav-link @if(Request::is('admin')) active @endif">
                  <i class="nav-icon fa fa-tachograph-digital"></i>
                  <p>
                    @lang('menu.Dashboard')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif --}}
              <!-- user-type S or O -->

              <!-- driver -->
              @if(Auth::user()->user_type=="D")

              <li class="nav-item">
                <a href="{{url('admin/')}}" class="nav-link @if(Request::is('admin')) active @endif">
                  <i class="nav-icon fa fa-user"></i>
                  <p>
                    @lang('fleet.myProfile')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('my_bookings')}}"
                  class="nav-link @if(Request::is('admin/my_bookings')) active @endif">
                  <i class="nav-icon fa fa-book"></i>
                  <p>
                    @lang('menu.my_bookings')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('admin/vehicle-inspection')}}"
                  class="nav-link @if((Request::is('admin/vehicle-inspection*')) || (Request::is('admin/view-vehicle-inspection*')) || (Request::is('admin/print-vehicle-inspection*'))) active @endif">
                  <i class="fa fa-briefcase nav-icon"></i>
                  <p>@lang('fleet.vehicle_inspection')</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('admin/change-details/'.Auth::user()->id)}}"
                  class="nav-link @if(Request::is('admin/change-details*')) active @endif">
                  <i class="nav-icon fa fa-edit"></i>
                  <p>
                    @lang('fleet.editProfile')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @if (!empty(Hyvikk::chat('pusher_app_id')) && !empty(Hyvikk::chat ('pusher_app_key')) &&
              !empty(Hyvikk::chat('pusher_app_secret')) && !empty(Hyvikk::chat('pusher_app_cluster')))

              <li class="nav-item">
                <a href="{{ url('admin/chat/')}}" class="nav-link @if(Request::is('admin/chat')) active @endif">
                  <i class="nav-icon fa fa-comments-o"></i>
                  <p>
                    @lang('fleet.chat')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif

              @if(Request::is('admin/notes*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif

              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-sticky-note"></i>
                  <p>
                    @lang('fleet.notes')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('notes.index') }}"
                      class="nav-link @if((Request::is('admin/notes*') && !(Request::is('admin/notes/create')))) active @endif">
                      <i class="fa fa-flag nav-icon"></i>
                      <p> @lang('fleet.manage_note')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('notes.create') }}"
                      class="nav-link @if(Request::is('admin/notes/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p>@lang('fleet.create_note')</p>
                    </a>
                  </li>
                </ul>
              </li>

              @if(Request::is('admin/driver-reports*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif

              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-book"></i>
                  <p>
                    @lang('menu.reports')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('dreports.monthly')}}"
                      class="nav-link @if(Request::is('admin/driver-reports/monthly')) active @endif">
                      <i class="fa fa-calendar nav-icon"></i>
                      <p>@lang('menu.monthlyReport')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('dreports.yearly')}}"
                      class="nav-link @if(Request::is('admin/driver-reports/yearly')) active @endif">
                      <i class="fa fa-calendar nav-icon"></i>
                      <p>@lang('fleet.yearlyReport')</p>
                    </a>
                  </li>
                </ul>
              </li>

              @if(Hyvikk::get('fuel_enable_driver') == 1)
              @if(Request::is('admin/fuel*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif

              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-filter"></i>
                  <p>
                    @lang('fleet.fuel')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>

                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('fuel.create') }}"
                      class="nav-link @if(Request::is('admin/fuel/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p> @lang('fleet.add_fuel')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('fuel.index') }}"
                      class="nav-link @if(Request::is('admin/fuel*') && !Request::is('admin/fuel/create')) active @endif">
                      <i class="fa fa-history nav-icon"></i>
                      <p>@lang('fleet.manage_fuel')</p>
                    </a>
                  </li>
                </ul>
              </li>
              @endif

              @if(Hyvikk::get('income_enable_driver') == 1 || Hyvikk::get('expense_enable_driver') == 1)
              @if((Request::is('admin/income')) || (Request::is('admin/expense')) || (Request::is('admin/transaction'))
              || (Request::is('admin/income_records')) || (Request::is('admin/expense_records')) )
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @can('Transactions list')
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-money-bill-alt"></i>
                  <p>
                    @lang('menu.transactions')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @if(Hyvikk::get('income_enable_driver') == 1)
                  <li class="nav-item">
                    <a href="{{ route('income.index')}}"
                      class="nav-link @if((Request::is('admin/income'))|| (Request::is('admin/income_records'))) active @endif">
                      <i class="fa fa-newspaper nav-icon"></i>
                      <p>@lang('fleet.manage_income')
                        
                      </p>
                      <i class="fa fa-plus"></i>
                    </a>
                  </li>
                  @endif
                  @if(Hyvikk::get('expense_enable_driver') == 1)
                  <li class="nav-item">
                    <a href="{{ route('expense.index')}}"
                      class="nav-link @if((Request::is('admin/expense')) || (Request::is('admin/expense_records'))) active @endif">
                      <i class="fa fa-newspaper nav-icon"></i>
                      <p>@lang('fleet.manage_expense')</p>
                    </a>
                  </li>
                  @endif
                </ul>
              </li> @endcan
              @endif

              {{-- <li class="nav-item">
                <a href="{{ url('admin/booking-expense')}}"
                  class="nav-link @if(Request::is('admin/booking-expense*')) active @endif">
                  <i class="fa fa-newspaper nav-icon"></i>
                  <p>@lang('fleet.booking_expense')</p>
                </a>
              </li> --}}
              @endif
              <!-- driver -->

              <!-- sidebar menus for office-admin and super-admin -->

              @if(Auth::user()->user_type=="S" || Auth::user()->user_type=="M")
              <li class="nav-item">
                <a href="{{ url('admin/')}}" class="nav-link @if(Request::is('admin')) active @endif">
                  <i class="nav-icon fa fa-tachograph-digital"></i>
                  <p>
                    @lang('menu.Dashboard')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif

              @if (!Auth::guest() && Auth::user()->user_type != "D" && Auth::user()->user_type != "C"  )
           
              @if((Request::is('admin/drivers*')) || (Request::is('admin/users*')) || (Request::is('admin/mlt'))
              || (Request::is('admin/chat')) )
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Users list','Drivers list','Customer list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-users"></i>
                  <p>
                    @lang('menu.users')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Drivers list')
                  <li class="nav-item">
                    <a href="{{ route('drivers.index')}}"
                      class="nav-link @if(Request::is('admin/drivers*')) active @endif">
                      <i class="fa fa-id-card nav-icon"></i>
                      <p>@lang('menu.drivers')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Users list')
                  <li class="nav-item">
                    <a href="{{ route('users.index')}}" class="nav-link @if(Request::is('admin/users')) active @endif">
                      <i class="fa fa-user nav-icon"></i>
                      <p>@lang('Client')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Users list')
                  <li class="nav-item">
                    <a href="{{ url('admin/mlt')}}" class="nav-link @if(Request::is('admin/mlt')) active @endif">
                      <i class="fa fa-user nav-icon"></i>
                      <p>@lang('MLT')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Users list')
                  <li class="nav-item">
                    <a href="{{ url('admin/companyteam')}}" class="nav-link @if(Request::is('admin/companyteam')) active @endif">
                      <i class="fa fa-user nav-icon"></i>
                      <p>company team</p>
                    </a>
                  </li>
                  @endcan
                  {{-- @can('Customer list')
                  <li class="nav-item">
                    <a href="{{ route('customers.index')}}"
                      class="nav-link @if(Request::is('admin/customers*')) active @endif">
                      <i class="fa fa-address-card nav-icon"></i>
                      <p>Emplyoees</p>
                    </a>
                  </li>
                  @endcan --}}
                  @if (!empty(Hyvikk::chat('pusher_app_id')) && !empty(Hyvikk::chat ('pusher_app_key')) &&
                  !empty(Hyvikk::chat('pusher_app_secret')) && !empty(Hyvikk::chat('pusher_app_cluster')))

                  <li class="nav-item">
                    <a href="{{ route('chat.index')}}" class="nav-link @if(Request::is('admin/chat')) active @endif">
                      <i class="fa fa-comments-o nav-icon"></i>
                      <p>@lang('fleet.chat')</p>
                    </a>
                  </li>
                  @endif
                </ul>
              </li>
              @endcanany

              @if((Request::is('admin/driver-logs')) || (Request::is('admin/vehicle-types*')) ||
              (Request::is('admin/vehicles*')) || (Request::is('admin/vehicle_group*')) ||
              (Request::is('admin/vehicle-reviews*')) || (Request::is('admin/view-vehicle-review*')) ||
              (Request::is('admin/vehicle-review*')) || (Request::is('admin/vehicle-make*')) ||
              (Request::is('admin/vehicle-model*')) || (Request::is('admin/vehicle-color*')))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Vehicles list','VehicleType list','VehicleGroup list','VehicleInspection list','VehicleColors
              list','VehicleModels list','VehicleMaker list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-taxi"></i>
                  <p>
                    @lang('menu.vehicles')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Vehicles list')
                
                    <li class="nav-item">
                      <a href="{{ route('vehicles.index')}}"
                      class="nav-link @if(Request::is('admin/vehicles')  || Request::is('admin/vehicles/create') || Request::is('admin/vehicles/*/edit')) active @endif" style="position:relative">
                      <i class="fa fa-truck nav-icon"></i>
                      <p>@lang('menu.manageVehicles')</p>  
                    </a>
                    </li>

                  
                  {{-- <span style="display:inline !important;">
                    <a href="{{ route('vehicles.create')}}" class="nav-link" title="@lang('fleet.addNew')"><i class="fa fa-plus" style="color: green;"></i></a>
                  </span> --}}
                  {{-- </li> --}}
                  @endcan
                  @can('VehicleType list')
                  <li class="nav-item">
                    <a href="{{ route('vehicle-types.index')}}"
                      class="nav-link @if(Request::is('admin/vehicle-types*')) active @endif">
                      <i class="fa fa-th-list nav-icon"></i>
                      <p>@lang('fleet.manage_vehicle_types')</p>
                    </a>
                  </li>
                  @endcan
                  {{-- @can('Vehicles list')
                  <li class="nav-item">
                    <a href="{{ url('admin/driver-logs')}}"
                      class="nav-link @if(Request::is('admin/driver-logs*')) active @endif">
                      <i class="fa fa-history nav-icon"></i>
                      <p>@lang('fleet.driver_logs')</p>
                    </a>
                  </li>
                  @endcan --}}
                  @can('VehicleGroup list')
                  <li class="nav-item">
                    <a href="{{ route('vehicle_group.index')}}"
                      class="nav-link @if(Request::is('admin/vehicle_group*')) active @endif">
                      <i class="fa fa-inbox nav-icon"></i>
                      <p>@lang('fleet.manageGroup')</p>
                    </a>
                  </li>
                  @endcan

                  @can('VehicleInspection list')
                  <li class="nav-item">
                    <a href="{{ url('admin/vehicle-reviews')}}"
                      class="nav-link @if((Request::is('admin/vehicle-reviews*')) || (Request::is('admin/view-vehicle-review*')) || (Request::is('admin/vehicle-review*'))) active @endif">
                      <i class="fa fa-briefcase nav-icon"></i>
                      <p>@lang('fleet.vehicle_inspection')</p>
                    </a>
                  </li>
                  @endcan
              
                  @if(Auth::user()->user_type=='S')
                  @if(Hyvikk::get('traccar_enable') == 1 ? true : false)
                  <li class="nav-item">
                    <a href="{{ url('admin/vehicles-track')}}"
                      class="nav-link @if((Request::is('admin/vehicles-track*'))) active @endif">
                      <i class="fa fa-location-dot nav-icon"></i>
                      <p>@lang('fleet.vehicles-track')</p>
                    </a>
                  </li>
                  @endif
                  @endif
                </ul>
              </li>
            @endcanany
              @if((Request::is('admin/income')) || (Request::is('admin/expense')) || (Request::is('admin/transaction'))
              || (Request::is('admin/income_records')) || (Request::is('admin/expense_records')) )
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @can('Transactions list')
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-money-bill-alt"></i>
                  <p>
                    @lang('menu.transactions')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('income.index')}}"
                      class="nav-link @if((Request::is('admin/income'))|| (Request::is('admin/income_records'))) active @endif">
                      <i class="fa fa-newspaper nav-icon"></i>
                      <p>@lang('fleet.manage_income')</p>
                      <i class="fa fa-plus" style="float:right;"></i>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('expense.index')}}"
                      class="nav-link @if((Request::is('admin/expense')) || (Request::is('admin/expense_records'))) active @endif">
                      <i class="fa fa-newspaper nav-icon"></i>
                      <p>@lang('fleet.manage_expense')</p>
                    </a>
                  </li>
                </ul>
              </li> @endcan

              @if((Request::is('admin/transactions*')) || (Request::is('admin/bookings*')) ||(Request::is('admin/bookings_calendar')) || (Request::is('admin/merge-bookings')) || (Request::is('admin/timeslots')) || (Request::is('admin/booking-quotation*')))
              @php($class="menu-open")
              @php($active="active")

              @else   
              @php($class="")
              @php($active="")

              @endif
              @canany(['Bookings list','Bookings add','BookingQuotations list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-address-card"></i>
                  <p>
                    @lang('menu.bookings')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Bookings add')
                  <li class="nav-item">
                    <a href="{{ route('bookings.create')}}"
                      class="nav-link @if(Request::is('admin/bookings/create')) active @endif">
                      <i class="fa fa-address-book nav-icon "></i>
                      <p>
                        @lang('menu.newbooking')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Bookings list')
                  <li class="nav-item">
                    <a href="{{ route('bookings.index')}}"
                      class="nav-link @if((Request::is('admin/bookings*')) && !(Request::is('admin/bookings/create')) && !(Request::is('admin/bookings_calendar'))) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>
                        @lang('menu.manage_bookings')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ url('admin/merge-bookings') }}" 
                        class="nav-link @if(Request::is('admin/merge-bookings')) active @endif">
                        <i class="fa fa-compress nav-icon"></i>
                        <p>Merge Bookings</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{ url('admin/timeslots') }}" 
                        class="nav-link @if(Request::is('admin/timeslots')) active @endif"> 
                        <i class="fa fa-clock nav-icon"></i>
                        <p>Time Slots</p>
                      </a>
                    </li>
                    {{-- adding timeslots is here --}}
                  <li class="nav-item">
                    <a href="{{ url('admin/transactions') }}"
                      class="nav-link @if((Request::is('admin/transactions'))) active @endif">
                      <i class="fa fa-money-bill-alt nav-icon"></i>
                      <p>
                        @lang('fleet.transactions')</p>
                    </a>
                  </li>
                  @endcan
                  @can('BookingQuotations list')
                  <li class="nav-item">
                    <a href="{{ route('booking-quotation.index')}}"
                      class="nav-link @if(Request::is('admin/booking-quotation*')) active @endif">
                      <i class="fa fa-quote-left nav-icon"></i>
                      <p>
                        @lang('fleet.booking_quotes')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Bookings list')
                  <li class="nav-item">
                    <a href="{{ route('bookings.calendar')}}"
                      class="nav-link @if(Request::is('admin/bookings_calendar')) active @endif">
                      <i class="fa fa-calendar nav-icon"></i>
                      <p>
                        @lang('menu.calendar')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
              @endcanany

              @if(Request::is('admin/reports*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @can('Reports list')
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-book"></i>
                  <p>
                    @lang('menu.reports')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">

                  <li class="nav-item">
                    <a href="{{ url('admin/reports/income') }}"
                      class="nav-link @if(Request::is('admin/reports/income')) active @endif">
                      <i class="fa fa-credit-card nav-icon"></i>
                      <p> @lang('fleet.income') @lang('fleet.report')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('admin/reports/expense') }}"
                      class="nav-link @if(Request::is('admin/reports/expense')) active @endif">
                      <i class="fa fa-money-bill-alt nav-icon"></i>
                      <p> @lang('fleet.expense') @lang('fleet.report')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('reports.delinquent') }}"
                      class="nav-link @if(Request::is('admin/reports/delinquent')) active @endif">
                      <i class="fa fa-file-text nav-icon"></i>
                      <p> @lang('menu.deliquentReport')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('reports.monthly') }}"
                      class="nav-link @if(Request::is('admin/reports/monthly')) active @endif">
                      <i class="fa fa-calendar nav-icon"></i>
                      <p>@lang('menu.monthlyReport')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('reports.booking') }}"
                      class="nav-link @if(Request::is('admin/reports/booking')) active @endif">
                      <i class="fa fa-book nav-icon"></i>
                      <p>@lang('menu.bookingReport')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('reports.users') }}"
                      class="nav-link @if(Request::is('admin/reports/users')) active @endif">
                      <i class="fa fa-address-book nav-icon"></i>
                      <p>@lang('fleet.user_report')</p>
                    </a>
                  </li>


                  <li class="nav-item">
                    <a href="{{ route('reports.work_order') }}"
                      class="nav-link @if(Request::is('admin/reports/work-order')) active @endif">
                      <i class="nav-icon fa fa-book"></i>
                      <p>@lang('fleet.work_order_report')</p>
                    </a>
                  </li>


                  <li class="nav-item">
                    <a href="{{ route('reports.fuel') }}"
                      class="nav-link @if(Request::is('admin/reports/fuel')) active @endif">
                      <i class="fa fa-truck nav-icon"></i>
                      <p>@lang('fleet.fuelReport')</p>
                    </a>
                  </li>


                  <li class="nav-item">
                    <a href="{{ route('reports.drivers') }}"
                      class="nav-link @if(Request::is('admin/reports/drivers')) active @endif">
                      <i class="fa fa-id-card nav-icon"></i>
                      <p>@lang('fleet.driverReport')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('reports.customers') }}"
                      class="nav-link @if(Request::is('admin/reports/customers')) active @endif">
                      <i class="fa fa-users nav-icon"></i>
                      <p>@lang('fleet.customerReport')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('reports.vendors') }}"
                      class="nav-link @if(Request::is('admin/reports/vendors')) active @endif">
                      <i class="fa fa-cubes nav-icon"></i>
                      <p>@lang('fleet.vendorReport')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('reports.yearly') }}"
                      class="nav-link @if(Request::is('admin/reports/yearly')) active @endif">
                      <i class="fa fa-calendar nav-icon"></i>
                      <p>@lang('fleet.yearlyReport')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('reports.payments') }}"
                      class="nav-link @if(Request::is('admin/reports/payments')) active @endif">
                      <i class="fa fa-money-check-alt nav-icon"></i>
                      <p>@lang('fleet.paymentReport')</p>
                    </a>
                  </li>
                </ul>
              </li>
              @endcan

              @if(Request::is('admin/fuel*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Fuel list','Fuel add'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-filter"></i>
                  <p>
                    @lang('fleet.fuel')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Fuel add')
                  <li class="nav-item">
                    <a href="{{ route('fuel.create') }}"
                      class="nav-link @if(Request::is('admin/fuel/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p> @lang('fleet.add_fuel')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Fuel list')
                  <li class="nav-item">
                    <a href="{{ route('fuel.index') }}"
                      class="nav-link @if(Request::is('admin/fuel*') && !Request::is('admin/fuel/create')) active @endif">
                      <i class="fa fa-history nav-icon"></i>
                      <p>@lang('fleet.manage_fuel')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
              @endcanany

              @if(Request::is('admin/vendors*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Vendors list','Vendors add'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-cubes"></i>
                  <p>
                    @lang('fleet.vendors')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Vendors add')
                  <li class="nav-item">
                    <a href="{{ route('vendors.create') }}"
                      class="nav-link @if(Request::is('admin/vendors/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p> @lang('fleet.add_vendor')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Vendors list')
                  <li class="nav-item">
                    <a href="{{ route('vendors.index') }}"
                      class="nav-link @if((Request::is('admin/vendors*') && !(Request::is('admin/vendors/create')))) active @endif">
                      <i class="fa fa-cube nav-icon"></i>
                      <p>@lang('fleet.manage_vendor')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li> @endcanany


              @if(Request::is('admin/parts*') && !Request::is('admin/parts-used*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Parts list','Parts add','PartsCategory list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-gear"></i>
                  <p>
                    @lang('fleet.parts')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Parts add')
                  <li class="nav-item">
                    <a href="{{ route('parts.create') }}"
                      class="nav-link @if(Request::is('admin/parts/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p> @lang('fleet.addParts')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Parts list')
                  <li class="nav-item">
                    <a href="{{ route('parts.index') }}"
                      class="nav-link @if(Request::is('admin/parts*') && !(Request::is('admin/parts-category*')) && !Request::is('admin/parts/create')) active @endif">
                      <i class="fa fa-gears nav-icon"></i>
                      <p>@lang('menu.manageParts')</p>
                    </a>
                  </li>
                  @endcan
                  @can('PartsCategory list')
                  <li class="nav-item">
                    <a href="{{ route('parts-category.index') }}"
                      class="nav-link @if(Request::is('admin/parts-category*')) active @endif">
                      <i class="fa fa-list nav-icon"></i>
                      <p>@lang('fleet.partsCategory')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>@endcanany

              @if(Request::is('admin/work_order*') || Request::is('admin/parts-used*') ||
              Request::is('admin/mechanic*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['WorkOrders list','WorkOrders add','Mechanics list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-shopping-cart"></i>
                  <p>
                    @lang('fleet.work_orders')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('WorkOrders add')
                  <li class="nav-item">
                    <a href="{{ route('work_order.create') }}"
                      class="nav-link @if(Request::is('admin/work_order/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p> @lang('fleet.add_order')</p>
                    </a>
                  </li>
                  @endcan
                  @can('WorkOrders list')
                  <li class="nav-item">
                    <a href="{{ route('work_order.index') }}"
                      class="nav-link @if((Request::is('admin/work_order*')) && !(Request::is('admin/work_order/create')) && !(Request::is('admin/work_order/logs')) || Request::is('admin/parts-used*')) active @endif">
                      <i class="fa fa-inbox nav-icon"></i>
                      <p>@lang('fleet.manage_work_order')</p>
                    </a>
                  </li>
                  @endcan
                  @can('WorkOrders list')
                  <li class="nav-item">
                    <a href="{{ url('admin/work_order/logs') }}"
                      class="nav-link @if(Request::is('admin/work_order/logs')) active @endif">
                      <i class="fa fa-history nav-icon"></i>
                      <p>@lang('fleet.work_order_logs')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Mechanics list')
                  <li class="nav-item">
                    <a href="{{ url('admin/mechanic') }}"
                      class="nav-link @if(Request::is('admin/mechanic*')) active @endif">
                      <i class="fa fa-user nav-icon"></i>
                      <p>@lang('fleet.mechanics')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>@endcanany

              @if(Request::is('admin/notes*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Notes list','Notes add'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-sticky-note"></i>
                  <p>
                    @lang('fleet.notes')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Notes list')
                  <li class="nav-item">
                    <a href="{{ route('notes.index') }}"
                      class="nav-link @if((Request::is('admin/notes*') && !(Request::is('admin/notes/create')))) active @endif">
                      <i class="fa fa-flag nav-icon"></i>
                      <p> @lang('fleet.manage_note')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Notes add')
                  <li class="nav-item">
                    <a href="{{ route('notes.create') }}"
                      class="nav-link @if(Request::is('admin/notes/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p>@lang('fleet.create_note')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li> @endcanany

              @if((Request::is('admin/service-reminder*')) || (Request::is('admin/service-item*')))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['ServiceReminders list','ServiceReminders add','ServiceItems list'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-clock-rotate-left"></i>
                  <p>
                    @lang('fleet.serviceReminders')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('ServiceReminders list')
                  <li class="nav-item">
                    <a href="{{ route('service-reminder.index') }}"
                      class="nav-link @if(Request::is('admin/service-reminder')) active @endif">
                      <i class="fa fa-arrows-alt nav-icon"></i>
                      <p>@lang('fleet.manage_reminder')</p>
                    </a>
                  </li>
                  @endcan
                  @can('ServiceReminders add')
                  <li class="nav-item">
                    <a href="{{ route('service-reminder.create')}}"
                      class="nav-link @if(Request::is('admin/service-reminder/create')) active @endif">
                      <i class="fa fa-check-square nav-icon"></i>
                      <p>@lang('fleet.add_service_reminder')</p>
                    </a>
                  </li>
                  @endcan
                  @can('ServiceItems list')
                  <li class="nav-item">
                    <a href="{{ route('service-item.index') }}"
                      class="nav-link @if(Request::is('admin/service-item*')) active @endif">
                      <i class="fa fa-warning nav-icon"></i>
                      <p>@lang('fleet.service_item')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li> @endcanany
              @if(Request::is('admin/testimonials*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Testimonials list','Testimonials add'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-quote-left"></i>
                  <p>
                    @lang('fleet.testimonials')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Testimonials list')
                  <li class="nav-item">
                    <a href="{{ route('testimonials.index') }}"
                      class="nav-link @if((Request::is('admin/testimonials*') && !(Request::is('admin/testimonials/create')))) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p> @lang('fleet.manage_testimonial')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Testimonials add')
                  <li class="nav-item">
                    <a href="{{ route('testimonials.create') }}"
                      class="nav-link @if(Request::is('admin/testimonials/create')) active @endif">
                      <i class="fa fa-plus-square nav-icon"></i>
                      <p>@lang('fleet.add_testimonial')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>@endcanany



              @if(Request::is('admin/team*'))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @canany(['Team list','Team add'])
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-users"></i>
                  <p>
                    @lang('fleet.team')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Team list')
                  <li class="nav-item">
                    <a href="{{ route('team.index') }}"
                      class="nav-link @if((Request::is('admin/team*') && !(Request::is('admin/team/create')))) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p> @lang('fleet.manage_team')</p>
                    </a>
                  </li>
                  @endcan
                  @can('Team add')
                  <li class="nav-item">
                    <a href="{{ route('team.create') }}"
                      class="nav-link @if(Request::is('admin/team/create')) active @endif">
                      <i class="fa fa-user-plus nav-icon"></i>
                      <p>@lang('fleet.addMember')</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
              @endcanany

              @if(Request::is('admin/settings*') || Request::is('admin/roles*') || Request::is('admin/fare-settings') ||
              Request::is('admin/api-settings') || (Request::is('admin/expensecategories*')) ||
              (Request::is('admin/incomecategories*')) || (Request::is('admin/expensecategories*')) ||
              (Request::is('admin/send-email')) || (Request::is('admin/set-email')) ||
              (Request::is('admin/cancel-reason*')) || (Request::is('admin/frontend-settings*')) ||
              (Request::is('admin/company-services*')) || (Request::is('admin/payment-settings*')) ||
              (Request::is('admin/twilio-settings*')) || (Request::is('admin/chat-settings*') || (Request::is('admin/traccar-settings'))))
              @php($class="menu-open")
              @php($active="active")

              @else
              @php($class="")
              @php($active="")
              @endif
              @can('Settings list')
              <li class="nav-item has-treeview {{$class}}">
                <a href="#" class="nav-link {{$active}}">
                  <i class="nav-icon fa fa-gear"></i>
                  <p>
                    @lang('menu.settings')
                    <i class="right fa fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('roles.index')}}" class="nav-link @if(Request::is('admin/roles*')) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>@lang('fleet.user_access_management')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('settings.index') }}"
                      class="nav-link @if(Request::is('admin/settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('menu.general_settings')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ url('admin/api-settings')}}"
                      class="nav-link @if(Request::is('admin/api-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('menu.api_settings')</p>
                    </a>
                  </li>
                  @if (Auth::user()->user_type == "S")
                  <li class="nav-item">
                    <a href="{{ url('admin/payment-settings')}}"
                      class="nav-link @if(Request::is('admin/payment-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('fleet.payment_settings')</p>
                    </a>
                  </li>
                 
                 
                  <li class="nav-item">
                    <a href="{{ url('admin/traccar-settings')}}"
                      class="nav-link @if(Request::is('admin/traccar-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('fleet.traccar_settings')</p>
                    </a>
                  </li>
                 
                  @endif
                  <li class="nav-item">
                    <a href="{{ url('admin/twilio-settings')}}"
                      class="nav-link @if(Request::is('admin/twilio-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('fleet.twilio_settings')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('chat_settings.index') }}"
                      class="nav-link @if(Request::is('admin/chat-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('fleet.chat') @lang('menu.settings')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('cancel-reason.index')}}"
                      class="nav-link @if(Request::is('admin/cancel-reason*')) active @endif">
                      <i class="fa fa-ban nav-icon"></i>
                      <p>@lang('fleet.cancellation')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('admin/send-email')}}"
                      class="nav-link @if(Request::is('admin/send-email')) active @endif">
                      <i class="fa fa-envelope nav-icon"></i>
                      <p>@lang('menu.email_notification')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('admin/set-email')}}"
                      class="nav-link @if(Request::is('admin/set-email')) active @endif">
                      <i class="fa fa-envelope-open nav-icon"></i>
                      <p>@lang('menu.email_content')</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ url('admin/fare-settings')}}"
                      class="nav-link @if(Request::is('admin/fare-settings')) active @endif">
                      <i class="fa fa-gear nav-icon"></i>
                      <p>@lang('menu.fare_settings')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('expensecategories.index') }}"
                      class="nav-link @if(Request::is('admin/expensecategories*')) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>@lang('menu.expenseCategories')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ route('incomecategories.index') }}"
                      class="nav-link @if(Request::is('admin/incomecategories*')) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>@lang('menu.incomeCategories')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ url('admin/frontend-settings')}}"
                      class="nav-link @if(Request::is('admin/frontend-settings')) active @endif">
                      <i class="fa fa-address-card nav-icon"></i>
                      <p>@lang('fleet.frontend_settings')</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{ url('admin/company-services')}}"
                      class="nav-link @if(Request::is('admin/company-services*')) active @endif">
                      <i class="fa fa-tasks nav-icon"></i>
                      <p>@lang('fleet.companyServices')</p>
                    </a>
                  </li>

                </ul>
              </li>
              @endcan

              @if(Hyvikk::api('api_key') != null && Hyvikk::api('db_url') != null) <li class="nav-item">
                <a href="{{ url('admin/driver-maps')}}"
                  class="nav-link @if(Request::is('admin/driver-maps') || Request::is('admin/track-driver*')) active @endif">
                  <i class="nav-icon fa fa-map"></i>
                  <p>
                    @lang('fleet.maps')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li> @endif
              <!-- super-admin -->

              @if(Hyvikk::api('api') && Hyvikk::api('driver_review') == 1) <li class="nav-item">
                <a href="{{ url('admin/reviews')}}" class="nav-link @if(Request::is('admin/reviews')) active @endif">
                  <i class="nav-icon fa fa-star"></i>
                  <p>
                    @lang('fleet.reviews')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li> @endif

              @can('Inquiries list')
              @if(in_array(Auth::user()->user_type, ['S','M']))
              <li class="nav-item">
                <a href="{{ url('admin/messages')}}" class="nav-link @if(Request::is('admin/messages')) active @endif">
                  <i class="nav-icon fa fa-comments"></i>
                  <p>
                    @lang('fleet.inquiries')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif
              @endcan
              @endif
              @if(Auth::user()->user_type=="S" || Auth::user()->user_type=="M")
              <li class="nav-item">
                <a href="https://goo.gl/forms/PtzIirmT3ap8m5dY2" target="_blank" class="nav-link">
                  <i class="nav-icon fa fa-comment"></i>
                  <p>
                    @lang('fleet.helpus')
                    <span class="right badge badge-danger"></span>
                  </p>
                </a>
              </li>
              @endif
            </ul>
          </nav>
        </div>
      </div>

    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">@yield('heading') </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                @if(!(Request::is('admin')))
                <li class="breadcrumb-item"><a href="{{ url('admin/')}}">@lang('fleet.home')</a></li>
                @endif
                @yield('breadcrumb')
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>

      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          @yield('content')
        </div><!-- /.container-fluid -->
      </section>
      <div id="chat-overlay" class="row"></div>
      <audio id="chat-alert-sound" style="display: none">
        <source src="{{ asset('assets/chat-sound.mp3') }}" />
      </audio>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      {!! Hyvikk::get('web_footer') !!}
      <div class="float-right d-none d-sm-inline-block">
        <b>@lang('fleet.version')</b> 6.5
      </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->
  <script src="{{ asset('assets/js/cdn-canvasjs.min.js')}}"></script>
  @yield('script2')
  <script src="{{asset('assets/js/plugins-jquery.min.js')}}"></script>
  <script src="{{asset('assets/js/jquery-ui.min.js')}}"></script>
  {{-- <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script> --}}
  <script src="{{ asset('assets/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datetimepicker.js') }}"></script>
  <script src="{{asset('assets/js/bootstrap-datepicker.min.js')}}"></script>
  {{-- <!-- fullCalendar 2.2.5 --> --}}
  <script src="{{asset('assets/js/new_moment.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins-fullcalendar.min.js')}}"></script>
  {{-- <script src="{{asset('assets/plugins/iCheck/icheck.min.js')}}"></script> --}}
  {{-- <script src="{{asset('assets/js/plugins-icheck.min.js')}}"></script> --}}
  <script src="{{asset('assets/js/plugins-bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins-select2.full.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins-fastclick.js')}}"></script>
  <script src="{{asset('assets/js/cdn-jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('assets/js/plugins-dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{ asset('assets/js/cdn-dataTables.buttons.min.js')}}"></script>
  <script src="{{ asset('assets/js/cdn-buttons.print.min.js')}}"></script>
  <script src="{{asset('assets/js/adminlte.js')}}"></script>
  <script src="{{asset('web-sw.js?v3') }}"></script>

  <script type="text/javascript" src="{{ asset('assets/js/cdn-jszip.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/cdn-pdfmake.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/cdn-vfs_fonts.js')}}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/cdn-buttons.html5.min.js')}}"></script>

  <script src="{{ asset('assets/js/cdn-Chart.bundle.min.js')}}"></script>
  <script src="{{ asset('assets/js/cdn-ckeditor.js')}}"></script>

  <script>
    $('[title]').tooltip();

    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('{{ asset("web-sw.js?v3") }}', {
        scope: '.' // <--- THIS BIT IS REQUIRED
      }).then(function(registration) {
        // Registration was successful
        // console.log('ServiceWorker registration successful with scope: ', registration.scope);
      }, function(err) {
        // registration failed :(
        // console.log('ServiceWorker registration failed: ', err);
      });
    }
  </script>
  <script type="text/javascript">
    $(document).ready(function() {

      $('#phone').on('input', function(evt) {
        var inputVal = $(this).val();
        var cleanedVal = inputVal.replace(/[^0-9.]/g, '').replace(/^0+/, '');
        if (cleanedVal.length > 15) {
          cleanedVal = cleanedVal.slice(0, 15);
        }
        $(this).val(cleanedVal);
      });

      $('input[type="number"]').on('keypress', function(evt) {
        var inputVal = $(this).val();
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode;

        if (inputVal.length >= 15) {
          return false;
        }
        if (ASCIICode === 46 && inputVal.length === 0) {
          return false;
        }
        if (ASCIICode === 46 || (ASCIICode >= 48 && ASCIICode <= 57)) {
          return true;
        }
        return false;
      });

      $('input[type="text"]').on('keypress', function(evt) {
        var inputVal = $(this).val();
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
        var inputId = $(this).attr("id");

        if (
          (ASCIICode >= 65 && ASCIICode <= 90) || // A-Z
          (ASCIICode >= 97 && ASCIICode <= 122) || // a-z
          ASCIICode === 32 ||        // Space
          ASCIICode === 44 ||        // Comma
          ASCIICode === 45 ||        // Hyphen (minus)
          ASCIICode === 46 ||        // Period
          ASCIICode === 47 ||        // Slash
          (ASCIICode >= 48 && ASCIICode <= 57) || // Digits 0-9
          ASCIICode === 58 ||        // Colon
          ASCIICode === 64 ||        // At symbol (@)
          (inputId === "currency" && [36, 8364, 163, 165, 8377, 165, 8355, 8363].includes(ASCIICode)) // Currency symbols
        ) {
          return true;
        }
        return false;
      });

      // $('input').on('input', function(evt) {
      //   var inputVal = $(this).val();
      //   // Allow alphanumeric characters (A-Z, a-z, 0-9), specific symbols (-, /, ., ,, :, space, and @)
      //   var cleanedVal = inputVal.replace(/[^A-Za-z0-9\/\.\,\:\-\s@]/g, '')
      //   $(this).val(cleanedVal);
      // });

      $("input[type=search]").on("input", function () {
        const inputValue = this.value.trim(); // Trim the input value to remove whitespace
        if (inputValue.length > 0) {   
          $(".nav-sidebar li").hide().filter(function () {
            return $(this).text().toLowerCase().indexOf(inputValue.toLowerCase()) !== -1;
          }) //.show(); 
        } else { 
          $(".nav-sidebar li").show();
        }
      });

      // old search input code
      // $("input[type=search]").on("keydown", function () {
        //   if (this.value.length > 0) {   
        //     $(".nav-sidebar li").hide().filter(function () {
        //       return $(this).text().toLowerCase().indexOf($("input[type=search]").val().toLowerCase()) != -1;
        //     })//.show(); 
        //   }  
        //   else { 
        //     $(".nav-sidebar li").show();
        //   }
        // }); 

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#data_table tfoot th').each( function () {
        // console.log($('#data_table tfoot th').length);
        if($(this).index() != 0 && $(this).index() != $('#data_table tfoot th').length - 1) {
          var title = $(this).text();
          $(this).html( '<input type="text" placeholder="'+title+'" />' );
        }
      });

      $('#ajax_data_table tfoot th').each( function () {
        // console.log($('#data_table tfoot th').length);
        if($(this).index() != 0 && $(this).index() != $('#data_table tfoot th').length - 1) {
          var title = $(this).text();
          $(this).html( '<input type="text" placeholder="'+title+'" />' );
        }
      });

      $('#data_table1 tfoot th').each( function () {
        // console.log($(this).index());
        if($(this).index() != 0 && $(this).index() != $('#data_table1 tfoot th').length - 1){
          var title = $(this).text();
          $(this).html( '<input type="text" placeholder="'+title+'" />' );
        }
      });

      var table1 = $('#data_table1').DataTable({
      dom: 'Bfrtip',

      buttons: [
          {
        extend: 'print',
        text: '<i class="fa fa-print"></i> {{__("fleet.print")}}',

        exportOptions: {
          columns: ([1,2,3,4,5,6,7,8,9,10]),
        },
        customize: function ( win ) {
                $(win.document.body)
                    .css( 'font-size', '10pt' )
                    .prepend(
                        '<h3>{{__("fleet.bookings")}}</h3>'
                    );
                $(win.document.body).find( 'table' )
                    .addClass( 'table-bordered' );
                // $(win.document.body).find( 'td' ).css( 'font-size', '10pt' );

            }
          }
      ],
      "language": {
            "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
      columnDefs: [ { orderable: false, targets: [0] } ],
      // individual column search
      "initComplete": function() {
            table1.columns().every(function () {
              var that = this;
              $('input', this.footer()).on('keyup change', function () {
                  that.search(this.value).draw();
              });
            });
          }
      });

      var table = $('#data_table').DataTable({
      "language": {
        "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
      },
      columnDefs: [ { orderable: false, targets: [0] } ],
      // individual column search
      "initComplete": function() {
              table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                  // console.log($(this).parent().index());
                    that.search(this.value).draw();
                });
              });
            }
      });
      $('[data-toggle="tooltip"]').tooltip();

    });
  </script>
  <script> // Routes and Labels
    // Bookings
    var getDriverRoute='{{ url("admin/get_driver") }}';
    var getVehicleRoute='{{ url("admin/get_vehicle") }}';
    var prevAddress='{{ url("admin/prev-address") }}';
    var selectDriver="@lang('fleet.selectDriver')";
    var selectCustomer="@lang('fleet.selectCustomer')";
    var selectVehicle="@lang('fleet.selectVehicle')";
    var addCustomer="@lang('fleet.add_customer')";
    var prevAddressLang="@lang('fleet.prev_addr')";
    var fleet_email_already_taken="@lang('fleet.email_already_taken')";

    // Driver
    var driver_comission_amount="@lang('fleet.enter_amount')";
    var driver_comission_percentage="@lang('fleet.enter_percent')";

    // Users
    var group_id_label="@lang('fleet.selectGroup')";
    var role_id_label="@lang('fleet.role')";

  </script>

  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
  <script>var current_route = "{{Route::current()->getName() ?? '' }}"; </script>
  {{-- <script>var google_api = "{{ Hyvikk::api('google_api') }}"; </script>  --}}
  <script src="{{ asset('assets/js/pnotify.custom.min.js')}}"></script>

  @yield('script')

  <script> var base_url = '{{ url("/") }}'; </script>

</body>
</html>