@extends('layout.default')

@section('title', $title ?? 'Dashboard')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-alt alert-success solid alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-alt alert-danger solid alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('page_content')
</div>
@endsection
