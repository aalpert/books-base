<ul class="nav nav-tabs mb-3" id="bookTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#book-info" role="tab">Информация</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#book-price" role="tab">Цены</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="book-info" role="tabpanel">
        <!-- book-info -->
        <div class="row">
            <div class="col-md-3 text-center">
                @if($book->image)
                    <img src="{{route('book.gallery', $book)}}" class="w-100">
                @else
                    Изображения нет
                @endif
            </div>
            <div class="col-md-5">
                <dl>
                    <dt class="col-sm-2">Автор:</dt>
                    <dd class="col-sm-10">{{implode(', ', $book->authors()->pluck('name')->all())}}</dd>
                </dl>

                <dl>
                    <dt class="col-sm-2">Серия:</dt>
                    <dd class="col-sm-10">{{$book->series['title']}}</dd>
                </dl>

                <dl>
                    <dt class="col-sm-2">Жанр:</dt>
                    <dd class="col-sm-10">{{implode(', ', $book->categories()->pluck('title')->all())}}</dd>
                </dl>

                <dl>
                    <dt class="col-sm-2">Издательство:</dt>
                    <dd class="col-sm-10">{{implode(', ', $book->publishers()->pluck('title')->all())}}</dd>
                </dl>
            </div>
            <div class="col-md-4">
                <dl>
                    @foreach($book->details as $detail => $value)
                        <dt class="col-sm-2">{{$detail}}:</dt>
                        <dd class="col-sm-10">{{$value}}</dd>
                    @endforeach
                    {{--<dt class="col-sm-2">Формат:</dt>--}}
                    {{--<dd class="col-sm-10">{{$book->details['format']}}</dd>--}}

                    {{--<dt class="col-sm-2">Год:</dt>--}}
                    {{--<dd class="col-sm-10">{{$book->year}}</dd>--}}

                    {{--<dt class="col-sm-2">Страниц:</dt>--}}
                    {{--<dd class="col-sm-10">{{$book->details['pages']}}</dd>--}}

                    {{--<dt class="col-sm-2">Добавлена:</dt>--}}
                    {{--<dd class="col-sm-10">{{$book->created_at->diffForHumans()}}</dd>--}}
                </dl>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <hr>
                {!! $book->description !!}
            </div>
        </div>
        <!-- END: book-info -->
    </div>
    <div class="tab-pane fade" id="book-price" role="tabpanel">
        <h5>@lang('book.availability_'.$book->availability)</h5>
        @if(count($book->prices))
            <table class="table table-sm">
                <thead class="thead-dark">
                <tr>
                    <th>Доступна с</th>
                    <th>Источник</th>
                    <th>Цена</th>
                </tr>
                </thead>
                <tbody>
                @foreach($book->prices->sortByDesc('created_at') as $bp)
                    <tr>
                        <td>{{$bp->available_at->format('d/m/Y')}}</td>
                        <td scope="row">
                            {{$bp->source['title']}}
                        </td>
                        <td scope="row">
                            {{$bp->price}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            Информации о ценах нет
        @endif
    </div>
</div>
