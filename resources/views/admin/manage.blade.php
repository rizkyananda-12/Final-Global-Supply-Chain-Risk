<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="p-4">

    <div class="container">

        <div class="card card-custom p-3 mb-4 bg-white d-flex flex-row justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-0 text-dark">⚙️ Panel Kelola Data Admin</h3>
                <small class="text-muted">Kelola Data Master Negara & User Platform</small>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm fw-bold rounded-pill">
                ← Kembali ke Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">

            <div class="col-md-6">
                <div class="card card-custom p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-secondary">👥 Daftar Pengguna System</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                <tr>
                                    <td class="fw-bold">{{ $u->name }}</td>
                                    <td><small>{{ $u->email }}</small></td>
                                    <td>
                                        <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.user.role', $u->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                Ubah Role
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-secondary">🌍 Data Master Negara</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ISO</th>
                                    <th>Negara</th>
                                    <th>Mata Uang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $c)
                                <tr>
                                    <td><span class="badge bg-info text-dark">{{ $c->iso_code }}</span></td>
                                    <td class="fw-bold">{{ $c->name }}</td>
                                    <td>{{ $c->currency }}</td>
                                    <td>
                                        <form action="{{ route('admin.country.delete', $c->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data negara.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
<div class="col-md-12 mt-4">
    <div class="card card-custom p-4 bg-white">
        <h5 class="fw-bold mb-3 text-secondary">⚓ Data Pelabuhan Aktif</h5>
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Pelabuhan</th>
                    <th>Kode Negara</th>
                    <th>Koordinat (Lat, Long)</th>
                    <th>Status</th>
                    <th>Tingkat Risiko</th>
                </tr>
            </thead>
            <tbody>
               @foreach(\App\Models\Port::all() as $p)
               <tr>
                <td class="fw-bold">{{ $p->port_name }}</td>
                <td><span class="badge bg-secondary">{{ $p->country_code }}</span></td>
                <td><small>{{ $p->latitude }}, {{ $p->longitude }}</small></td>
                <td>
                    <span class="badge {{ $p->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td>{{ $p->risk_level ?? 10 }}%</td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>