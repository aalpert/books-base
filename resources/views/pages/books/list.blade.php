@extends('admin')

@section('content')
    <h2>Книги <a href="{{route('book.create')}}" class="ml-2"> <i class="far fa-plus-square"></i></a></h2>

    @include('widgets.books-list')

@endsection

