<?php

use App\Views\Layout;
use App\Services\AuthorizationService;

Layout::header();
?>

<div class="mb-5">
    <h1 class="mb-4">
        <i class="fas fa-chart-line"></i> Dashboard
    </h1>

    <?php if (!empty($user)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>¡Bienvenido, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?>!</strong>
            Tienes acceso como <strong><?= strtoupper(AuthorizationService::role() ?? 'desconocido') ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <?php if (AuthorizationService::isAdmin()): ?>
            <!-- ADMIN DASHBOARD -->
            <div class="row">
                <!-- QuickLinks Cards -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon primary">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5 class="card-title">Centros de Costo</h5>
                            <p class="text-muted-custom">Gestiona todos los centros</p>
                            <div class="d-grid gap-2">
                                <a href="/centros_costo" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="/centros_costo/create" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon success">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h5 class="card-title">Proyectos</h5>
                            <p class="text-muted-custom">Administra proyectos</p>
                            <div class="d-grid gap-2">
                                <a href="/proyectos" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="/proyectos/create" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon warning">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h5 class="card-title">Gastos</h5>
                            <p class="text-muted-custom">Gestiona todos los gastos</p>
                            <div class="d-grid gap-2">
                                <a href="/gastos" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="/gastos/create" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon info">
                                <i class="fas fa-cog"></i>
                            </div>
                            <h5 class="card-title">Administración</h5>
                            <p class="text-muted-custom">Configurar usuarios</p>
                            <div class="d-grid gap-2">
                                <a href="/admin/managers" class="btn btn-primary btn-sm">
                                    <i class="fas fa-users-cog"></i> Managers
                                </a>
                                <a href="/admin/project-members" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-tie"></i> Miembros
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Row -->
            <div class="row mt-5">
                <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Estadísticas Generales</h3>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat">
                        <h3><?= $stats['total_centros'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-building"></i> Centros de Costo
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #198754, #157347);">
                        <h3><?= $stats['total_proyectos'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-project-diagram"></i> Proyectos
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #ffc107, #ffb300);">
                        <h3><?= $stats['total_gastos'] ?? 0 ?></h3>
                        <p style="color: #333;">
                            <i class="fas fa-receipt"></i> Total Gastos
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                        <h3><?= $stats['gastos_pendientes'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-clock"></i> Pendientes
                        </p>
                    </div>
                </div>
            </div>

        <?php elseif (AuthorizationService::isManager()): ?>
            <!-- MANAGER DASHBOARD -->
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon primary">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h5 class="card-title">Proyectos a Cargo</h5>
                            <p class="text-muted-custom">Gestiona tus proyectos</p>
                            <div class="d-grid gap-2">
                                <a href="/proyectos" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon warning">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h5 class="card-title">Gastos y Aprobación</h5>
                            <p class="text-muted-custom">Revisa & aprueba</p>
                            <div class="d-grid gap-2">
                                <a href="/gastos" class="btn btn-primary btn-sm">
                                    <i class="fas fa-list"></i> Ver Gastos
                                </a>
                                <a href="/approve/gastos" class="btn btn-warning btn-sm">
                                    <i class="fas fa-check"></i> Aprobar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon info">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Miembros en Proyecto</h5>
                            <p class="text-muted-custom">Asigna usuarios</p>
                            <div class="d-grid gap-2">
                                <a href="/manager/project-members" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Administrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Row -->
            <div class="row mt-5">
                <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Mis Estadísticas</h3>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat">
                        <h3><?= $stats['gastos_pendientes'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-clock"></i> Gastos por Aprobar
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #198754, #157347);">
                        <h3><?= $stats['total_gastos'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-receipt"></i> Total de Gastos
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #0dcaf0, #0dbbda);">
                        <h3><?= $stats['total_proyectos'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-project-diagram"></i> Proyectos a Cargo
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #6f42c1, #5a32a3);">
                        <h3><?= $stats['total_centros'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-building"></i> Centros Asignados
                        </p>
                    </div>
                </div>
            </div>

        <?php elseif (AuthorizationService::isUser()): ?>
            <!-- USER DASHBOARD -->
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon primary">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h5 class="card-title">Mis Gastos</h5>
                            <p class="text-muted-custom">Registra y visualiza tus gastos</p>
                            <div class="d-grid gap-2">
                                <a href="/gastos" class="btn btn-primary btn-sm">
                                    <i class="fas fa-list"></i> Ver Mis Gastos
                                </a>
                                <a href="/gastos/create" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo Gasto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 card-shadow">
                        <div class="card-body text-center">
                            <div class="card-icon info">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h5 class="card-title">Mi Información</h5>
                            <p class="text-muted-custom">Consulta tus datos</p>
                            <div class="d-grid gap-2">
                                <a href="/profile" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user"></i> Mi Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Row -->
            <div class="row mt-5">
                <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Mis Estadísticas</h3>
                
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="dashboard-stat">
                        <h3><?= $stats['my_gastos'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-receipt"></i> Gastos Registrados
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #ffc107, #ffb300);">
                        <h3><?= $stats['my_gastos_pendientes'] ?? 0 ?></h3>
                        <p style="color: #333;">
                            <i class="fas fa-clock"></i> Pendientes de Aprobación
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="dashboard-stat" style="background: linear-gradient(135deg, #198754, #157347);">
                        <h3><?= $stats['my_gastos_aprobados'] ?? 0 ?></h3>
                        <p>
                            <i class="fas fa-check"></i> Aprobados
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning">
            <strong>Sesión inválida</strong>
            <p>Por favor, inicia sesión nuevamente.</p>
            <a href="/login" class="btn btn-primary">Ir al Login</a>
        </div>
    <?php endif; ?>
</div>

<?php Layout::footer(); ?>

