<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{csrf_token()}}">

    <title>Books Base</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    @yield('style')
</head>

<body>

@include('widgets/header')

<main role="main" class="container">

    @include('widgets/errors')

    @include('widgets/success')

    @yield('content')

</main>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script defer src="https://use.fontawesome.com/releases/v5.0.1/js/all.js"></script>
<script src="{{asset('js/app.js')}}"></script>

@yield('scripts')

</body>
</html>
