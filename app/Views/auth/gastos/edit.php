<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Editar Gasto</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modificar Gasto</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/gastos/edit/<?= $gasto['id'] ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Proyecto</label>
                                <select name="proyecto_id" id="proyectoSelect" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($proyectos as $p): ?>
                                        <option value="<?= htmlspecialchars($p['id']) ?>" data-centro="<?= htmlspecialchars($p['centro_costo_nombre']) ?>" <?= $gasto['proyecto_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($p['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Centro de Costo</label>
                                <input type="text" id="centroCostoDisplay" class="form-control" value="" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($gasto['fecha']) ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Monto</label>
                                <input type="number" name="monto" class="form-control" step="0.01" value="<?= htmlspecialchars($gasto['monto']) ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo de Gasto</label>
                                <select name="tipo" class="form-select" <?= $gasto['estado'] !== 'pendiente' ? 'disabled' : '' ?> required>
                                    <option value="">Seleccione...</option>
                                    <option value="adelanto" <?= $gasto['tipo'] === 'adelanto' ? 'selected' : '' ?>>Adelanto</option>
                                    <option value="reembolso" <?= $gasto['tipo'] === 'reembolso' ? 'selected' : '' ?>>Reembolso</option>
                                    <option value="registro" <?= $gasto['tipo'] === 'registro' ? 'selected' : '' ?>>Registro</option>
                                </select>
                                <?php if ($gasto['estado'] !== 'pendiente'): ?>
                                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($gasto['tipo']) ?>">
                                <?php endif; ?>
                                <small class="form-text text-muted">Solo editable mientras esta pendiente</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="categoria_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= htmlspecialchars($c['id']) ?>" <?= $gasto['categoria_id'] == $c['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Medio de Pago</label>
                                <select name="medio_pago_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($mediosPago as $m): ?>
                                        <option value="<?= htmlspecialchars($m['id']) ?>" <?= $gasto['medio_pago_id'] == $m['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($gasto['descripcion'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Documento</label>
                                <input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($gasto['documento'] ?? '') ?>" placeholder="Ruta o nombre del archivo">
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activoCheck" <?= $gasto['activo'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activoCheck">Gasto Activo</label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    <span class="badge bg-<?= $gasto['activo'] ? 'success' : 'secondary' ?>">
                                        <?= $gasto['activo'] ? 'Este gasto está activo' : 'Este gasto está inactivo' ?>
                                    </span>
                                </small>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    Estado actual: <strong><?= htmlspecialchars($gasto['estado']) ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Actualizar Gasto</button>
                            </div>
                            <div class="col-4">
                                <a href="/gastos" class="btn btn-outline-secondary btn-lg w-100">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const proyectoSelect = document.getElementById('proyectoSelect');
    const centroCostoDisplay = document.getElementById('centroCostoDisplay');

    function updateCentroCosto() {
        const selected = proyectoSelect.options[proyectoSelect.selectedIndex];
        centroCostoDisplay.value = selected ? (selected.dataset.centro || '') : '';
    }

    proyectoSelect.addEventListener('change', updateCentroCosto);
    updateCentroCosto();
</script>

<?php Layout::footer(); ?>
