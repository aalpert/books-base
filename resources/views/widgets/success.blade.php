@if($success_message = session('success_message'))
    <div class="alert alert-success alert-dismissible fade show">
        {{$success_message}}

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
