<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Mi Perfil</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos de Transferencia</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="/profile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Banco</label>
                                <input type="text" name="banco" class="form-control form-control-lg" value="<?= htmlspecialchars($user['banco'] ?? '') ?>" placeholder="Ej: Banco Estado">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Cuenta</label>
                                <input type="text" name="tipo_cuenta" class="form-control form-control-lg" value="<?= htmlspecialchars($user['tipo_cuenta'] ?? '') ?>" placeholder="Ej: Cuenta Vista">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Numero de Cuenta</label>
                                <input type="text" name="numero_cuenta" class="form-control form-control-lg" value="<?= htmlspecialchars($user['numero_cuenta'] ?? '') ?>" placeholder="Ej: 12345678">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Titular</label>
                                <input type="text" name="titular_cuenta" class="form-control form-control-lg" value="<?= htmlspecialchars($user['titular_cuenta'] ?? '') ?>" placeholder="Nombre del titular">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">RUT Titular</label>
                                <input type="text" name="rut_titular" class="form-control form-control-lg" value="<?= htmlspecialchars($user['rut_titular'] ?? '') ?>" placeholder="Ej: 12.345.678-9">
                            </div>
                        </div>

                        <div class="row g-2 mt-4">
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Guardar</button>
                            </div>
                            <div class="col-4">
                                <a href="/dashboard" class="btn btn-outline-secondary btn-lg w-100">Volver</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
