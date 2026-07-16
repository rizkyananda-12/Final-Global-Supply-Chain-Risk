<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Supply Chain Risk</title>
    <!-- Kita pakai Bootstrap CDN agar tampilannya rapi tanpa perlu instalasi rumit -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4 fw-bold text-primary">Admin Login</h3>
                        
                        <!-- Menampilkan pesan error jika email/password salah -->
                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                @foreach ($errors->all() as $error)
                                    <small class="d-block">{{ $error }}</small>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ url('/login') }}" method="POST">
                            <!-- Token keamanan wajib Laravel agar form bisa diproses -->
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Masukkan email admin" value="{{ old('email') }}" required autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
                        </form>
                    </div>
                </div>
                @if (session('success'))
                <div class="alert alert-success py-2">
                    <small class="d-block">{{ session('success') }}</small>
                </div>
                @endif
            </div>
            <form>
                <div class="text-center mt-3">
                    <a href="{{ url('/register') }}" class="text-decoration-none text-primary"><small>Belum punya akun? Daftar disini</small></a>
                </div>
                @if (session('success'))
                <div class="alert alert-success py-2 text-center">
                    <small class="fw-semibold">{{ session('success') }}</small>
                </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>