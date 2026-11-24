<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Warehouse FG PT Indofood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --indo-navy: #0E2340;
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

        @keyframes cardFadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-shell {
            width: 100%;
            max-width: 840px;
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-radius: 14px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 0.92fr 1.08fr;
            box-shadow: 0 18px 45px rgba(14, 35, 64, 0.12);
            animation: cardFadeUp 0.42s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .login-shell::before {
            content: "";
            grid-column: 1 / -1;
            height: 5px;
            background: linear-gradient(90deg, var(--indo-red) 0%, var(--indo-red) 32%, var(--indo-blue) 32%, #1B75C9 100%);
        }

        .brand-side {
            background: var(--indo-navy);
            color: #FFFFFF;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-logo-box {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            padding: 10px 15px;
            border-radius: 9px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            transition: transform 0.2s ease;
        }

        .brand-logo-box:hover {
            transform: translateY(-2px);
        }

        .brand-logo-box img {
            width: 155px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .brand-heading {
            margin: 28px 0 10px;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: -0.02em;
        }

        .brand-desc {
            color: #A8C2DF;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        .brand-bottom {
            margin-top: 32px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.10);
            font-size: 11.5px;
            color: #8CA9C9;
        }

        .form-side {
            padding: 40px 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h2 {
            margin: 0 0 4px;
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

        .input-group {
            transition: box-shadow 0.18s ease;
            border-radius: 0.375rem;
        }

        .input-group-text {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-right: none;
            color: #64748B;
            transition: border-color 0.18s ease, color 0.18s ease;
        }

        .form-control {
            min-height: 44px;
            border: 1px solid var(--border-crisp);
            border-left: none;
            font-size: 13.5px;
            color: #0F233C;
            box-shadow: none !important;
            transition: border-color 0.18s ease;
        }

        .form-control:focus {
            border-color: var(--indo-blue);
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(0, 82, 155, 0.12);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--indo-blue);
            color: var(--indo-blue);
        }

        .password-toggle {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-left: none;
            color: #64748B;
            padding: 0 14px;
            transition: color 0.15s ease, border-color 0.18s ease;
        }

        .input-group:focus-within .password-toggle {
            border-color: var(--indo-blue);
        }

        .password-toggle:hover {
            color: var(--indo-blue);
        }

        .form-check-input {
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .form-check-input:checked {
            background-color: var(--indo-blue);
            border-color: var(--indo-blue);
        }

        .btn-indo-submit {
            min-height: 44px;
            background: var(--indo-blue);
            border: 1px solid var(--indo-blue);
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.14s ease, background-color 0.16s ease, box-shadow 0.16s ease;
        }

        .btn-indo-submit:hover,
        .btn-indo-submit:focus {
            background: var(--indo-blue-hover);
            border-color: var(--indo-blue-hover);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 82, 155, 0.24);
        }

        .btn-indo-submit:active {
            transform: scale(0.98);
            box-shadow: none;
        }

        .btn-indo-submit:disabled {
            opacity: 0.85;
            transform: none;
            cursor: not-allowed;
        }

        .form-footer {
            margin-top: 22px;
            text-align: center;
            color: #94A3B8;
            font-size: 11.5px;
        }

        @media (max-width: 768px) {
            .login-shell {
                max-width: 440px;
                grid-template-columns: 1fr;
            }

            .brand-side {
                padding: 28px 24px;
            }

            .form-side {
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>
    <main class="login-shell">
        <section class="brand-side">
            <div>
                <div class="brand-logo-box">
                    <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood">
                </div>

                <h1 class="brand-heading">Warehouse Finished Goods</h1>
                <p class="brand-desc">
                    Sistem Manajemen Penyimpanan & Inventori Produk Jadi.
                </p>
            </div>

            <div class="brand-bottom">
                PT Indofood &bull; Internal Warehouse System
            </div>
        </section>

        <section class="form-side">
            <div class="form-header">
                <h2>Masuk</h2>
                <p>Silakan masuk menggunakan akun gudang Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
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
                    <label class="form-check-label small text-secondary" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-indo-submit w-100" id="submitBtn">
                    Masuk
                </button>
            </form>

            <div class="form-footer">
                &copy; {{ date('Y') }} PT Indofood &mdash; Warehouse Finished Goods
            </div>
        </section>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            this.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });

        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });
    </script>
</body>

</html>
