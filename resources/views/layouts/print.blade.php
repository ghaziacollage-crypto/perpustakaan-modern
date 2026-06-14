<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Print') - {{ app_setting('app_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Fredoka+One&display=swap" rel="stylesheet">
    @stack('custom-css')
    <style>
        :root {
            --comic-cream: #FFF8F0;
            --comic-orange: #FF6B35;
            --comic-dark: #1A1A2E;
            --comic-blue: #4ECDC4;
            --comic-yellow: #FFE66D;
            --comic-red: #FF3366;
            --comic-green: #00C896;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{{ asset('kop.png') }}" alt="Kop Surat" style="max-width:100%; height:auto;" />
    </div>
    @yield('content')
    <div style="text-align: center; margin-top: 40px; page-break-inside: avoid;">
        <div>Kepala Perpustakaan</div>
        <div style="height:60px;"></div>
        <div style="font-weight:bold; text-decoration:underline;">
            Ailen Rossa Nauda, M.Pd.
        </div>
        <div>NIP. 196904061998022001</div>
    </div>
</body>
</html>