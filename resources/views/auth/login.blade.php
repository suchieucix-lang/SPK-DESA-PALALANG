<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - SPK Desa Palalang</title>
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 50%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            color: #334155;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(148, 163, 184, 0.15);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            padding: 2.2rem 2rem 1.8rem;
            text-align: center;
            color: #ffffff;
        }

        .login-logo {
            width: 56px;
            height: 56px;
            background: #ffffff;
            color: #0284c7;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.1);
            margin-bottom: 0.8rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
        }

        .btn-primary-soft {
            background: #0284c7;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-primary-soft:hover {
            background: #0369a1;
            transform: translateY(-1px);
            color: #ffffff;
        }

        .login-footer {
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <h5 class="fw-bold mb-1">SPK DESA PALALANG</h5>
            <p class="mb-0 text-white-50" style="font-size: 0.82rem;">
                Penetapan Prioritas Usulan Koreksi DTKS (PKH & BPNT)
            </p>
        </div>

        <div class="p-4 p-sm-4">
            <div class="text-center mb-3">
                <h6 class="fw-bold text-dark mb-1">Masuk ke Sistem</h6>
                <small class="text-muted">Akses khusus Perangkat Desa Palalang</small>
            </div>

            @if(session('success'))
                <div class="alert alert-success py-2 px-3 rounded-3 text-sm mb-3" style="font-size: 0.85rem; background-color: #ecfdf5; color: #047857;">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('login'))
                <div class="alert alert-danger py-2 px-3 rounded-3 text-sm mb-3" style="font-size: 0.85rem; background-color: #fff1f2; color: #be123c;">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first('login') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="login" class="form-control" placeholder="admin" value="{{ old('login', 'admin') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="passwordField" class="form-control" placeholder="password" value="password" required>
                        <button class="btn btn-light border" type="button" id="togglePassword">
                            <i class="bi bi-eye text-muted"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                        <label class="form-check-label text-muted" for="rememberMe" style="font-size: 0.82rem;">
                            Ingat Sesi Saya
                        </label>
                    </div>
                    <span class="badge rounded-pill border" style="background-color: #f1f5f9; color: #475569; font-size: 0.72rem;">Admin Desa</span>
                </div>

                <button type="submit" class="btn btn-primary-soft w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>

            <div class="mt-3 p-2.5 rounded-3 text-center border" style="background-color: #f8fafc;">
                <small class="text-muted d-block" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    Akses Login: Username <code>admin</code> &bull; Password <code>password</code>
                </small>
            </div>
        </div>

        <div class="login-footer">
            Pemerintah Desa Palalang, Kecamatan Pakong, Kabupaten Pamekasan
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const pass = document.getElementById('passwordField');
            const icon = this.querySelector('i');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pass.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
