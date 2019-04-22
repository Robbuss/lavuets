<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
    <title>Application</title>
    <!-- Fonts -->
</head>

<body>
    <div id="app">
        <router-component></router-component>
    </div>
    <script src="/js/app.js"></script>
</body>

</html> 