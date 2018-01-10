@extends('admin')

@section('content')
    <h2>
        <a href="{{route('source.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        {{$source->title}}
    </h2>

    @include('widgets.books-list')

@endsection
