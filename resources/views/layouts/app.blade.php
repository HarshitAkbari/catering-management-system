@extends('layout.default')

@section('title', $title ?? 'Dashboard')

@section('content')
<div class="container-fluid">
    @include('components.flash-messages')

    @yield('page_content')
</div>
@endsection
