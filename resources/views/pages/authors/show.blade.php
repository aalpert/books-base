@extends('admin')

@section('content')
    <h2>
        <a href="{{route('author.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        {{$author->name}}
    </h2>

    <p>
        {{$author->description}}
    </p>

    @include('widgets.books-list')

@endsection
