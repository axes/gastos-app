<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos App | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
    <link href="/css/theme.css" rel="stylesheet">
</head>
<body class="auth-page">

<main class="auth-shell">
    <div class="auth-card">
        <div class="row g-0">
            <div class="col-12 col-lg-6 auth-hero">
                <div class="auth-hero-inner">
                    <div class="auth-badge">Control de gastos</div>
                    <h1>Acceso seguro para tu equipo</h1>
                    <p>Centraliza gastos, aprobaciones y reportes con acceso por rol.</p>
                    <ul class="auth-features">
                        <li>Flujo de aprobacion por proyecto</li>
                        <li>Visibilidad por centro de costo</li>
                        <li>Reportes y estados en tiempo real</li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-6 auth-form">
                <div class="auth-form-inner">
                    <div class="auth-title">
                        <h2>Bienvenido</h2>
                        <p>Ingresa con tus credenciales para continuar.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-sm" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login" class="auth-form-fields">
                        <div class="mb-3">
                            <label class="form-label" for="rut">RUT</label>
                            <input type="text" id="rut" name="rut" class="form-control form-control-lg" placeholder="admin-001" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group auth-input-group">
                                <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Tu password" required>
                                <button type="button" class="btn btn-outline-secondary" data-password-toggle="password" aria-label="Mostrar password">
                                    Mostrar
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Recordar sesion en este equipo
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                    </form>

                    <p class="auth-helper">Si tienes problemas de acceso, contacta a Administracion.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
</body>
</html>
