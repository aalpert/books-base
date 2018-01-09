<ul class="nav nav-tabs mb-3" id="bookTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#book-info" role="tab">Информация</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#book-price" role="tab">Аудит</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="book-info" role="tabpanel">
        <!-- book-info -->
        <div class="row">
            <div class="col-md-3 text-center">
                <img src="{{route('book.gallery', $book)}}" class="w-100">
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
                    <dd class="col-sm-10">{{$book->publisher['title']}}</dd>
                </dl>
            </div>
            <div class="col-md-4">
                <dl>
                    <dt class="col-sm-2">Цена:</dt>
                    <dd class="col-sm-10">{{$book->price}}</dd>

                    <dt class="col-sm-2">Формат:</dt>
                    <dd class="col-sm-10">{{$book->format}}</dd>

                    <dt class="col-sm-2">Год:</dt>
                    <dd class="col-sm-10">{{$book->year}}</dd>

                    <dt class="col-sm-2">Страниц:</dt>
                    <dd class="col-sm-10">{{$book->pages}}</dd>

                    <dt class="col-sm-2">Добавлена:</dt>
                    <dd class="col-sm-10">{{$book->created_at->diffForHumans()}}</dd>
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
        @if(count($book->history))
            <table class="table table-sm">
                <thead class="thead-dark">
                <tr>
                    <th>Дата</th>
                    <th>Тип</th>
                    <th>Значение</th>
                </tr>
                </thead>
                <tbody>
                @foreach($book->history->sortByDesc('created_at') as $ph)
                    <tr>
                        <td>{{$ph->created_at->format('d/m/Y H:i:s')}}</td>
                        <th scope="row">
                            @if($ph->type == 'source')
                                Источник
                            @elseif($ph->type == 'price')
                                Цена
                            @elseif($ph->type == 'availability')
                                Наличие
                            @endif
                        </th>
                        <th scope="row">
                            @if($ph->type == 'availability')
                                @lang('book.availability_'.$ph->value)
                            @else
                                {{$ph->value}}
                            @endif
                        </th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
