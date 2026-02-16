<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Centros de Costo</h1>
        <a href="/centros_costo/create" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Nuevo Centro
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($centros)): ?>
                <div class="p-4">
                    <div class="alert alert-info mb-0">
                        No hay centros de costo registrados. <a href="/centros_costo/create" class="alert-link">Crear uno</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover data-table mb-0">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="50%">Nombre</th>
                                <th width="20%">Estado</th>
                                <th width="25%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($centros as $c): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($c['id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $c['activo'] ? 'success' : 'secondary' ?>">
                                        <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/centros_costo/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-<?= $c['activo'] ? 'danger' : 'success' ?>" 
                                                onclick="toggleState('/centros_costo/deactivate/<?= $c['id'] ?>', '<?= $c['activo'] ? 'desactivar' : 'activar' ?>')" 
                                                data-bs-toggle="tooltip" 
                                                title="<?= $c['activo'] ? 'Desactivar' : 'Activar' ?>">
                                            <i class="fa-solid fa-<?= $c['activo'] ? 'ban' : 'check' ?>"></i>
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
