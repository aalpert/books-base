@extends('admin')

@section('content')
    <div class="row">
        <div class="col-md-4 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Книг</h5>
                    <h3 class="card-text"><a href="{{route('book.list')}}">{{$books}}</a></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Авторов</h5>
                    <h3 class="card-text"><a href="{{route('author.list')}}">{{$authors}}</a></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Серий</h5>
                    <h3 class="card-text"><a href="{{route('series.list')}}">{{$series}}</a></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Категорий</h5>
                    <h3 class="card-text"><a href="{{route('category.list')}}">{{$categories}}</a></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Издательств</h5>
                    <h3 class="card-text"><a href="{{route('publisher.list')}}">{{$publisers}}</a></h3>
                </div>
            </div>
        </div>
    </div>
@endsection
