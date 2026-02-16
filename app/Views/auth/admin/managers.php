<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Asignar Responsables a Centros de Costo</h1>
            <p class="text-muted mt-2">Cada Centro de Costo puede tener un único responsable (rol Manager)</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Centros de Costo</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Centro de Costo</th>
                            <th style="width: 35%;">Responsable Actual</th>
                            <th style="width: 35%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($centros)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No hay centros de costo registrados
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($centros as $c): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($c['nombre']) ?></strong>
                            </td>
                            <td>
                                <?php if ($c['manager_id']): ?>
                                    <div>
                                        <i class="fa-solid fa-user-tie text-primary me-1"></i>
                                        <strong><?= htmlspecialchars($c['manager_nombre']) ?></strong>
                                    </div>
                                    <small class="text-muted"><?= htmlspecialchars($c['manager_email']) ?></small>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Sin responsable</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="/admin/managers/assign" class="row g-2">
                                    <input type="hidden" name="centro_costo_id" value="<?= $c['id'] ?>">
                                    <div class="col-8">
                                        <select name="manager_id" class="form-select form-select-sm">
                                            <option value="">-- Sin responsable --</option>
                                            <?php foreach ($managers as $m): ?>
                                                <option value="<?= $m['id'] ?>" <?= ($c['manager_id'] == $m['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($m['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">
                                            <i class="fa-solid fa-save me-1"></i> Asignar
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($managers)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Managers Disponibles</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($managers as $m): ?>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-user-tie fa-2x text-primary me-3"></i>
                            <div>
                                <strong><?= htmlspecialchars($m['nombre']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($m['email']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php Layout::footer(); ?>
