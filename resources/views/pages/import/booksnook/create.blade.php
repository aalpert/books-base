@extends('admin')

@section('content')
    <h2>
        Новый импорт
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('import.store.booksnook')}}">
            {{csrf_field()}}
            <div class="form-group">
                <label>Host</label>
                <input type="text" class="form-control" name="host" required>
            </div>

            {{--<div class="form-group">--}}
                {{--<label>Token</label>--}}
                {{--<input type="text" class="form-control" name="token" required>--}}
            {{--</div>--}}

            <div class="form-group">
                <label for="bookSource">Источник</label>
                <select class="form-control" name="source" id="bookSource">
                    @foreach($sources->all() as $source)
                        <option value="{{$source->id}}">{{$source->title}}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Импортировать</button>
        </form>
    </div>
@endsection
