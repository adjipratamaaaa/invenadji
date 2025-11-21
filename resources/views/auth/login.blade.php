<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JIPARTS Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .brand-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .brand-logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .brand-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
        }
        .brand-tagline {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 5px 0 0 0;
        }
        .login-form {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head><div class="login-footer"></div>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="login-card">
                        <!-- Brand Header -->
                        <div class="brand-header">
                            <div class="brand-logo">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <h1 class="brand-name">JIPARTS</h1>
                            <p class="brand-tagline">Computer Parts & Inventory System</p>
                        </div>

                        <!-- Login Form -->
                        <div class="login-form">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Email Input -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="Enter your email" 
                                               required 
                                               autofocus>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Password Input -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Enter your password" 
                                               required>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-login w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to System
                                </button>

                                <!-- Forgot Password -->
                                @if (Route::has('password.request'))
                                    <div class="text-center">
                                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                                            Forgot Your Password?
                                        </a>
                                    </div>
                                @endif
                                <div class="text-center mt-3">
    <a href="{{ route('register') }}" class="text-decoration-none">
        Belum punya akun? Daftar di sini
    </a>
</div>

                            </form>
                        </div>
<!-- Login Guide -->
<div class="text-center mt-3" style="font-size: 0.85rem; color:#555;">
    <strong>Panduan Login:</strong><br>
    Admin: <code>admin@inventory.com</code> / <code>password</code><br>
    Kasir: <code>kasir@inventory.com</code> / <code>password</code><br>
    Gudang: <code>gudang@inventory.com</code> / <code>password</code>
</div>

                        <!-- Footer -->
                        <div class="login-footer">
                            <p>&copy; 2024 JIPARTS. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>