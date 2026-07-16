<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin Baru - Supply Chain Risk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">Daftar Admin Baru</h3>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                @foreach ($errors->all() as $error)
                                    <small class="d-block">{{ $error }}</small>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ url('/register') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama Admin" value="{{ old('name') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="admin@example.com" value="{{ old('email') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password (Minimal 8 Karakter)</label>
                                <input type="password" name="password" class="form-control" placeholder="Buat password" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Daftar Sekarang</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ url('/login') }}" class="text-decoration-none"><small>Sudah punya akun? Login disini</small></a>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ url('/register') }}" class="text-decoration-none"><small>Belum punya akun? Daftar disini</small></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>