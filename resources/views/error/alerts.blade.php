@if ($errors->any())
    <x-alert type="danger" title="There were errors with your submission:">
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
