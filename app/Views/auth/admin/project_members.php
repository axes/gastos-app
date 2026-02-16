<?php

use App\Views\Layout;

Layout::header();
?>

<div class="mb-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Gestión de Miembros en Proyectos</h1>
            <p class="text-muted mt-2">Asigna miembros y define encargados para cada proyecto.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Asignar Miembros a Proyecto</h5>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/project-members/assign" id="assignForm">
                <div class="row g-3">
                    <div class="col-lg-12">
                        <label class="form-label fw-semibold">Proyecto</label>
                        <select name="proyecto_id" class="form-select form-select-lg select2-single"
                                id="proyecto_select" required>
                            <option value="">Selecciona un proyecto</option>
                            <?php foreach ($proyectos as $p): ?>
                                <option value="<?= htmlspecialchars($p['id']) ?>">
                                    <?= htmlspecialchars($p['nombre']) ?> (<?= htmlspecialchars($p['centro_costo']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Miembros del equipo</label>
                        <select name="users_data[]" class="form-select form-select-lg select2-multiple"
                                id="users_select" required multiple>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= htmlspecialchars($u['id']) ?>">
                                    <?= htmlspecialchars($u['nombre']) ?> (<?= htmlspecialchars($u['departamento'] ?? 'Sin Depto') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted d-block mt-2">
                            Puedes seleccionar varios usuarios. Los ya asignados se actualizaran automaticamente.
                        </small>
                    </div>

                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Encargados</label>
                        <select name="encargados[]" class="form-select form-select-lg select2-multiple"
                                id="encargados_select" multiple>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= htmlspecialchars($u['id']) ?>">
                                    <?= htmlspecialchars($u['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted d-block mt-2">
                            Los encargados pueden aprobar gastos. Los demas seran miembros normales.
                        </small>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-4">
                        <button type="submit" class="btn btn-primary w-100">
                            Guardar Asignaciones
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Proyectos y Gestión de Miembros</h5>
        </div>
        <div class="card-body">
            <?php if (empty($proyectos)): ?>
                <div class="alert alert-info">
                    No hay proyectos registrados.
                </div>
            <?php else: ?>
                <?php foreach ($proyectos as $p): ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($p['nombre']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($p['centro_costo']) ?></small>
                            </div>
                            <span class="badge bg-secondary"><?= (int)$p['member_count'] ?> miembros</span>
                        </div>
                        <div class="card-body">

                            <?php
                            $stmt = $db->prepare(
                                "SELECT pm.id, u.id as user_id, u.nombre, u.departamento, pm.role_in_project 
                                 FROM proyecto_members pm
                                 INNER JOIN users u ON u.id = pm.user_id
                                 WHERE pm.proyecto_id = :proyecto_id AND pm.activo = 1
                                 ORDER BY pm.role_in_project DESC, u.nombre"
                            );
                            $stmt->execute(['proyecto_id' => $p['id']]);
                            $members = $stmt->fetchAll();
                            ?>

                            <?php if (empty($members)): ?>
                                <div class="alert alert-warning alert-sm mb-0">
                                    No hay miembros asignados a este proyecto.
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                <?php foreach ($members as $m): ?>
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="card-title mb-0"><?= htmlspecialchars($m['nombre']) ?></h6>
                                                        <small class="text-muted d-block">
                                                            <?= htmlspecialchars($m['departamento'] ?? 'Sin Depto') ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge <?= $m['role_in_project'] === 'encargado' ? 'bg-warning text-dark' : 'bg-info' ?>">
                                                        <?= ucfirst(htmlspecialchars($m['role_in_project'])) ?>
                                                    </span>
                                                </div>

                                                <div class="d-flex gap-2 mt-3">
                                                    <?php if ($m['role_in_project'] === 'member'): ?>
                                                        <form method="post" action="/admin/project-members/promote/<?= $m['id'] ?>" class="flex-fill">
                                                            <button type="submit" class="btn btn-sm btn-outline-warning w-100" title="Promover a Encargado">
                                                                Promover
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="post" action="/admin/project-members/demote/<?= $m['id'] ?>" class="flex-fill">
                                                            <button type="submit" class="btn btn-sm btn-outline-info w-100" title="Degradar a Miembro">
                                                                Degradar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <form method="post" action="/admin/project-members/unassign/<?= $m['id'] ?>" class="flex-fill"
                                                          onsubmit="return confirm('¿Remover a <?= htmlspecialchars(addslashes($m['nombre'])) ?> del proyecto?');">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" title="Remover del proyecto">
                                                            Remover
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php Layout::footer(); ?>
