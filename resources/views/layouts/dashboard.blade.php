<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard-main.css') }}">

  <title>@yield('title')</title>
</head>
<body>
  @include('components.sidebar')
  @yield('content')


  <script src="{{ asset('js/style.js') }}"></script>
</body>
</html>
