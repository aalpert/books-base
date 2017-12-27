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
                <label for="bookPrice">Цена</label>
                <input type="text" class="form-control" name="price" id="bookPrice">
            </div>

            <div class="form-group">
                <label for="bookSource">Источник</label>
                <select class="form-control" name="source" id="bookSource">
                    @foreach($sources->all() as $source)
                        <option value="{{$source->id}}">{{$source->title}}</option>
                    @endforeach
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
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    </script>
@endsection
