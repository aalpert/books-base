@extends('admin')

@section('content')
    <h2>Серии</h2>
    <div class="card">
        @if(count($series))
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Издательство</th>
                    <th scope="col">Книг</th>
                    <th scope="col" width="1%">&nbsp</th>
                </tr>
                </thead>
                <tbody>
                @foreach($series->all() as $s)
                    <tr>
                        <th scope="row">{{$s->id}}</th>
                        <td><a href="{{route('series.show', $s)}}">{{$s->title}}</a></td>
                        <td><a href="{{route('publisher.show', $s->publisher)}}">{{$s->publisher['title']}}</a></td>
                        <td>{{$s->books->count()}}</td>
                        <td nowrap="">

                            {{--<a href="{{route('source.edit', $source)}}" class="btn btn-outline-primary">--}}
                            {{--<i class="fas fa-pencil-alt"></i>--}}
                            {{--<span class="ml-2 d-none d-md-inline-block">Изменить</span>--}}
                            {{--</a>--}}

                            {{--<form method="post" class="d-inline" action="{{route('source.delete')}}">--}}
                            {{--{{csrf_field()}}--}}
                            {{--{{method_field('DELETE')}}--}}
                            {{--<input type="hidden" name="id" value="{{$source->id}}">--}}
                            {{--<button type="submit" class="btn btn-danger">--}}
                            {{--<i class="fas fa-trash-alt"></i>--}}
                            {{--<span class="ml-2 d-none d-md-inline-block">Удалить</span>--}}
                            {{--</button>--}}
                            {{--</form>--}}

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="row justify-content-center">
                {{$series->links()}}
            </div>
        @else
            <div class="text-center">
                Серий нет
            </div>
        @endif
    </div>
@endsection
