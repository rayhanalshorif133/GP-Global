<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> GP Global | Service Check</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        /* remove increment and decrement btn from input number */
 input[type='number']::-webkit-inner-spin-button,
 input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
 }
 .required::after {
    content: " *";
    color: red;
    font-weight: bold;
}

.optional::after {
    content: " (optional)";
    color: #999;
    font-weight: normal;
}

.tk::after {
    content: "৳";
    color: #000;
    font-weight: normal;
}
    </style>
</head>

<body class="hold-transition">
    <div class="header">
        <div class="header__logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
        </div>
        <div class="header__title">
            <h1>
                {{env('APP_NAME')}}
            </h1>
        </div>
    </div>
    <div class="login-content">
        <div class="login-box">
            
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <div class="login-text">
                        Choice your service
                    </div>
                    {{-- <p class="login-box-msg">Sign in to start your session</p> --}}

                    <form method="POST" action="{{ route('service.subscription') }}">
                        @csrf
                        <div class="form-group">
                            <label class="required">Phone Number</label>
                            <input type="text" required class="form-control" name="phone_number" placeholder="+8801700000000"/>
                        </div>
                        <div class="form-group">
                            <label class="required">Select a service</label>
                            <select class="form-control" name="service_id">
                                <option value="" disable selected>
                                    Select a service    
                                </option>
                                @foreach ($services as $service)
                                    <option value="{{$service->id}}">{{$service->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary w-full">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
