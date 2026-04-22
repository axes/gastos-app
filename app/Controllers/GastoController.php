<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\CentroCosto;
use App\Models\Proyecto;
use App\Models\Categoria;
use App\Models\MedioPago;
use App\Models\Gasto;
use App\Services\AuthService;
use App\Services\AuthorizationService;

class GastoController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        // Get gastos accessible to current user based on role
        $gastos = AuthorizationService::getAccessibleGastos();

        // Apply additional filters if provided
        $filters = [
            'centro_costo_id' => $_GET['centro_costo_id'] ?? '',
            'proyecto_id' => $_GET['proyecto_id'] ?? '',
            'desde' => $_GET['desde'] ?? '',
            'hasta' => $_GET['hasta'] ?? '',
        ];

        // Re-filter based on user scope
        $gastos = $this->applyFilters($gastos, $filters);

        // For filter dropdowns, show only user's accessible data
        $centros = $this->getAccessibleCentros();
        $proyectos = $this->getAccessibleProyectos();

        require_once __DIR__ . '/../Views/auth/gastos/list.php';
    }

    public function createForm()
    {
        $proyectos = $this->getAccessibleProyectosForForm();
        if (empty($proyectos)) {
            $_SESSION['error'] = 'No tienes acceso a proyectos para crear gastos.';
            header('Location: /gastos');
            exit;
        }

        $categorias = (new Categoria($this->db))->getActive();
        $mediosPago = (new MedioPago($this->db))->getActive();

        require_once __DIR__ . '/../Views/auth/gastos/create.php';
    }

    public function create()
    {
        $data = $this->collectPayload();
        if (!$data) {
            header('Location: /gastos/create');
            exit;
        }

        // Verify user can create gasto in this proyecto
        if (!AuthorizationService::canCreateGasto($data['proyecto_id'])) {
            $_SESSION['error'] = 'No tienes permiso para crear gastos en este proyecto.';
            header('Location: /gastos');
            exit;
        }

        // Add current user as created_by
        $user = AuthService::user();
        $data['created_by'] = $user['id'];
        $data['estado'] = 'pendiente';

        (new Gasto($this->db))->create($data);
        $this->maybeWarnBudget($data['proyecto_id'], $data['tipo'], $data['monto']);
        $_SESSION['success'] = 'Gasto creado exitosamente.';
        header('Location: /gastos');
        exit;
    }

    public function editForm($id)
    {
        $gasto = (new Gasto($this->db))->getById($id);
        if (!$gasto) {
            http_response_code(404);
            echo 'Gasto no encontrado';
            return;
        }

        if (!$this->canManageGasto($gasto)) {
            http_response_code(403);
            echo 'No tienes permiso para editar este gasto.';
            return;
        }

        $proyectos = $this->getAccessibleProyectosForForm($gasto['proyecto_id']);
        $categorias = (new Categoria($this->db))->getActive();
        $mediosPago = (new MedioPago($this->db))->getActive();

        require_once __DIR__ . '/../Views/auth/gastos/edit.php';
    }

    public function update($id)
    {
        $gasto = (new Gasto($this->db))->getById($id);
        if (!$gasto) {
            http_response_code(404);
            echo 'Gasto no encontrado';
            return;
        }

        if (!$this->canManageGasto($gasto)) {
            $_SESSION['error'] = 'No tienes permiso para editar este gasto.';
            header('Location: /gastos');
            exit;
        }

        $data = $this->collectPayload();
        if (!$data) {
            header('Location: /gastos/edit/' . $id);
            exit;
        }

        if ($gasto['estado'] !== 'pendiente' && $data['tipo'] !== $gasto['tipo']) {
            $_SESSION['error'] = 'No puedes cambiar el tipo de gasto si ya fue revisado.';
            header('Location: /gastos/edit/' . $id);
            exit;
        }

        $data['activo'] = isset($_POST['activo']) ? 1 : 0;
        $data['estado'] = $gasto['estado'];
        (new Gasto($this->db))->update($id, $data);
        $_SESSION['success'] = 'Gasto actualizado exitosamente.';
        header('Location: /gastos');
        exit;
    }

    public function deactivate($id)
    {
        $gasto = (new Gasto($this->db))->getById($id);
        if (!$gasto) {
            http_response_code(404);
            return;
        }

        if (!$this->canManageGasto($gasto)) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /gastos');
            exit;
        }

        (new Gasto($this->db))->toggleActive($id);
        header('Location: /gastos');
        exit;
    }

    private function canManageGasto(array $gasto): bool
    {
        $user = AuthService::user();
        $isCreator = $gasto['created_by'] == $user['id'];
        $isEncargado = AuthorizationService::isEncargadoOfProyecto($gasto['proyecto_id']);
        $isAdmin = AuthorizationService::isAdmin();

        return $isCreator || $isEncargado || $isAdmin;
    }

    private function getAccessibleCentros()
    {
        if (AuthorizationService::isAdmin()) {
            return (new CentroCosto($this->db))->getAll();
        }

        if (AuthorizationService::isManager()) {
            $centroIds = AuthorizationService::getManagedCentroCostos();
            if (empty($centroIds)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($centroIds), '?'));
            $stmt = $this->db->prepare(
                "SELECT * FROM centros_costo WHERE id IN ({$placeholders})"
            );
            $stmt->execute($centroIds);
            return $stmt->fetchAll();
        }

        return [];
    }

    private function getAccessibleProyectos()
    {
        $proyectoIds = AuthorizationService::getAccessibleProyectos();
        if (empty($proyectoIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($proyectoIds), '?'));
        $stmt = $this->db->prepare(
            "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
             FROM proyectos p
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             WHERE p.id IN ({$placeholders})"
        );
        $stmt->execute($proyectoIds);
        return $stmt->fetchAll();
    }

    private function getAccessibleProyectosForForm($includeId = null)
    {
        if (AuthorizationService::isAdmin()) {
            return (new Proyecto($this->db))->getActiveWithCentroCosto($includeId);
        }

        if (AuthorizationService::isManager()) {
            $centroIds = AuthorizationService::getManagedCentroCostos();
            if (empty($centroIds)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($centroIds), '?'));
            $sql = "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
                    FROM proyectos p
                    INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                    WHERE (p.activo = 1 OR p.id = ?) 
                    AND p.centro_costo_id IN ({$placeholders})
                    ORDER BY p.nombre ASC";
            $stmt = $this->db->prepare($sql);
            $params = array_merge([$includeId ?? 0], $centroIds);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        // User: only proyectos where they're member
        $user = AuthService::user();
        $stmt = $this->db->prepare(
            "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
             FROM proyectos p
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             INNER JOIN proyecto_members pm ON pm.proyecto_id = p.id
             WHERE pm.user_id = :user_id AND pm.activo = 1 AND (p.activo = 1 OR p.id = :include_id)
             ORDER BY p.nombre ASC"
        );
        $stmt->bindValue(':user_id', $user['id']);
        $stmt->bindValue(':include_id', $includeId ?? 0, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function applyFilters(array $gastos, array $filters): array
    {
        if (!empty($filters['centro_costo_id'])) {
            $gastos = array_filter($gastos, fn($g) =>
                $g['centro_costo_id'] == $filters['centro_costo_id']
            );
        }

        if (!empty($filters['proyecto_id'])) {
            $gastos = array_filter($gastos, fn($g) =>
                $g['proyecto_id'] == $filters['proyecto_id']
            );
        }

        if (!empty($filters['desde'])) {
            $gastos = array_filter($gastos, fn($g) =>
                $g['fecha'] >= $filters['desde']
            );
        }

        if (!empty($filters['hasta'])) {
            $gastos = array_filter($gastos, fn($g) =>
                $g['fecha'] <= $filters['hasta']
            );
        }

        return array_values($gastos);
    }

    private function collectPayload()
    {
        $proyectoId = (int)($_POST['proyecto_id'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? '');
        $monto = trim($_POST['monto'] ?? '');
        $categoriaId = (int)($_POST['categoria_id'] ?? 0);
        $medioPagoId = (int)($_POST['medio_pago_id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? '');

        $allowedTipos = ['adelanto', 'reembolso', 'registro'];
        if (!in_array($tipo, $allowedTipos, true)) {
            $_SESSION['error'] = 'Tipo de gasto invalido.';
            return null;
        }

        if ($proyectoId <= 0 || $fecha === '' || $monto === '' || $categoriaId <= 0 || $medioPagoId <= 0) {
            $_SESSION['error'] = 'Proyecto, fecha, monto, categoria y medio de pago son obligatorios.';
            return null;
        }

        return [
            'proyecto_id' => $proyectoId,
            'fecha' => $fecha,
            'monto' => $monto,
            'categoria_id' => $categoriaId,
            'medio_pago_id' => $medioPagoId,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'documento' => trim($_POST['documento'] ?? ''),
            'tipo' => $tipo,
        ];
    }

    private function maybeWarnBudget($proyectoId, $tipo, $monto): void
    {
        if ($tipo !== 'adelanto') {
            return;
        }

        $proyecto = (new Proyecto($this->db))->getById($proyectoId);
        if (!$proyecto || empty($proyecto['presupuesto'])) {
            return;
        }

        $total = (new Gasto($this->db))->getTotalAdelantoByProyecto($proyectoId);
        $nuevoTotal = $total + (float)$monto;

        if ($nuevoTotal > (float)$proyecto['presupuesto']) {
            $_SESSION['success'] = 'Gasto creado. Atencion: el presupuesto del proyecto fue superado.';
        }
    }
}
