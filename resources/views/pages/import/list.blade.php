@extends('admin')

@section('content')
    <h2>История импорта <a href="{{route('import.create')}}" class="ml-2"> <i class="far fa-plus-square"></i></a></h2>
    <div class="card">
        @if(count($imports))
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Источник</th>
                    <th scope="col">Обработано</th>
                    <th scope="col">Пропущено</th>
                    <th scope="col">Появились</th>
                    <th scope="col">Создано</th>
                    <th scope="col">Обновлено</th>
                    <th scope="col">Исчезло</th>
                    <th scope="col">Статус</th>
                    <th scope="col" width="1%">&nbsp</th>
                </tr>
                </thead>
                <tbody>
                @foreach($imports->all() as $import)
                    <tr>

                        <th scope="row">{{$import->id}}</th>
                        <td><a href="{{route('import.show', $import)}}">{{$import->source->title}}</a></td>

                        <td>{{$import->total}}</td>
                        <td>{{$import->skipped}}</td>
                        <td>{{$import->appeared}}</td>
                        <td>{{$import->created}}</td>
                        <td>{{$import->updated}}</td>
                        <td>{{$import->removed}}</td>

                        <td>
                            @if($import->status === 'started')
                                Обрабатывается... <i class="fas fa-sync fa-spin"></i>
                            @else
                                Завершен
                            @endif
                        </td>
                        <td nowrap="">

                            <a href="{{route('import.getPriceList', $import)}}" class="btn btn-outline-primary">
                                <i class="fas fa-cloud-download-alt"></i>
                            </a>

                            <form method="post" class="d-inline" action="{{route('import.clean', $import)}}">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="fas fa-recycle"></i>
                                </button>
                            </form>

                            {{--<form method="post" class="d-inline" action="{{route('import.delete')}}">--}}
                                {{--{{csrf_field()}}--}}
                                {{--{{method_field('DELETE')}}--}}
                                {{--<input type="hidden" name="id" value="{{$import->id}}">--}}
                                {{--<button type="submit" class="btn btn-danger">--}}
                                    {{--<i class="fas fa-trash-alt"></i>--}}
                                    {{--<span class="ml-2 d-none d-md-inline-block"></span>--}}
                                {{--</button>--}}
                            {{--</form>--}}

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center">
                Ничего не импортировалось
            </div>
        @endif
    </div>
@endsection
