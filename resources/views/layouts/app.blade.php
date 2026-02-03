<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kepegawaian UNP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; }
        .unp-header { background-color: #FFD700; border-bottom: 5px solid #003366; padding: 10px; }
        .unp-title { font-weight: bold; color: #000; text-transform: uppercase; margin: 0; }
    </style>
</head>
<body>

<div class="unp-header shadow">
    <div class="container-fluid d-flex align-items-center">
        <img src="/img/logo_unp.png" alt="Logo UNP" style="width: 60px; margin-right: 15px;">
        <div>
            <h2 class="unp-title h4">Dashboard Analisis Data Kepegawaian</h2>
            <h4 class="unp-title h6">Universitas Negeri Padang</h4>
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