@extends('admin')

@section('content')
    <h2>
        <a href="{{route('category.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        {{$category->title}}
    </h2>

    @include('widgets.books-list')

@endsection
