@extends('admin')

@section('content')
    <h2>Издательства</h2>
    <div class="card">
        @if(count($publishers))
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Книг</th>
                    <th scope="col" width="1%">&nbsp</th>
                </tr>
                </thead>
                <tbody>
                @foreach($publishers->all() as $publisher)
                    <tr>
                        <th scope="row">{{$publisher->id}}</th>
                        <td><a href="{{route('publisher.show', $publisher)}}">{{$publisher->title}}</a></td>
                        <td>{{$publisher->books->count()}}</td>
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
                {{$publishers->links()}}
            </div>
        @else
            <div class="text-center">
                Издательств нет
            </div>
        @endif
    </div>
@endsection
