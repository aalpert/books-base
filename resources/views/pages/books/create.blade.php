@extends('admin')

@section('content')
    <h2>
        <a href="{{route('book.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        Создать книгу
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('book.store')}}">
            {{csrf_field()}}

            <div class="form-group">
                <label for="bookTitle">Название</label>
                <input type="text" class="form-control" name="title" id="bookTitle">
            </div>

            <div class="form-group">
                <label for="bookAuthor">Автор</label>
                <input type="text" class="form-control" name="author" id="bookAuthor">
            </div>

            <div class="form-group">
                <label for="bookSeries">Серия</label>
                <input type="text" class="form-control" name="series" id="bookSeries">
            </div>

            <div class="form-group">
                <label for="bookCategory">Жанр</label>
                <input type="text" class="form-control" name="category" id="bookCategory">
            </div>

            <div class="form-group">
                <label for="bookPublisher">Издательство</label>
                <input type="text" class="form-control" name="publisher" id="bookPublisher">
            </div>

            <div class="form-group">
                <label for="bookDescription">Описание</label>
                <textarea class="form-control" id="bookDescription" name="description" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="bookIsbn">ISBN</label>
                <input type="text" class="form-control" name="isbn" id="bookIsbn">
            </div>

            <div class="form-group">
                <label for="bookYear">Год издания</label>
                <input type="text" class="form-control" name="year" id="bookYear">
            </div>

            <div class="form-group">
                <label for="bookPages">Страниц</label>
                <input type="text" class="form-control" name="pages" id="bookPages">
            </div>

            <div class="form-group">
                <label for="bookFormat">Формат</label>
                <input type="text" class="form-control" name="format" id="bookFormat">
            </div>

            <div class="form-group">
                <label for="bookFormat">Переплет</label>
                <input type="text" class="form-control" name="bookbinding" id="bookBookbinding">
            </div>

            <fieldset class="card form-group p-2">
                <legend><small>Цены</small></legend>
                @foreach($sources->all() as $source)
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{$source->title}}</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="price[{{$source->id}}]" value="">
                        </div>
                    </div>
                @endforeach
            </fieldset>

            <div class="form-group">
                <label>Наличие</label>
                <select class="form-control" name="availability">
                    <option value="A">@lang('book.availability_A')</option>
                    <option value="Z">@lang('book.availability_Z')</option>
                    <option value="AN">@lang('book.availability_AN')</option>
                    <option value="SB">@lang('book.availability_SB')</option>
                    <option value="NVN">@lang('book.availability_NVN')</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>

    <script>
        $('#bookAuthor').selectize({
            delimiter: ',',
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
