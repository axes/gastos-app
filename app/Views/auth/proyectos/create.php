<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Crear Proyecto</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Nuevo Proyecto</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/proyectos/create">
                        <div class="mb-3">
                            <label class="form-label">Centro de Costo</label>
                            <select name="centro_costo_id" class="form-select form-select-lg" required autofocus>
                                <option value="">-- Seleccionar Centro --</option>
                                <?php foreach ($centrosActivos as $cc): ?>
                                    <option value="<?= htmlspecialchars($cc['id']) ?>">
                                        <?= htmlspecialchars($cc['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Elige el centro de costo donde se aplicará este proyecto</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Proyecto</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" 
                                   placeholder="Ej: Proyecto de Infraestructura" required>
                            <small class="form-text text-muted">Ingresa un nombre descriptivo para el proyecto</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Término</label>
                                <input type="date" name="fecha_termino" class="form-control">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Presupuesto</label>
                            <input type="number" name="presupuesto" class="form-control form-control-lg" step="0.01" min="0" placeholder="0.00">
                            <small class="form-text text-muted">Opcional: usado para alertas de gastos con adelanto</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Guardar Proyecto</button>
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

<?php Layout::footer(); ?>
