
    <!-- Starts Wrapper -->
    <div class="wrapper">
        <!-- MOBILE OFFCANVAS NAVIGATIONS -->
        <!-- Left side off canvas menu -->
        <div class="offcanvas-nav left-side-nav" id="nav-left">
            <div class="ml-5 mt-2">
                <a href="{{ route('frontend.home') }}"><img src="{{ url('assets/images/' . Hyvikk::get('logo_img')) }}" alt="" height="70px"></a>
            </div>
            <div class="offcanvas-nav_close" data-close="nav-left">
                <i class="fa fa-times"></i>
            </div>
            <ul class="list-unstyled offcanvas-nav_links mt-0">
                <li>
                    <a class="nav-item nav-link" href="{{ route('frontend.home') }}">@lang('frontend.home')</a>
                </li>
                <li>
                    <a class="nav-item nav-link" href="{{ route('frontend.about') }}">@lang('frontend.about')</a>
                </li>
                <li>
                    <a class="nav-item nav-link" href="{{ route('frontend.contact') }}">@lang('frontend.contact')</a>
                </li>
            </ul>
        </div>
        <!-- Ends Left side off canvas menu -->
      
        <!-- Header starts -->
        <header>
            <div class="container-fluid d-flex justify-content-between">
                <nav class="navbar navbar-expand-lg d-flex flex-row-reverse flex-lg-row justify-content-aronud w-100">
                    <!-- Right side navbar toggler -->
                    <!-- <div class="right-nav-trigger" data-target="nav-right">
                        <div class="user-icon">
                            <img src="assets/images/user.jpg" alt="">
                        </div>
                    </div> -->
                    <a class="navbar-brand d-none d-sm-inline-block mr-5 pr-5 mr-lg-0 pr-lg-0" href="{{ route('frontend.home') }}">
                        <img src="{{ url('assets/images/' . Hyvikk::get('logo_img')) }}" alt="fleet-logo" height="100px">
                    </a>
                    <!-- mini logo for mobile device -->
                    <a class="navbar-brand d-inline-block d-sm-none" href="{{ route('frontend.home') }}">
                        <img src="{{ url('assets/images/' . Hyvikk::get('icon_img')) }}" alt="fleet-logo">
                    </a>
                    <!-- Left side navbar toggler -->
                    <button class="navbar-toggler left-nav-trigger" data-target="nav-left">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="collapse navbar-collapse mx-auto" id="navbar">
                        <div class="navbar-nav">
                            <a class="nav-item nav-link" href="{{ route('frontend.home') }}">@lang('frontend.home')</a>
                            <a class="nav-item nav-link" href="{{ route('frontend.about') }}">@lang('frontend.about')</a>
                            <a class="nav-item nav-link" href="{{ route('frontend.contact') }}">@lang('frontend.contact')</a>
                        </div>
                    </div>
                </nav>

                @if(!Auth::guest() && (Auth::user()->user_type == "C" || Auth::user()->user_type == "D"))
                    <!-- Item With Dropdown -->
                    <div class="login-container">
                        <div class="d-flex justify-content-center align-items-center dropdown">
                            <div class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><a href="#" class="stay">{{Auth::user()->name}}</a></div>
                            <!-- Dropdown -->
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('frontend.edit_profile') }}">@lang('frontend.edit_profile')</a>
                                <a class="dropdown-item" href="{{ route('frontend.booking_history',Auth::user()->id) }}">@lang('frontend.booking_history')</a>
                                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >@lang('frontend.logout')</a>
                            </div>   
                        </div>
                    
                        <form id="logout-form" action="{{ url('user-logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                @else
                    <div class="login-container">
                        <div class="login-popup-trigger d-flex justify-content-center align-items-center" data-target="login-popup">
                            <h6 class="av">@lang('frontend.login')</h6>
                            <img src="{{ asset('assets/images/frontend-icons-fleet-login2.png')}}" alt="">
                        </div>
                    
                        <div class="login-popup js-close-outside animated faster fadeInUp fadeOutDown" id="login-popup">
                            @if (session('success'))
                                <div class="alert alert-success xs-mt" style="margin-bottom: 35px !important;">
                                {{ session('success') }}
                                </div>
                            @endif
                            @if (count($errors->login) > 0)
                                <div class="alert alert-danger xs-mt" style="margin-bottom: 35px !important;">
                                <ul>
                                @foreach ($errors->login->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                </ul>
                                </div>
                            @endif
                            <form action="{{ url('user-login') }}" method="POST" class="text-center" id="hello">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label id="email_label" class="label-animate">@lang('frontend.email_address')</label>
                                    <input type="email" id="email" class="text-input" name="email" value="{{ old('email') }}">
                                </div>
                                <div class="form-group mb-4">
                                    <label id="password_label" class="label-animate">@lang('frontend.password')</label>
                                    <input type="password" id="password" class="text-input" name="password">
                                </div>
                                <button class="tab-button mx-auto mt-3 w-100" type="submit">@lang('frontend.login')</button>
                                <p class="login-helper mt-3">
                                    <a class="link" href="{{ url('forgot-password') }}">@lang('frontend.forgot_password')</a><br>
                                    <span>@lang('frontend.do_not_account')</span>
                                    <a class="link" data-target="login-modal">@lang('frontend.register_now')</a>
                                </p>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </header>