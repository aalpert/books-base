<div class="card">

    <form method="get">
        <div class="row">
            <div class="col-md-3 my-2">
                <input class="form-control" type="text" placeholder="Название" name="title" value="{{request('title')}}">
            </div>
            <div class="col-md-3 my-2">
                <input class="form-control" type="text" placeholder="ISBN" name="isbn" value="{{request('isbn')}}">
            </div>
            <div class="col-md-3 my-2">
                <select class="form-control" name="availability">
                    <option value="">-- Наличие --</option>
                    <option value="A" @if(request('availability') == 'A') selected="selected" @endif>@lang('book.availability_A')</option>
                    <option value="Z" @if(request('availability') == 'Z') selected="selected" @endif>@lang('book.availability_Z')</option>
                    <option value="AN" @if(request('availability') == 'AN') selected="selected" @endif>@lang('book.availability_AN')</option>
                    <option value="SB" @if(request('availability') == 'SB') selected="selected" @endif>@lang('book.availability_SB')</option>
                    <option value="NVN" @if(request('availability') == 'NVN') selected="selected" @endif>@lang('book.availability_NVN')</option>
                </select>
            </div>
            <div class="col-md-1 my-2">
                <input type="submit" class="btn btn-primary" value="Фильтровать">
            </div>
        </div>
    </form>

    @if(count($books))
        <table class="table table-hover table-responsive">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Название</th>
                <th scope="col">Автор</th>
                <th scope="col">Серия</th>
                <th scope="col">Издательство</th>
                <th scope="col" width="1%">&nbsp</th>
            </tr>
            </thead>
            <tbody>
            @foreach($books->all() as $book)
                <tr>
                    <th scope="row">{{$book->id}}</th>
                    <td>
                        <a href="" data-toggle="modal" data-target="#previewModal" data-title="{{$book->title}}"
                           data-book-id="{{$book->id}}">
                            {{$book->title}}
                        </a>
                    </td>
                    <td>
                        {{ implode(', ', $book->authors()->pluck('name')->all()) }}
                    </td>
                    <td>
                        {{ $book->series['title'] }}
                    </td>
                    <td>
                        {{ implode(', ', $book->publishers()->pluck('title')->all()) }}
{{--                        {{ $book->publisher['title'] }}--}}
                    </td>
                    <!-- Action Items -->
                    <td nowrap="">

                        <a href="{{route('book.edit', $book)}}" class="btn btn-outline-primary">
                            <i class="fas fa-pencil-alt"></i>
                            <span class="ml-2 d-none d-md-inline-block">Изменить</span>
                        </a>

                        <form method="post" class="d-inline" action="{{route('book.delete')}}">
                            {{csrf_field()}}
                            {{method_field('DELETE')}}
                            <input type="hidden" name="id" value="{{$book->id}}">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i>
                                <span class="ml-2 d-none d-md-inline-block">Удалить</span>
                            </button>
                        </form>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="row justify-content-center">
            {{$books->appends([
            'title'=>request('title'),
            'isbn'=>request('isbn'),
            'availability'=>request('availability'),
            ])->links()}}
        </div>
    @else
        <div class="text-center">
            Книг нет
        </div>
    @endif
</div>


<!-- Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $('#previewModal').on('show.bs.modal', function (event) {
            // event.preventDefault();
            var bookLink = $(event.relatedTarget);
            var modal = $(this);
            modal.find('.modal-title').text(bookLink.data('title'));
            modal.find('.modal-body').html('<i class="fas fa-spinner fa-spin"></i>');
            axios.get('/book/'+bookLink.data('book-id')).then(function(response) {
                modal.find('.modal-body').html(response.data);
            });
        })
    </script>
@endsection
