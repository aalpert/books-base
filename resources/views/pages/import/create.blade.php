@extends('admin')

@section('content')
    <h2>
        <a href="{{route('import.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        Запустить импорт
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('import.store')}}" enctype="multipart/form-data">
            {{csrf_field()}}

            <div class="form-group">
                <label for="bookSource">Источник</label>
                <select class="form-control" name="source" id="bookSource">
                    @foreach($sources->all() as $source)
                        <option value="{{$source->id}}">{{$source->title}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="fileUpload">Загрузить CSV</label>
                <input type="file" class="form-control-file" name="pricelist" id="fileUpload">
            </div>

            <div class="form-group">
                <label>Издательства:</label>
                <input type="text" class="form-control"
                       value="" name="publishers"
                       id="importPublishers">
            </div>

            <div class="form-group">
                <label>Очистка базы</label>
                <select class="form-control">
                    <option value="all" selected>По всему источнику</option>
                    <option value="publishers">По выбранным издательствам</option>
                    <option value="none">Не выполнять очистку</option>
                </select>
                <small class="form-text text-muted" name="clear">
                    Книги, которых нет в прайсе, будут помечены как не в наличии и будут удалены.
                </small>
            </div>
            </div>

            <button type="submit" class="btn btn-primary">Импортировать</button>
        </form>
    </div>
@endsection



@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>

    <script>
        $('#importPublishers').selectize({
            delimiter: '||',
            persist: false,
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        });

    </script>
@endsection
