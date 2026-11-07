<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — POS App</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Reset Total */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            /* BACKGROUND BARU: Biru Soft ke Ungu Modern */
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card Putih */
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 20px;
            /* Shadow dibuat lebih lembut tapi tebal biar kontras di background terang */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Icon Toko */
        .brand-icon {
            font-size: 2.5rem;
            color: #4f46e5;
            margin-bottom: 15px;
            display: inline-block;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 30px;
        }

        /* Form Group */
        .input-group-custom {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        .input-group-custom label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        /* Ikon di dalam input */
        .input-icon {
            position: absolute;
            left: 15px;
            top: 38px; /* Disesuaikan agar pas di tengah input */
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
        }

        /* Input Field */
        .form-input {
            width: 100%;
            padding: 12px 15px 12px 45px; /* Padding kiri untuk ikon */
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
        }

        /* Pesan Error */
        .error-msg {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
            text-align: left;
        }

        /* Tombol Login */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #4338ca;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Header -->
        <div class="brand-icon">
            <i class="bi bi-shop"></i>
        </div>
        <h2 class="login-title">POS App</h2>
        <p class="login-subtitle">Silakan login untuk melanjutkan</p>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Input Email -->
            <div class="input-group-custom">
                <label for="email">Email</label>
                <i class="bi bi-envelope input-icon"></i>
                <input type="email" 
                       name="email" 
                       id="email" 
                       class="form-input @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" 
                       placeholder="Masukkan email" 
                       required autofocus>
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Input Password -->
            <div class="input-group-custom">
                <label for="password">Password</label>
                <i class="bi bi-lock input-icon"></i>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="form-input @error('password') is-invalid @enderror" 
                       placeholder="Masukkan password" 
                       required>
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
    </div>

    <!-- Bootstrap JS (Opsional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>