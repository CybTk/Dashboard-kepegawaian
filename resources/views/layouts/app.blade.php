<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepegawaian UNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .unp-header { background-color: #FFD700; border-bottom: 5px solid #003366; padding: 10px; text-align: center; }
        .unp-title { font-weight: bold; color: #000; text-transform: uppercase; margin: 0; }
    </style>
</head>
<body>

<div class="unp-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-2">
                <img src="{{ asset('img/logo_unp.png') }}" alt="Logo" width="80">
            </div>
            <div class="col-md-8">
                <h2 class="unp-title">Dashboard Analisis Data Kepegawaian</h2>
                <h4 class="unp-title">Universitas Negeri Padang</h4>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-3">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>