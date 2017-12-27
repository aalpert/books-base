@extends('admin')

@section('content')
    <h2>
        <a href="{{url('/sources')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        Редактировать источник
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('source.update')}}">
            {{method_field('PATCH')}}
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$source->id}}">
            <div class="form-group">
                <label for="exampleInputEmail1">Название</label>
                <input type="text" class="form-control" name="title" value="{{$source->title}}" id="sourceTitle">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
