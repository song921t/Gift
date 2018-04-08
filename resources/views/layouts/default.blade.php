<!doctype html>
<html lang="en">
<head>
    <title>@yield('title','Gift') - by 921t</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
@include('layouts._header')
<div class="container">
    <div class="col-md-offset-1 col-md-10">
        @include('shared._message')
        @yield('content')
        @include('layouts._footer')
    </div>
</div>
</body>
<script src="/js/app.js"></script>
</html>