<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard-main.css') }}">
  {{-- thêm styles riêng cho từng trang --}}
  @stack('styles')
   {{-- CSRF Token cho AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
  {{-- thêm scripts riêng cho từng trang --}}
  @stack('scripts')
  <script>
      axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').content;
  </script>
</body>
</html>
