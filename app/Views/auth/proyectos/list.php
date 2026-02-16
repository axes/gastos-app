<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Proyectos</h1>
        <a href="/proyectos/create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo Proyecto
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($proyectos)): ?>
                <div class="p-4">
                    <div class="alert alert-info mb-0">
                        No hay proyectos registrados. <a href="/proyectos/create" class="alert-link">Crear uno</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover data-table mb-0">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="25%">Centro de Costo</th>
                                <th width="35%">Nombre</th>
                                <th width="15%">Estado</th>
                                <th width="20%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($proyectos as $p): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($p['id']) ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($p['centro_costo_nombre']) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $p['activo'] ? 'success' : 'secondary' ?>">
                                        <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/proyectos/edit/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-<?= $p['activo'] ? 'danger' : 'success' ?>" 
                                                onclick="toggleState('/proyectos/deactivate/<?= $p['id'] ?>', '<?= $p['activo'] ? 'desactivar' : 'activar' ?>')" 
                                                data-bs-toggle="tooltip" 
                                                title="<?= $p['activo'] ? 'Desactivar' : 'Activar' ?>">
                                            <i class="fa-solid fa-<?= $p['activo'] ? 'ban' : 'check' ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
