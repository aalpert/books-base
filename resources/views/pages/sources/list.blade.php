@extends('admin')

@section('content')
    <h2>Источники цен <a href="{{route('source.create')}}" class="ml-2"> <i class="far fa-plus-square"></i></a></h2>
    <div class="card">
        @if(count($sources))
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Драйвер</th>
                    <th scope="col">Книг</th>
                    <th scope="col">Сохранен</th>
                    <th scope="col" width="1%">&nbsp</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sources->all() as $source)
                    <tr>
                        <th scope="row">{{$source->id}}</th>
                        <td><a href="{{route('source.show', $source)}}">{{$source->title}}</a></td>
                        <td>{{$source->driver}}</td>
                        <td>{{$source->bookPrices->count()}}</td>
                        <td>{{$source->updated_at->diffForHumans()}}</td>
                        <td nowrap="">

                            <a href="{{route('source.edit', $source)}}" class="btn btn-outline-primary">
                                <i class="fas fa-pencil-alt"></i>
                                <span class="ml-2 d-none d-md-inline-block">Изменить</span>
                            </a>

                            <form method="post" class="d-inline" action="{{route('source.delete')}}">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                                <input type="hidden" name="id" value="{{$source->id}}">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                    <span class="ml-2 d-none d-md-inline-block">Удалить</span>
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center">
                Источников данных нет
            </div>
        @endif
    </div>
@endsection
