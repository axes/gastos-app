<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Editar Proyecto</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modificar Proyecto</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/proyectos/edit/<?= $proyecto['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">ID del Proyecto</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($proyecto['id']) ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Centro de Costo</label>
                            <select name="centro_costo_id" class="form-select form-select-lg" required>
                                <option value="">-- Seleccionar Centro --</option>
                                <?php foreach ($centrosActivos as $cc): ?>
                                    <option value="<?= htmlspecialchars($cc['id']) ?>" 
                                            <?= $proyecto['centro_costo_id'] == $cc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cc['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Cambia el centro de costo si es necesario</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Proyecto</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($proyecto['nombre']) ?>" required>
                            <small class="form-text text-muted">Edita el nombre del proyecto si es necesario</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($proyecto['fecha_inicio'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Término</label>
                                <input type="date" name="fecha_termino" class="form-control" value="<?= htmlspecialchars($proyecto['fecha_termino'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Presupuesto</label>
                            <input type="number" name="presupuesto" class="form-control form-control-lg" step="0.01" min="0" value="<?= htmlspecialchars($proyecto['presupuesto'] ?? '') ?>" placeholder="0.00">
                            <small class="form-text text-muted">Opcional: usado para alertas de gastos con adelanto</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="activoCheck" name="activo" 
                                       <?= $proyecto['activo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activoCheck">Proyecto Activo</label>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <span class="badge bg-<?= $proyecto['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $proyecto['activo'] ? 'Este proyecto está activo' : 'Este proyecto está inactivo' ?>
                                </span>
                            </small>
                        </div>

                        <div class="row g-2">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Actualizar Proyecto</button>
                            </div>
                            <div class="col-4">
                                <a href="/proyectos" class="btn btn-outline-secondary btn-lg w-100">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
