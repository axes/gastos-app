<?php

namespace App\Views;

use App\Services\AuthService;
use App\Services\AuthorizationService;

class Layout
{
    public static function header()
    {
        $user = AuthService::user();
        $role = AuthorizationService::role();
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gastos App</title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
            <!-- DataTables CSS -->
            <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
            <!-- Select2 CSS -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
            <!-- Select2 Bootstrap 5 CSS -->
            <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
            <!-- Font Awesome para iconos -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
            <!-- Estilos personalizados -->
            <link href="/css/styles.css" rel="stylesheet">
            <link href="/css/theme.css" rel="stylesheet">
            <script>
                // Aplicar tema antes de renderizar para evitar parpadeo
                (function() {
                    var theme = document.cookie.split('; ').find(row => row.startsWith('theme='));
                    theme = theme ? theme.split('=')[1] : 'dark';
                    document.documentElement.setAttribute('data-theme', theme);
                })();
            </script>
        </head>

        <body>

            <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/dashboard">GastosApp</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <?php if ($role === 'admin'): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="centrosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Centros de Costo
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="centrosDropdown">
                                        <li><a class="dropdown-item" href="/centros_costo">Listar Centros</a></li>
                                        <li><a class="dropdown-item" href="/admin/managers">Asignar Responsables</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="proyectosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Proyectos
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="proyectosDropdown">
                                        <li><a class="dropdown-item" href="/proyectos">Listar Proyectos</a></li>
                                        <li><a class="dropdown-item" href="/admin/project-members">Miembros Proyectos</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/gastos">Gastos</a>
                                </li>
                            <?php elseif ($role === 'manager'): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="proyectosManagerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Proyectos
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="proyectosManagerDropdown">
                                        <li><a class="dropdown-item" href="/proyectos">Listar Proyectos</a></li>
                                        <li><a class="dropdown-item" href="/manager/project-members">Miembros Proyectos</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/gastos">Gastos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/approve/gastos">Aprobar Gastos</a>
                                </li>
                            <?php elseif ($role === 'user'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/gastos">Mis Gastos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/gastos/create">Nuevo Gasto</a>
                                </li>
                            <?php elseif ($role === 'finance'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/finance/reembolsos">Reembolsos</a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item me-2 d-flex align-items-center">
                                <button type="button" id="theme-toggle" class="btn btn-sm btn-outline-light" aria-label="Cambiar tema">
                                    <i class="fa-solid fa-moon"></i>
                                </button>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?> (<?= htmlspecialchars($role ?? 'desconocido') ?>)
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/profile">Perfil</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="/logout">Cerrar Sesión</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container mt-4">
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            <?php
        }

        public static function footer()
        {
            ?>
            </div>

            <!-- Bootstrap JS Bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
            <!-- jQuery (requerido por DataTables y Select2) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <!-- DataTables JS -->
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
            <!-- Select2 JS - Multi-select elegante -->
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- App.js - Scripts personalizados -->
            <script src="/js/app.js"></script>
        </body>

        </html>
<?php
        }
    }
