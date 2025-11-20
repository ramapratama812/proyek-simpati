<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portofolio')</title>

    {{-- CSS dari app tetap bisa dipakai --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body style="background: white; margin:0; padding:0;">

    {{-- INI UNTUK ISI HALAMAN --}}
    <div>
        @yield('content')
    </div>

</body>
</html>
