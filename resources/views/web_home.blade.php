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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

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
.nav-tabs .nav-link{
    color:black;
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
    <div class="mx-auto justify-content-center d-flex flex-column" style="margin-top:11rem">
        <nav class="mx-auto justify-content-center">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button class="nav-link active" id="nav-choice-service-tab" data-bs-toggle="tab" data-bs-target="#nav-choice-service" type="button" role="tab" aria-controls="nav-choice-service" aria-selected="true">Choice service</button>
              <button class="nav-link" id="nav-Refund-tab" data-bs-toggle="tab" data-bs-target="#nav-Refund" type="button" role="tab" aria-controls="nav-Refund" aria-selected="false">Refund</button>
              <button class="nav-link" id="nav-send-sms-tab" data-bs-toggle="tab" data-bs-target="#nav-send-sms" type="button" role="tab" aria-controls="nav-send-sms" aria-selected="false">Send SMS</button>
            </div>
        </nav>
        <div class="tab-content mx-auto justify-content-center" id="nav-tabContent" style="margin-top:2rem">
            <div class="tab-pane fade show active" id="nav-choice-service" role="tabpanel" aria-labelledby="nav-choice-service-tab">
                @include('_partials.new_service')
            </div>
            <div class="tab-pane fade" id="nav-Refund" role="tabpanel" aria-labelledby="nav-Refund-tab">
                @include('_partials.refund')
            </div>
            <div class="tab-pane fade" id="nav-send-sms" role="tabpanel" aria-labelledby="nav-send-sms-tab">
                @include('_partials.send-sms') 
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>
