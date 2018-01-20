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
</head>

<body>

<main role="main" class="container">
    <div class="row card p-5">

        @include('widgets/errors')

        <div class="col-6">
            <h2>Войти</h2>
            <form method="POST" action="{{route('auth.login')}}">
                {{csrf_field()}}
                <div class="form-group">
                    <label>Имя пользователя</label>
                    <input type="text" class="form-control" value="" name="username">
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" class="form-control" value="" name="password">
                </div>

                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="remember" checked>
                        Запомнить
                    </label>
                    <button type="submit" class="btn btn-primary float-right">Войти</button>
                </div>

            </form>
        </div>
    </div>
</main>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script defer src="https://use.fontawesome.com/releases/v5.0.1/js/all.js"></script>

</body>
</html>
