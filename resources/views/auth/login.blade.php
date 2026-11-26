<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | WMS Indofood FG</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/indofood-logo.svg') }}">
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
            background-color: #E6EDF5;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(0, 82, 155, 0.09) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(215, 25, 32, 0.07) 0%, transparent 45%),
                radial-gradient(#CBD5E1 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 22px 22px;
            color: #0F233C;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        @keyframes cardFadeUp {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.99);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes itemSlideRight {
            from {
                opacity: 0;
                transform: translateX(-14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes formFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseStatus {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.45;
                transform: scale(1.3);
            }
        }

        @keyframes warehouseScanline {
            0% {
                top: -15%;
                opacity: 0;
            }

            15% {
                opacity: 0.7;
            }

            85% {
                opacity: 0.7;
            }

            100% {
                top: 115%;
                opacity: 0;
            }
        }

        .login-shell {
            width: 100%;
            max-width: 900px;
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-radius: 16px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.02fr 0.98fr;
            box-shadow: 0 24px 55px rgba(14, 35, 64, 0.15);
            animation: cardFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: box-shadow 0.3s ease;
        }

        .login-shell:hover {
            box-shadow: 0 28px 65px rgba(14, 35, 64, 0.19);
        }

        .login-shell::before {
            content: "";
            grid-column: 1 / -1;
            height: 5px;
            background: linear-gradient(90deg, var(--indo-red) 0%, var(--indo-red) 32%, var(--indo-blue) 32%, #1B75C9 100%);
        }

        /* Panel Kiri: Warehouse Blueprint Grid & Scanner Effect */
        .brand-side {
            position: relative;
            background-color: var(--indo-navy);
            background-image:
                radial-gradient(circle at 85% 15%, rgba(0, 119, 214, 0.34) 0%, transparent 55%),
                radial-gradient(circle at 10% 90%, rgba(215, 25, 32, 0.2) 0%, transparent 50%),
                linear-gradient(rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.045) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 26px 26px, 26px 26px;
            color: #FFFFFF;
            padding: 38px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* Garis Laser Scanner Gudang Halus */
        .brand-side::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.45) 50%, transparent 100%);
            box-shadow: 0 0 14px rgba(56, 189, 248, 0.35);
            pointer-events: none;
            animation: warehouseScanline 6.5s linear infinite;
        }

        .brand-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            opacity: 0;
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.08s forwards;
        }

        .brand-logo-box {
            display: inline-flex;
            align-items: center;
            background: #FFFFFF;
            padding: 9px 14px;
            border-radius: 9px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22);
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease;
        }

        .brand-logo-box:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.3);
        }

        .brand-logo-box img {
            width: 142px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .system-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 11px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(52, 211, 153, 0.38);
            color: #6EE7B7;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .system-status-pill:hover {
            background: rgba(16, 185, 129, 0.22);
            transform: scale(1.03);
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34D399;
            box-shadow: 0 0 8px #34D399;
            animation: pulseStatus 2s infinite ease-in-out;
        }

        .brand-heading {
            margin: 26px 0 8px;
            font-size: 25px;
            font-weight: 800;
            line-height: 1.24;
            letter-spacing: -0.02em;
            opacity: 0;
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.16s forwards;
        }

        .brand-desc {
            color: #B4CCE6;
            font-size: 13px;
            line-height: 1.6;
            margin: 0 0 22px;
            opacity: 0;
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.22s forwards;
        }

        .wms-feature-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .wms-feature-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.09);
            backdrop-filter: blur(4px);
            cursor: default;
            opacity: 0;
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1),
                background-color 0.22s ease,
                border-color 0.22s ease;
        }

        .wms-feature-item:nth-child(1) {
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.28s forwards;
        }

        .wms-feature-item:nth-child(2) {
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.36s forwards;
        }

        .wms-feature-item:nth-child(3) {
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.44s forwards;
        }

        .wms-feature-item:hover {
            transform: translateX(6px);
            background: rgba(255, 255, 255, 0.11);
            border-color: rgba(125, 211, 252, 0.42);
        }

        .wms-feature-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: rgba(56, 189, 248, 0.16);
            color: #7DD3FC;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: transform 0.22s ease, background-color 0.22s ease, color 0.22s ease;
        }

        .wms-feature-item:hover .wms-feature-icon {
            transform: scale(1.1);
            background: rgba(56, 189, 248, 0.28);
            color: #FFFFFF;
        }

        .wms-feature-text {
            font-size: 12px;
            font-weight: 600;
            color: #E2E8F0;
            line-height: 1.35;
        }

        .wms-feature-sub {
            display: block;
            font-size: 10.5px;
            font-weight: 500;
            color: #94B2D1;
        }

        .brand-bottom {
            margin-top: 26px;
            padding-top: 13px;
            border-top: 1px solid rgba(255, 255, 255, 0.10);
            font-size: 11px;
            color: #8CA9C9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            opacity: 0;
            animation: itemSlideRight 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards;
        }

        /* Panel Kanan: Form Login dengan Staggered Fade */
        .form-side {
            padding: 40px 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
        }

        .form-header {
            margin-bottom: 24px;
            opacity: 0;
            animation: formFadeIn 0.45s ease-out 0.15s forwards;
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

        .form-body-animate {
            opacity: 0;
            animation: formFadeIn 0.45s ease-out 0.25s forwards;
        }

        .form-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #1E3A5F;
            margin-bottom: 6px;
        }

        .input-group {
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            border-radius: 0.375rem;
        }

        .input-group-text {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-right: none;
            color: #64748B;
            transition: border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;
        }

        .form-control {
            min-height: 44px;
            border: 1px solid var(--border-crisp);
            border-left: none;
            font-size: 13.5px;
            color: #0F233C;
            box-shadow: none !important;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--indo-blue);
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3.5px rgba(0, 82, 155, 0.13);
            transform: translateY(-1px);
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--indo-blue);
            background: #F0F7FF;
            color: var(--indo-blue);
        }

        .password-toggle {
            background: #F8FAFC;
            border: 1px solid var(--border-crisp);
            border-left: none;
            color: #64748B;
            padding: 0 14px;
            transition: color 0.15s ease, border-color 0.2s ease, background-color 0.2s ease;
        }

        .input-group:focus-within .password-toggle {
            border-color: var(--indo-blue);
            background: #F0F7FF;
        }

        .password-toggle:hover {
            color: var(--indo-blue);
        }

        .form-check-input {
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .form-check-input:hover {
            transform: scale(1.06);
        }

        .form-check-input:checked {
            background-color: var(--indo-blue);
            border-color: var(--indo-blue);
        }

        /* Tombol Submit dengan Efek Kilau Shimmer */
        .btn-indo-submit {
            position: relative;
            overflow: hidden;
            min-height: 44px;
            background: var(--indo-blue);
            border: 1px solid var(--indo-blue);
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.16s cubic-bezier(0.16, 1, 0.3, 1),
                background-color 0.18s ease,
                box-shadow 0.18s ease;
        }

        .btn-indo-submit::after {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.24), transparent);
            transform: skewX(-20deg);
            transition: left 0.55s ease;
        }

        .btn-indo-submit:hover::after {
            left: 150%;
        }

        .btn-indo-submit:hover,
        .btn-indo-submit:focus {
            background: var(--indo-blue-hover);
            border-color: var(--indo-blue-hover);
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 82, 155, 0.28);
        }

        .btn-indo-submit .bi-arrow-right {
            display: inline-block;
            transition: transform 0.18s ease;
        }

        .btn-indo-submit:hover .bi-arrow-right {
            transform: translateX(4px);
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
            opacity: 0;
            animation: formFadeIn 0.45s ease-out 0.35s forwards;
        }

        @media (max-width: 820px) {
            .login-shell {
                max-width: 450px;
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
                <div class="brand-top-row">
                    <div class="brand-logo-box">
                        <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood">
                    </div>
                    <span class="system-status-pill">
                        <span class="status-dot"></span>
                        WMS ONLINE
                    </span>
                </div>

                <h1 class="brand-heading">Warehouse Finished Goods</h1>
                <p class="brand-desc">
                    Sistem Manajemen Penyimpanan, Okupansi Rak, &amp; Audit Inventori Produk Jadi.
                </p>

                <div class="wms-feature-list">
                    <div class="wms-feature-item">
                        <span class="wms-feature-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                        <div class="wms-feature-text">
                            Pemetaan Rak 2D Real-Time
                            <span class="wms-feature-sub">Visualisasi kapasitas &amp; okupansi slot per level</span>
                        </div>
                    </div>
                    <div class="wms-feature-item">
                        <span class="wms-feature-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <div class="wms-feature-text">
                            Kontrol Pergerakan IN / OUT / MOVE
                            <span class="wms-feature-sub">Validasi kapasitas pallet &amp; isolasi multi-gudang</span>
                        </div>
                    </div>
                    <div class="wms-feature-item">
                        <span class="wms-feature-icon"><i class="bi bi-camera-fill"></i></span>
                        <div class="wms-feature-text">
                            Pelacakan Bad Stock Terverifikasi
                            <span class="wms-feature-sub">Dokumentasi foto bukti &amp; kalkulasi stok bersih</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="brand-bottom">
                <span>PT Indofood &bull; Finished Goods WMS</span>
                <span>RBAC v1.0</span>
            </div>
        </section>

        <section class="form-side">
            <div class="form-header">
                <h2>Masuk ke Sistem</h2>
                <p>Gunakan akun operasional gudang Anda untuk melanjutkan.</p>
            </div>

            <div class="form-body-animate">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">Email Operasional</label>
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
                        <label class="form-check-label small text-secondary" for="remember">Ingat sesi masuk
                            saya</label>
                    </div>

                    <button type="submit" class="btn btn-indo-submit w-100" id="submitBtn">
                        Masuk ke Dashboard <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>

            <div class="form-footer">
                &copy; {{ date('Y') }} PT Indofood &mdash; Internal Warehouse System
            </div>
        </section>
    </main>

    {{-- Tirai Transisi Masuk ke Dashboard (Razor-Sharp Tanpa Blur) --}}
    <div id="loginExitCurtain"
        style="
        position: fixed;
        inset: 0;
        background: linear-gradient(135deg, #0E2340 0%, #00529B 100%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        opacity: 0;
        pointer-events: none;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transition: opacity 0.32s ease-out;
    ">
        <div
            style="
            background: #FFFFFF;
            padding: 11px 20px;
            border-radius: 11px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
            margin-bottom: 18px;
        ">
            <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood"
                style="width: 136px; height: auto; display: block;">
        </div>
        <div class="d-flex align-items-center gap-2"
            style="font-size: 13.5px; font-weight: 700; letter-spacing: 0.02em; color: #FFFFFF;">
            <span class="spinner-border spinner-border-sm text-info" role="status"></span>
            <span>Membuka Command Center Gudang...</span>
        </div>
    </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const exitCurtain = document.getElementById('loginExitCurtain');
        const curtainInner = document.getElementById('curtainInner');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            this.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memverifikasi...';

            sessionStorage.setItem('wms_just_logged_in', '1');
            exitCurtain.style.pointerEvents = 'auto';
            exitCurtain.style.opacity = '1';

            setTimeout(() => {
                loginForm.submit();
            }, 340);
        });
    </script>
</body>

</html>
