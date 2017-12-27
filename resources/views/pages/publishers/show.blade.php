@extends('admin')

@section('content')
    <h2>
        <a href="{{route('publisher.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        {{$publisher->title}}
    </h2>

    @include('widgets.books-list')

@endsection