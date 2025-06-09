<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stockmed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: rgb(24, 144, 255);
        }
        body {
            background: linear-gradient(135deg, var(--primary) 0%, #e6f7ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(24, 144, 255, 0.10);
            padding: 40px 32px;
            max-width: 370px;
            width: 100%;
        }
        .login-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(24, 144, 255, 0.15);
        }
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: #1677ff;
            border-color: #1677ff;
        }
        .logo {
            display: block;
            margin: 0 auto 18px auto;
            width: 60px;
        }
        .error-message {
            color: #d32f2f;
            background: #fff0f0;
            border: 1px solid #ffcdd2;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="https://placehold.co/60x60/1890ff/fff?text=S" alt="Stockmed Logo" class="logo">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="error-message">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/login">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Sua senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="text-center mt-3">
            <small>&copy; <?= date('Y') ?> Stockmed</small>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>