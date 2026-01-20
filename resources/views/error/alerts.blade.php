@if ($errors->any())
    <div class="alert alert-alt alert-danger solid alert-dismissible fade show" role="alert">
        <strong>There were errors with your submission:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

