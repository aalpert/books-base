<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">

    <a class="navbar-brand" href="{{route('main')}}">Books Base</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="{{route('source.list')}}">Источники <span class="sr-only">(current)</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('book.list')}}">Книги</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('author.list')}}">Автора</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('series.list')}}">Серии</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('category.list')}}">Категории</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('publisher.list')}}">Издательства</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{route('import.list')}}">Импорт</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="">{{Auth::user()->name}}</a>
            </li>
            <li>
                <a class="nav-link" href="{{route('logout')}}"><i class="fas fa-sign-in-alt"></i></a>
            </li>
        </ul>

    </div>
</nav>
