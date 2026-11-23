<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem Warehouse FG PT Indofood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --indo-navy: #0E2340;
            --indo-navy-deep: #09182D;
            --indo-blue: #00529B;
            --indo-blue-hover: #003F78;
            --indo-red: #D71920;
            --border-crisp: #D8E2EC;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
            background-color: #E8EEF5;
            color: #0F233C;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .login-shell {
            width: 100%;
            max-width: 1020px;
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-radius: 14px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            box-shadow: 0 18px 45px rgba(14, 35, 64, 0.14);
        }

        /* Top Indofood Corporate Accent Strip */
        .login-shell::before {
            content: "";
            grid-column: 1 / -1;
            height: 5px;
            background: linear-gradient(90deg, var(--indo-red) 0%, var(--indo-red) 32%, var(--indo-blue) 32%, #1B75C9 100%);
        }

        /* Left Corporate Brand Panel */
        .brand-side {
            background: var(--indo-navy);
            color: #FFFFFF;
            padding: 44px 42px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-logo-box {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            padding: 11px 16px;
            border-radius: 9px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .brand-logo-box img {
            width: 175px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .system-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 34px;
            padding: 5px 11px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 6px;
            color: #9EC5EC;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .system-tag-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--indo-red);
        }

        .brand-heading {
            margin: 16px 0 14px;
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: -0.025em;
        }

        .brand-desc {
            color: #BFD3E8;
            font-size: 13.5px;
            line-height: 1.7;
            margin: 0 0 28px;
            max-width: 410px;
        }

        .module-list {
            display: grid;
            gap: 10px;
        }

        .module-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 13px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 8px;
            font-size: 12.5px;
            color: #E2ECF7;
        }

        .module-item i {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--indo-blue);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .brand-bottom {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.10);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11.5px;
            color: #8CA9C9;
        }

        /* Right Form Panel */
        .form-side {
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 26px;
        }

        .form-header h2 {
            margin: 0 0 6px;
            color: var(--indo-navy);
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .form-header p {
            margin: 0;
            color: #64748B;
            font-size: 13px;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #1E3A5F;
            margin-bottom: 6px;
        }

        .input-group-text {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-right: none;
            color: #64748B;
        }

        .form-control {
            min-height: 44px;
            border: 1px solid var(--border-crisp);
            border-left: none;
            font-size: 13.5px;
            color: #0F233C;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: var(--indo-blue);
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(0, 82, 155, 0.12);
            border-radius: 0.375rem;
        }

        .password-toggle {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-left: none;
            color: #64748B;
            padding: 0 14px;
        }

        .password-toggle:hover {
            color: var(--indo-blue);
        }

        .form-check-input:checked {
            background-color: var(--indo-blue);
            border-color: var(--indo-blue);
        }

        .btn-indo-submit {
            min-height: 46px;
            background: var(--indo-blue);
            border: 1px solid var(--indo-blue);
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            transition: background-color 0.15s ease;
        }

        .btn-indo-submit:hover,
        .btn-indo-submit:focus {
            background: var(--indo-blue-hover);
            border-color: var(--indo-blue-hover);
            color: #FFFFFF;
        }

        .form-footer {
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px solid #EDF2F7;
            text-align: center;
            color: #94A3B8;
            font-size: 11.5px;
        }

        @media (max-width: 850px) {
            .login-shell {
                max-width: 460px;
                grid-template-columns: 1fr;
            }

            .brand-side {
                padding: 30px 26px;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .system-tag {
                margin-top: 22px;
            }

            .form-side {
                padding: 32px 26px;
            }
        }
    </style>
</head>

<body>
    <main class="login-shell">
        {{-- PANEL IDENTITAS PT INDOFOOD --}}
        <section class="brand-side">
            <div>
                <div class="brand-logo-box">
                    <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood">
                </div>

                <div>
                    <span class="system-tag">
                        <span class="system-tag-dot"></span>
                        Warehouse Management System
                    </span>
                </div>

                <h1 class="brand-heading">Warehouse Finished Goods</h1>
                <p class="brand-desc">
                    Sistem operasional terintegrasi untuk pemantauan kapasitas pallet, penempatan rak, serta pencatatan
                    mutasi produk jadi.
                </p>

                <div class="module-list">
                    <div class="module-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>Monitoring Kapasitas & Utilisasi Rak Real-Time</span>
                    </div>
                    <div class="module-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>Pemetaan Lokasi Penyimpanan Pallet & Karton</span>
                    </div>
                    <div class="module-item">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Kontrol Transaksi Masuk, Keluar & Pindah Rak</span>
                    </div>
                </div>
            </div>

            <div class="brand-bottom">
                <span>PT Indofood &bull; Finished Goods Division</span>
                <span>v1.0 Enterprise</span>
            </div>
        </section>

        {{-- PANEL AUTENTIKASI --}}
        <section class="form-side">
            <div class="form-header">
                <h2>Masuk ke Sistem</h2>
                <p>Gunakan akun operasional yang terdaftar pada sistem gudang.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control"
                            value="{{ old('email') }}" placeholder="nama@indofood.co.id" autocomplete="username"
                            required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" id="togglePassword"
                            aria-label="Tampilkan kata sandi">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                    <label class="form-check-label small text-secondary" for="remember">Pertahankan sesi masuk</label>
                </div>

                <button type="submit" class="btn btn-indo-submit w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Dashboard
                </button>
            </form>

            <div class="form-footer">
                Warehouse Finished Goods Monitoring System &copy; {{ date('Y') }}<br>
                Akses Terbatas PT Indofood
            </div>
        </section>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            this.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    </script>
</body>

</html>
