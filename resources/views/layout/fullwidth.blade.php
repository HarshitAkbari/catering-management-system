@php
    $controller = \App\Helper\DzHelper::controller();
    $action = \App\Helper\DzHelper::action();
@endphp


<!DOCTYPE html>
<html lang="en" class="h-100">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
    <meta property="og:title" content="GetSkills  : GetSkills Online Learning  Admin Laravel Template" />
    <meta property="og:description" content="GetSkills  : GetSkills Online Learning  Admin Laravel Template" />
    <meta property="og:image" content="https://getskills.dexignzone.com/xhtml/social-image.png" />
    <meta name="format-detection" content="telephone=no">
    <title>{{ config('dz.name') }} | @yield('title', $page_title ?? '')</title>
    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
            @yield('content')
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->

    @php
        $action = isset($action) ? $controller.'_'.$action : 'dashboard_1';
    @endphp
    
    @if(!empty(config('dz.public.global.js.top')))
        @foreach(config('dz.public.global.js.top') as $script)
                <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.pagelevel.js.'.$action)))
        @foreach(config('dz.public.pagelevel.js.'.$action) as $script)
                <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.global.js.bottom')))
        @foreach(config('dz.public.global.js.bottom') as $script)
                <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif

    @yield('scripts')


</body>
</html>