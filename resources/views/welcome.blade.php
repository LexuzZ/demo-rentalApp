<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')

        <title>Laravel</title>

        
    </head>
    <body>
        <h1 class="font-bold text-2xl">Hello</h1>
    </body>
</html>
<script>
    window.addEventListener('notify', function (event) {
        const type = event.detail.type;
        const message = event.detail.message;

        // Gunakan notifikasi custom, misal Filament Toast, SweetAlert, atau Alpine
        alert(message); // Ganti dengan UI yang lebih bagus
    });
</script>
