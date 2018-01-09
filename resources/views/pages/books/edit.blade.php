@extends('admin')

@section('content')
    <h2>
        <a href="{{route('book.list')}}" class="mr-2"><i class="fas fa-long-arrow-alt-left"></i></a>
        Редактировать книгу
    </h2>
    <div class="card p-2">
        <form method="post" action="{{route('book.update')}}">
            {{method_field('PATCH')}}
            {{csrf_field()}}

            <input type="hidden" name="id" value="{{$book->id}}">

            <div class="form-group">
                <label for="bookTitle">Название</label>
                <input type="text" class="form-control" value="{{$book->title}}" name="title" id="bookTitle">
            </div>

            <div class="form-group">
                <label for="bookAuthor">Автор</label>
                <input type="text" class="form-control"
                       value="{{ implode(', ', $book->authors()->pluck('name')->all()) }}"
                       name="author" id="bookAuthor">
            </div>

            <div class="form-group">
                <label for="bookSeries">Серия</label>
                <input type="text" class="form-control" value="{{$book->series['title']}}" name="series"
                       id="bookSeries">
            </div>

            <div class="form-group">
                <label for="bookCategory">Жанр</label>
                <input type="text" class="form-control"
                       value="{{ implode('||', $book->categories()->pluck('title')->all()) }}" name="category"
                       id="bookCategory">
            </div>

            <div class="form-group">
                <label for="bookPublisher">Издательство</label>
                <input type="text" class="form-control" name="publisher" value="{{$book->publisher['title']}}"
                       id="bookPublisher">
            </div>

            <div class="form-group">
                <label for="bookDescription">Описание</label>
                <textarea class="form-control" id="bookDescription" name="description"
                          rows="3">{{$book->description}}</textarea>
            </div>

            <div class="form-group">
                <label for="bookIsbn">ISBN</label>
                <input type="text" class="form-control" name="isbn" value="{{$book->isbn}}" id="bookIsbn">
            </div>

            <div class="form-group">
                <label for="bookYear">Год издания</label>
                <input type="text" class="form-control" name="year" value="{{$book->year}}" id="bookYear">
            </div>

            <div class="form-group">
                <label for="bookPages">Страниц</label>
                <input type="text" class="form-control" name="pages" value="{{$book->pages}}" id="bookPages">
            </div>

            <div class="form-group">
                <label for="bookFormat">Формат</label>
                <input type="text" class="form-control" name="format" value="{{$book->format}}" id="bookFormat">
            </div>

            <div class="form-group">
                <label for="bookFormat">Переплет</label>
                <input type="text" class="form-control" name="bookbinding" value="{{$book->bookbinding}}" id="bookBookbinding">
            </div>

            <div class="form-group">
                <label for="bookPrice">Цена</label>
                <input type="text" class="form-control" name="price" value="{{$book->price}}" id="bookPrice">
            </div>

            <div class="form-group">
                <label>Наличие</label>
                <select class="form-control" name="availability">
                    <option value="A" @if($book->availability == 'A') selected="selected" @endif>@lang('book.availability_A')</option>
                    <option value="Z" @if($book->availability == 'Z') selected="selected" @endif>@lang('book.availability_Z')</option>
                    <option value="AN" @if($book->availability == 'AN') selected="selected" @endif>@lang('book.availability_AN')</option>
                    <option value="SB" @if($book->availability == 'SB') selected="selected" @endif>@lang('book.availability_SB')</option>
                    <option value="NVN" @if($book->availability == 'NVN') selected="selected" @endif>@lang('book.availability_NVN')</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bookSource">Источник</label>
                <select class="form-control" name="source" id="bookSource">
                    @foreach($sources->all() as $source)
                        <option value="{{$source->id}}"
                                @if($source->id === $book->source_id) selected="selected" @endif>{{$source->title}}</option>
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
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        });

        $('#bookCategory').selectize({
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


@section('styles')
@endsection
