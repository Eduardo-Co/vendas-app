<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    @livewireStyles
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">


    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            max-width: 120px; 
            height: auto;
        }
        .brand-text {
            display: flex;
            align-items: baseline;
            font-size: 1.5rem;
        }
        .brand-text .brand-vendas {
            color: #27292b; 
            font-weight: 700; 
            margin-right: 0.2rem;
        }
        .brand-text .brand-app {
            color: #6c757d;
            font-style: italic; 
            font-size: 1.25rem; 
        }
    </style>
</head>
<body>

    @yield('content')

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @livewireScripts
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
