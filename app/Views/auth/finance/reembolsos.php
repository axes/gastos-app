<?php

use App\Views\Layout;

Layout::header();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Reembolsos</h1>
</div>

<form method="get" action="/finance/reembolsos" class="mb-3">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="aprobado" <?= ($estado ?? '') === 'aprobado' ? 'selected' : '' ?>>Aprobado</option>
                <option value="reembolsado" <?= ($estado ?? '') === 'reembolsado' ? 'selected' : '' ?>>Reembolsado</option>
                <option value="pendiente" <?= ($estado ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="rechazado" <?= ($estado ?? '') === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
                <option value="anulado" <?= ($estado ?? '') === 'anulado' ? 'selected' : '' ?>>Anulado</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <a href="/finance/reembolsos" class="btn btn-outline-secondary flex-fill" data-bs-toggle="tooltip" title="Limpiar filtros">
                <i class="fa-solid fa-eraser"></i>
            </a>
        </div>
    </div>
</form>

<?php if (empty($gastos)): ?>
    <div class="alert alert-info">
        No hay gastos para este estado.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover data-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Proyecto</th>
                            <th>Solicitante</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($gastos as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['id']) ?></td>
                            <td><?= htmlspecialchars($g['fecha']) ?></td>
                            <td><?= htmlspecialchars($g['proyecto_nombre']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($g['created_by_nombre']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($g['created_by_email'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($g['monto']) ?></td>
                            <td>
                                <span class="badge bg-<?= $g['estado'] === 'aprobado' ? 'success' : ($g['estado'] === 'reembolsado' ? 'primary' : 'secondary') ?>">
                                    <?= htmlspecialchars($g['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" data-bs-target="#transfer<?= $g['id'] ?>">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </button>
                                    <?php if ($g['estado'] === 'aprobado'): ?>
                                        <form method="post" action="/finance/reembolsos/mark/<?= $g['id'] ?>" class="d-inline">
                                            <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Marcar reembolsado">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="transfer<?= $g['id'] ?>">
                            <td colspan="7">
                                <strong>Datos de transferencia</strong><br>
                                <span>Banco: <?= htmlspecialchars($g['banco'] ?? 'No informado') ?></span><br>
                                <span>Tipo cuenta: <?= htmlspecialchars($g['tipo_cuenta'] ?? 'No informado') ?></span><br>
                                <span>Numero: <?= htmlspecialchars($g['numero_cuenta'] ?? 'No informado') ?></span><br>
                                <span>Titular: <?= htmlspecialchars($g['titular_cuenta'] ?? 'No informado') ?></span><br>
                                <span>RUT titular: <?= htmlspecialchars($g['rut_titular'] ?? 'No informado') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php Layout::footer(); ?>
