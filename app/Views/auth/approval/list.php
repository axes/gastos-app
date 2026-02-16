<?php

use App\Views\Layout;

Layout::header();
?>

<h1>Bandeja de Aprobación</h1>

<?php if (empty($gastos)): ?>
    <div class="alert alert-info">
        No hay gastos pendientes de aprobación.
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Centro</th>
                <th>Proyecto</th>
                <th>Tipo</th>
                <th>Creado por</th>
                <th>Categoria</th>
                <th>Monto</th>
                <th>Medio Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($gastos as $g): ?>
            <tr>
                <td><?= htmlspecialchars($g['id']) ?></td>
                <td><?= htmlspecialchars($g['fecha']) ?></td>
                <td><?= htmlspecialchars($g['centro_costo_nombre']) ?></td>
                <td><?= htmlspecialchars($g['proyecto_nombre']) ?></td>
                <td>
                    <span class="badge bg-info text-dark">
                        <?= htmlspecialchars($g['tipo'] ?? 'registro') ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($g['created_by_nombre']) ?></td>
                <td><?= htmlspecialchars($g['categoria_nombre']) ?></td>
                <td><?= htmlspecialchars($g['monto']) ?></td>
                <td><?= htmlspecialchars($g['medio_pago_nombre']) ?></td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?= $g['id'] ?>" title="Aprobar">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $g['id'] ?>" title="Rechazar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <button class="btn btn-sm btn-info" data-bs-toggle="collapse" data-bs-target="#details<?= $g['id'] ?>" title="Detalles">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <tr class="collapse" id="details<?= $g['id'] ?>">
                <td colspan="9">
                    <strong>Descripción:</strong> <?= htmlspecialchars($g['descripcion'] ?? 'Sin descripción') ?><br>
                    <strong>Documento:</strong> <?= htmlspecialchars($g['documento'] ?? 'Sin documento') ?>
                </td>
            </tr>

            <!-- Modal Aprobar -->
            <div class="modal fade" id="approveModal<?= $g['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Aprobar Gasto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" action="/approve/gastos/<?= $g['id'] ?>">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Comentario (opcional):</label>
                                    <textarea name="comment" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Aprobar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Rechazar -->
            <div class="modal fade" id="rejectModal<?= $g['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Rechazar Gasto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" action="/reject/gastos/<?= $g['id'] ?>">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Motivo del rechazo (requerido):</label>
                                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Rechazar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php Layout::footer(); ?>
