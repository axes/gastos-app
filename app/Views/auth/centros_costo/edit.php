<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Editar Centro de Costo</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modificar Centro</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/centros_costo/edit/<?= $centro['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">ID del Centro</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($centro['id']) ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del Centro</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($centro['nombre']) ?>" required autofocus>
                            <small class="form-text text-muted">Edita el nombre del centro si es necesario</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="activoCheck" name="activo" 
                                       <?= $centro['activo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="activoCheck">Centro Activo</label>
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                <span class="badge bg-<?= $centro['activo'] ? 'success' : 'secondary' ?>">
                                    <?= $centro['activo'] ? 'Este centro está activo' : 'Este centro está inactivo' ?>
                                </span>
                            </small>
                        </div>

                        <div class="row g-2">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Actualizar Centro</button>
                            </div>
                            <div class="col-4">
                                <a href="/centros_costo" class="btn btn-outline-secondary btn-lg w-100">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
