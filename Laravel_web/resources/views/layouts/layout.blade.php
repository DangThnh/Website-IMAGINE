<!doctype html>
<html lang="en">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">  {{-- Ân thêm dòng này --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Custom Auth Laravel')</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/styleMainPage.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles') <!-- Thêm để chèn CSS từ index -->
  </head>
  <body>
    <div class ="font">
        <div class="header" style="background-color: #1741ca; padding: 20px;">
        <a href="{{ url('images/gallery') }}" onclick="return confirmLeave();" style="text-decoration: none; color: white;">
            <h1>IMAGINE</h1>
            <h2>Image engine, power your dream</h2>
        </a>
        </div>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
  </body>
</html>
