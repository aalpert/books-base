@extends('admin')

@section('content')
    <h2>
        <a href="{{route('import.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        {{$import->source['title']}}
    </h2>
    <div class="card my-2 p-2">
        <dl class="row">
            <dt class="col-sm-2">Запущен:</dt>
            <dd class="col-sm-10">{{$import->created_at->diffForHumans()}}</dd>

            <dt class="col-sm-2">Завершен:</dt>
            <dd class="col-sm-10">
                @if($import->status === 'started')
                    Обрабатывается...
                @else
                    {{$import->updated_at->diffForHumans()}}
                @endif
            </dd>
        </dl>
        @if(!empty($import->params['limit_publishers']))
            <hr>
            <dl class="row">
                <dt class="col-sm-2">Издательства:</dt>
                <dd class="col-sm-10">
                    @foreach($import->params['limit_publishers'] as $publisher)
                        <span class="badge badge-secondary">{{$publisher}}</span>
                    @endforeach
                </dd>
            </dl>
        @endif
        <hr>
        <dl class="row">
            <dt class="col-sm-2">Обработано:</dt>
            <dd class="col-sm-10">{{$import->total}}</dd>

            <dt class="col-sm-2">Пропущено:</dt>
            <dd class="col-sm-10">{{$import->skipped}}</dd>

            <dt class="col-sm-2">Добавлено:</dt>
            <dd class="col-sm-10">{{$import->created}}</dd>

            <dt class="col-sm-2">Удалено:</dt>
            <dd class="col-sm-10">{{$import->removed}}</dd>
        </dl>
    </div>
    <ul class="nav nav-tabs my-3">
        <li class="nav-item">
            <a class="nav-link @if(request('status', 'created') == 'created') active @endif" href="?status=created">Добавлено</a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(request('status') == 'deleted') active @endif" href="?status=deleted">Удалено</a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(request('status') == 'updated') active @endif" href="?status=updated">Обновлено</a>
        </li>
    </ul>
    <div class="card">
        @if(count($logs))
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th scope="col">Название</th>
                    <th scope="col">isbn</th>
                    <th scope="col">Автор</th>
                    <th scope="col">Издательство</th>
                    <th scope="col">Цена</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs->all() as $log)
                    <tr>
                        <th scope="row">{{$log->title}}</th>
                        <td nowrap="">{{$log->isbn}}</td>
                        <td>{{$log->author}}</td>
                        <td>{{$log->publisher}}</td>
                        <td>{{ number_format($log->price, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="row justify-content-center">
                {{$logs->appends(['status' => request('status', 'created')])->links()}}
            </div>
        @else
            <div class="text-center">
                Записей нет
            </div>
        @endif
    </div>
@endsection
