<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">

    <!--fonts and icons-->
    <link rel="stylesheet" href="{{asset('fontawesome/css/all.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!--css files-->
    <link rel="stylesheet" href="{{asset('css/mdb.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/welcome-style.css')}}">

    <title>TEALEAVES COLLECT MANAGEMENT SYSTEM</title>

</head>
<body>
   
    <div class="form-section rgba-stylish-strong h-100 d-flex justify-content-center align-items-center" style="height: 100% !important;">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-6 col-md-10 col-sm-12 mx-auto">

                    <!--Form with header-->
                    <div class="card wow fadeIn" data-wow-delay="0.3s">
                        <div class="card-body">

                            <div class="form-outline form-white mb-4">
                                <input type="text" id="username" name="username" class="form-control form-control-lg" />
                                <label class="form-label" for="username">User name</label>
                            </div>

                            <div class="form-outline form-white mb-4">
                                <input type="password" id="password" name="password" class="form-control form-control-lg" />
                                <label class="form-label" for="password">Password</label>
                            </div>

                            <div class="row mb-4">
                                <div class="d-flex justify-content-center">
                                    <!-- Simple link -->
                                    <a href="#!">Forgot password?</a>
                                </div>
                            </div>

                            <button type="button" id="submit-user" class="btn btn-lg btn-success btn-block">Sign in</button>
                            
                        </div>
                    </div>
                    <!--/Form with header-->

                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{asset('js/jquery-3.5.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/mdb.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('sweetalert/sweetalert2.all.js')}}"></script>

    <script>
        !function(e){navigator.userAgent.toLowerCase().indexOf("chrome")>=0&&e("input, select").on("change focus",function(t){setTimeout(function(){e.each(document.querySelectorAll("*:-webkit-autofill"),function(){var t=e(this).clone(!0,!0);e(this).after(t).remove(),n()})},300)}).change();var n=function(){};n()}(jQuery);
        
        $("#submit-user").click(function(e) {
        
            if ( $("#username").val().length !== 0 && $("#password").val().length !== 0 ){

                var username = $('#username').val();
                var password = $('#password').val();
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{url("/authentication")}}',
                    type: 'POST',
                    data: {
                        username:username,
                        password:password
                    },
                    dataType: 'JSON',
                    success: function (data) { 
                        if(data.result == true) {
                            console.log(data);
                            window.location.href = '{{ route("admin") }}';
                        }
                        else {
                            swal("Opps !", data.message, "error");
                        }                      
                    }
                });

            }
            else {
                swal("Opps !", "Please enter username and password", "error");
            }
        
        });

    </script>

</body>
</html>
