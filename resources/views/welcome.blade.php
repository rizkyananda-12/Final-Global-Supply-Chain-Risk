<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Risk Intelligence</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            color: #f8fafc;
        }
        .card-custom {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            border-color: #38bdf8;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">

    <div class="container text-center">
        <div class="mb-5">
            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">v1.0 Platform</span>
            <h1 class="fw-bold display-4 text-white">Global Supply Chain Risk</h1>
            <p class="text-secondary lead mx-auto" style="max-width: 600px;">
                Sistem pemantauan risiko rantai pasok global, indikator ekonomi negara, data pelabuhan, dan pemantauan cuaca real-time.
            </p>
        </div>

        <div class="row g-4 justify-content-center max-w-4xl mx-auto">
            <div class="d-flex align-items-center gap-2 flex-wrap">

            <div class="col-md-5">
                <div class="card card-custom h-100 p-4 text-start rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-20 p-3 rounded-3 text-primary me-3">
                            📊
                        </div>
                        <h4 class="fw-bold mb-0 text-white">Risk Dashboard</h4>
                    </div>
                    <p class="text-muted small">
                        Akses indikator risiko negara, statistik pelabuhan aktif, inflasi, GDP, serta pantauan cuaca otomatis.
                    </p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary w-100 fw-semibold mt-auto py-2">
                        Buka Dashboard &rarr;
                    </a>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card card-custom h-100 p-4 text-start rounded-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-20 p-3 rounded-3 text-success me-3">
                            🔐
                        </div>
                        <h4 class="fw-bold mb-0 text-white">Akses Akun</h4>
                    </div>
                    <p class="text-muted small">
                        Masuk ke akun pengguna atau administrator untuk mengelola data master negara dan pelabuhan.
                    </p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light w-100 fw-semibold mt-auto py-2">
                            Masuk sebagai {{ auth()->user()->name }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success w-100 fw-semibold mt-auto py-2">
                            Login Sistem
                        </a>
                    @endauth
                </div>
            </div>

        </div>

        <div class="mt-5 text-muted small">
            &copy; {{ date('Y') }} Risk Intelligence Engine. All rights reserved.
        </div>
    </div>

</body>
</html>