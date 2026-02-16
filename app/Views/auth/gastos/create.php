<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Crear Gasto</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Nuevo Gasto</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/gastos/create">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Proyecto</label>
                                <select name="proyecto_id" id="proyectoSelect" class="form-select form-select-lg" required autofocus>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($proyectos as $p): ?>
                                        <option value="<?= htmlspecialchars($p['id']) ?>" data-centro="<?= htmlspecialchars($p['centro_costo_nombre']) ?>">
                                            <?= htmlspecialchars($p['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Selecciona un proyecto activo para asignar el gasto</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Centro de Costo</label>
                                <input type="text" id="centroCostoDisplay" class="form-control form-control-lg" value="" placeholder="Se completa automaticamente" readonly>
                                <small class="form-text text-muted">Se muestra segun el proyecto seleccionado</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Monto</label>
                                <input type="number" name="monto" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo de Gasto</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    <option value="adelanto">Adelanto</option>
                                    <option value="reembolso">Reembolso</option>
                                    <option value="registro">Registro</option>
                                </select>
                                <small class="form-text text-muted">Reembolso: se gestiona por finanzas luego de aprobacion</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="categoria_id" class="form-select form-select-lg" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= htmlspecialchars($c['id']) ?>">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Medio de Pago</label>
                                <select name="medio_pago_id" class="form-select form-select-lg" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($mediosPago as $m): ?>
                                        <option value="<?= htmlspecialchars($m['id']) ?>">
                                            <?= htmlspecialchars($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Documento</label>
                                <input type="text" name="documento" class="form-control" placeholder="Ruta o nombre del archivo">
                                <small class="form-text text-muted">Opcional: referencia interna o ruta del respaldo</small>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    Al crear el gasto quedara en estado <strong>Pendiente</strong> hasta la aprobacion del manager.
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Guardar Gasto</button>
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
