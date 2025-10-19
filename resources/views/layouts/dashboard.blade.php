<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"> --}}


  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard-main.css') }}">
  
  <title>@yield('title')</title>
</head>
<body>
  @include('components.sidebar')
  <section id="content">
    @include('components.head')
    <main>
        @yield('content')
    </main>
  </section>

  <script src="{{ asset('js/style.js') }}"></script>
</body>
</html>
