@if (session('success'))
    <x-alert type="success" title="Success!" message="{{ session('success') }}" />
@endif

@if (session('error'))
    <x-alert type="danger" title="Error!" message="{{ session('error') }}" />
@endif

@if (session('warning'))
    <x-alert type="warning" title="Warning!" message="{{ session('warning') }}" />
@endif

@if (session('info'))
    <x-alert type="info" title="Info!" message="{{ session('info') }}" />
@endif

