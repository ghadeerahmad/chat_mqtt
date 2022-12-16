<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylsheet" href="{{ mix('css/app.css')}}"/>
    </head>
    <body class="antialiased">
        <div id="app"></div>
    <script src="{{mix('js/app.js')}}"></script>
    </body>
</html>
