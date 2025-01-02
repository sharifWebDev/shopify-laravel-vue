<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>{{env('APP_NAME')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(request()->has('embed'))
        @vite('resources/js/embedApp.js')
    @else
        @vite('resources/js/app.js')
    @endif

    @routes

    @inertiaHead

</head>
<body>

    @inertia

    <script>let vm = { host: '{{ \Request::get('host') }}' }</script>
</body>
</html>
