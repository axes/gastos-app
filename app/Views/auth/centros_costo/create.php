<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Crear Centro de Costo</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Nuevo Centro</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/centros_costo/create">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Centro</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" 
                                   placeholder="Ej: Centro Administrativo" required autofocus>
                            <small class="form-text text-muted">Ingresa un nombre descriptivo para el centro</small>
                        </div>

                        <div class="row g-2">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Guardar Centro</button>
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
