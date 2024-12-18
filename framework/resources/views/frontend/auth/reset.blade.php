<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>Reset Password | Fleet Manager </title>

    <meta
      name="viewport"
      content="width=device-width,initial-scale=1.0,maximum-scale=1"
    />
    <!--Favicon -->

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha512-iQQV+nXtBlmS3XiDrtmL+9/Z+ibux+YuowJjI4rcpO7NYgTzfTOiFNm09kWtfZzEB9fQ6TwOVc8lFVWooFuD/w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/theme.css?v=2.3.1') }}" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/content/nyks/css/nyks.css') }}" />
    <!-- End Page Styles -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-52376036-7"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-52376036-7');
    </script>
  </head>

  <!-- BODY START -->

  <body>
    <!-- Wrapper -->
    <section id="wrapper">
      <!-- ALL SECTIONS -->
      <section id="content">
        <!-- CONTENT -->
        <section class="fullscreen t-center fullwidth cover" style="background-color: rgba(0,204,55,242)">
          <!-- Container -->
          <div class="container-xs mxw-350 v-center">
            <!-- start card -->
            <div class="card">
              <div class="card-body">
                <div class="t-center">
                  <h1 class="bold-title">@lang('frontend.reset_password')</h1>

                  @if (session('error'))
                    <div class="alert alert-danger xs-mt">
                      {{ session('error') }}
                    </div>
                  @endif
                  
                  @if (session('success'))
                    <div class="alert alert-success xs-mt">
                      {{ session('success') }}
                    </div>
                  @endif

                  <div class="form dark xs-mt normal-title">
                    <form action="{{ url('reset-password') }}" method="post">
                      <!-- Email -->
                      {!! Form::hidden('token',$token) !!}
                      {{ csrf_field() }}

                      <input type="email" name="email" id="email" placeholder="Registered email address." class="classic_form bg-white radius" value="{{ $email }}" readonly />
                      
                      <input type="password" name="password" id="email" placeholder="@lang('frontend.password')" class="classic_form bg-white radius" required />
                        
                      <input type="password" name="password_confirmation" id="email" placeholder="@lang('frontend.confirm_password')" class="classic_form bg-white radius" required/>
                      
                      <!-- Send Button -->
                      <button type="submit" id="submit" class="bg-colored1 click-effect white bold qdr-hover-6 classic_form uppercase no-border radius">
                          @lang('frontend.reset_your_password')
                      </button>
                      <!-- End Send Button -->
                    </form>
                  </div>

                  <div class="p-2 radius-sm gray8" style="background-color:rgba(255,255,255,.5)">
                    <h5 class="mt-1 bold">
                      @lang('frontend.dont_have_account')
                      <a href="{{ url('/#register') }}" class="underline">@lang('frontend.regi')</a>
                    </h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End card -->
        </section>
        <!-- END CONTENT -->
      </section>
      <!-- END ALL SECTIONS -->
    </section>
    <!-- END WRAPPER -->

    <!-- Back To Top -->
    <a id="back-to-top" href="#top"><i class="fa fa-angle-up"></i></a>

    <!-- jQuery -->
    <script src="{{ asset('assets/frontend/js/jquery.min.js?v=2.3') }}"></script>
    <!-- PAGE OPTIONS - You can find special scripts for this version -->
    <script src="{{ asset('assets/frontend/content/nyks/js/plugins.js') }}"></script>
    <!-- <script src="content/antares/js/plugins.js"></script> -->
    <!-- MAIN SCRIPTS - Classic scripts for all theme -->
    <script src="{{ asset('assets/frontend/js/scripts.js?v=2.3.1') }}"></script>
  </body>
  <!-- Body End -->
</html>