<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/theme.css?v=2.3.1') }}" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/content/nyks/css/nyks.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha512-iQQV+nXtBlmS3XiDrtmL+9/Z+ibux+YuowJjI4rcpO7NYgTzfTOiFNm09kWtfZzEB9fQ6TwOVc8lFVWooFuD/w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>{{ Hyvikk::get('app_name') }}</title>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-52376036-7"></script>
  </head>
  <body>
    <section class="fullscreen t-center fullwidth cover" style="background-color: rgba(0,204,55,242)">
      <div class="container-xs mxw-350 v-center">
        <div class="card">
          <div class="card-body">
            <div class="t-center">
              <h1 class="bold-title">@lang('frontend.forget_password')</h1>
                <p class="bold mt-3">
                  @lang('frontend.forgot_text')
                </p>

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
                  <form action="{{ url('forgot-password') }}" method="post">
                    <!-- Email -->
                    {{ csrf_field() }}
                    <input type="email" name="email" id="email" placeholder="@lang('frontend.email_placeholder')" class="classic_form bg-white radius" value="{{ old('email') }}"/>
                    <!-- Send Button -->
                    <button type="submit" id="submit" class="bg-colored1 click-effect white bold qdr-hover-6 classic_form uppercase no-border radius">
                        @lang('frontend.reset_link')
                    </button>
                    <!-- End Send Button -->
                  </form>
                </div>
          
                <div class="radius-sm gray8" style="background-color:rgba(255,255,255,.5)">
                  <h5 class="mt-1">
                    @lang('frontend.dont_have_account')
                    <a href="{{ url('/#register') }}" class="underline">@lang('frontend.regi')</a>
                  </h5>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="{{ asset('assets/frontend/js/jquery.min.js?v=2.3') }}"></script>
    <!-- PAGE OPTIONS - You can find special scripts for this version -->
    <script src="{{ asset('assets/frontend/content/nyks/js/plugins.js') }}"></script>
    <!-- <script src="content/antares/js/plugins.js"></script> -->
    <!-- MAIN SCRIPTS - Classic scripts for all theme -->
    <script src="{{ asset('assets/frontend/js/scripts.js?v=2.3.1') }}"></script>
    <script>
       window.setTimeout(function () { 
            $(".alert").alert('close'); 
        }, 3000);
    </script>
  </body>
</html>