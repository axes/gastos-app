<?php

use App\Views\Layout;

Layout::header();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gastos</h1>
    <a href="/gastos/create" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Nuevo Gasto
    </a>
</div>

<form method="get" action="/gastos" class="mb-3">
    <div class="row g-3 align-items-end mb-3">
        <div class="col-3">
            <label class="form-label">Centro de Costo</label>
            <select name="centro_costo_id" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($centros as $cc): ?>
                    <option value="<?= htmlspecialchars($cc['id']) ?>" <?= ($filters['centro_costo_id'] ?? '') == $cc['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cc['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-3">
            <label class="form-label">Proyecto</label>
            <select name="proyecto_id" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>" <?= ($filters['proyecto_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-2">
            <label class="form-label">Desde</label>
            <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($filters['desde'] ?? '') ?>">
        </div>

        <div class="col-2">
            <label class="form-label">Hasta</label>
            <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($filters['hasta'] ?? '') ?>">
        </div>

        <div class="col-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="fa-solid fa-filter"></i> Filtrar
            </button>
            <a href="/gastos" class="btn btn-outline-secondary flex-fill" data-bs-toggle="tooltip" title="Limpiar filtros">
                <i class="fa-solid fa-eraser"></i>
            </a>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover data-table mb-0 table-sm">
    <thead>
        <tr>
            <th style="font-size: 0.85rem;">ID</th>
            <th style="font-size: 0.85rem;">Fecha</th>
            <th style="font-size: 0.85rem;">Centro</th>
            <th style="font-size: 0.85rem;">Proyecto</th>
            <th style="font-size: 0.85rem;">Tipo</th>
            <th style="font-size: 0.85rem;">Categoría</th>
            <th style="font-size: 0.85rem;">Medio</th>
            <th style="font-size: 0.85rem;">Monto</th>
            <th style="font-size: 0.85rem;">Estado</th>
            <th style="font-size: 0.85rem;">✓</th>
            <th style="font-size: 0.85rem;">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($gastos as $g): ?>
        <tr style="font-size: 0.85rem;">
            <td style="width: 5%;"><?= htmlspecialchars($g['id']) ?></td>
            <td style="width: 8%;"><?= htmlspecialchars($g['fecha']) ?></td>
            <td style="width: 10%;"><small><?= htmlspecialchars($g['centro_costo_nombre']) ?></small></td>
            <td style="width: 12%;"><small><?= htmlspecialchars($g['proyecto_nombre']) ?></small></td>
            <td style="width: 8%;">
                <span class="badge bg-info text-dark">
                    <?= substr(htmlspecialchars($g['tipo'] ?? 'registro'), 0, 3) ?>
                </span>
            </td>
            <td style="width: 10%;"><small><?= htmlspecialchars($g['categoria_nombre']) ?></small></td>
            <td style="width: 10%;"><small><?= htmlspecialchars($g['medio_pago_nombre']) ?></small></td>
            <td style="width: 8%; text-align: right;"><?= htmlspecialchars($g['monto']) ?></td>
            <td style="width: 8%;">
                <?php
                    $estadoIcon = '';
                    $estadoColor = '';
                    $estadoLabel = '';
                    switch ($g['estado']) {
                        case 'pendiente':
                            $estadoIcon = 'fa-clock';
                            $estadoColor = 'secondary';
                            $estadoLabel = 'Pendiente';
                            break;
                        case 'aprobado':
                            $estadoIcon = 'fa-check';
                            $estadoColor = 'success';
                            $estadoLabel = 'Aprobado';
                            break;
                        case 'rechazado':
                            $estadoIcon = 'fa-xmark';
                            $estadoColor = 'danger';
                            $estadoLabel = 'Rechazado';
                            break;
                        case 'reembolsado':
                            $estadoIcon = 'fa-money-bill';
                            $estadoColor = 'primary';
                            $estadoLabel = 'Reembolsado';
                            break;
                        case 'anulado':
                            $estadoIcon = 'fa-ban';
                            $estadoColor = 'dark';
                            $estadoLabel = 'Anulado';
                            break;
                    }
                ?>
                <span class="badge bg-<?= $estadoColor ?>" data-bs-toggle="tooltip" title="<?= $estadoLabel ?>">
                    <i class="fa-solid <?= $estadoIcon ?>"></i>
                </span>
                <?php if (($g['tipo'] ?? '') === 'reembolso' && ($g['estado'] ?? '') === 'aprobado'): ?>
                    <span class="badge bg-warning text-dark" data-bs-toggle="tooltip" title="Por reembolsar">
                        <i class="fa-solid fa-exclamation"></i>
                    </span>
                <?php endif; ?>
            </td>
            <td style="width: 5%; text-align: center;">
                <i class="fa-solid <?= $g['activo'] ? 'fa-eye text-success' : 'fa-eye-slash text-secondary' ?>" data-bs-toggle="tooltip" title="<?= $g['activo'] ? 'Activo' : 'Inactivo' ?>"></i>
            </td>
            <td style="width: 12%;">
                <div class="d-flex gap-1">
                    <a href="/gastos/edit/<?= $g['id'] ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="post" action="/gastos/deactivate/<?= $g['id'] ?>" class="d-inline">
                        <button type="submit" class="btn btn-sm btn-outline-<?= $g['activo'] ? 'danger' : 'success' ?>" data-bs-toggle="tooltip" title="<?= $g['activo'] ? 'Desactivar' : 'Activar' ?>">
                            <i class="fa-solid fa-<?= $g['activo'] ? 'ban' : 'check' ?>"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
            </table>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
