@extends('admin')

@section('content')
    <h2>
        <a href="{{route('source.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        Создать источник
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('source.store')}}">
            {{csrf_field()}}
            <div class="form-group">
                <label for="exampleInputEmail1">Название</label>
                <input type="text" class="form-control" name="title" id="sourceTitle">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
