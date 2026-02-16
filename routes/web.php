<?php

use App\Core\Router;
use App\Core\AuthMiddleware;
use App\Services\AuthService;

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/', function () {
	if (AuthService::check()) {
		header('Location: /dashboard');
		exit;
	}

	header('Location: /login');
	exit;
});

// Centros de Costo
$router->get('/centros_costo', 'CentroCostoController@index');
$router->get('/centros_costo/create', 'CentroCostoController@createForm');
$router->post('/centros_costo/create', 'CentroCostoController@create');
$router->get('/centros_costo/edit/{id}', 'CentroCostoController@editForm');
$router->post('/centros_costo/edit/{id}', 'CentroCostoController@update');
$router->post('/centros_costo/deactivate/{id}', 'CentroCostoController@deactivate');

// Proyectos
$router->get('/proyectos', 'ProyectoController@index');
$router->get('/proyectos/create', 'ProyectoController@createForm');
$router->post('/proyectos/create', 'ProyectoController@create');
$router->get('/proyectos/edit/{id}', 'ProyectoController@editForm');
$router->post('/proyectos/edit/{id}', 'ProyectoController@update');
$router->post('/proyectos/deactivate/{id}', 'ProyectoController@deactivate');

// Gastos
$router->get('/gastos', 'GastoController@index');
$router->get('/gastos/create', 'GastoController@createForm');
$router->post('/gastos/create', 'GastoController@create');
$router->get('/gastos/edit/{id}', 'GastoController@editForm');
$router->post('/gastos/edit/{id}', 'GastoController@update');
$router->post('/gastos/deactivate/{id}', 'GastoController@deactivate');

// Aprobación de gastos (manager/encargado)
$router->get('/approve/gastos', 'ApprovalController@index');
$router->post('/approve/gastos/{id}', 'ApprovalController@approve');
$router->post('/reject/gastos/{id}', 'ApprovalController@reject');

// Admin: Asignar Managers a Centros
$router->get('/admin/managers', 'AdminManagerController@index');
$router->post('/admin/managers/assign', 'AdminManagerController@assign');
$router->post('/admin/managers/unassign/{id}', 'AdminManagerController@unassign');

// Admin/Manager: Gestionar Miembros de Proyectos
$router->get('/admin/project-members', 'AdminProjectMembersController@index');
$router->post('/admin/project-members/assign', 'AdminProjectMembersController@assign');
$router->post('/admin/project-members/unassign/{id}', 'AdminProjectMembersController@unassign');
$router->post('/admin/project-members/promote/{id}', 'AdminProjectMembersController@promoteToEncargado');
$router->post('/admin/project-members/demote/{id}', 'AdminProjectMembersController@demoteToMember');

$router->get('/manager/project-members', 'ManagerProjectMembersController@index');
$router->post('/manager/project-members/assign', 'ManagerProjectMembersController@assign');
$router->post('/manager/project-members/unassign/{id}', 'ManagerProjectMembersController@unassign');
$router->post('/manager/project-members/promote/{id}', 'ManagerProjectMembersController@promoteToEncargado');
$router->post('/manager/project-members/demote/{id}', 'ManagerProjectMembersController@demoteToMember');

// Profile
$router->get('/profile', 'ProfileController@index');
$router->post('/profile', 'ProfileController@update');

// Finance: Reembolsos
$router->get('/finance/reembolsos', 'FinanceController@index');
$router->post('/finance/reembolsos/mark/{id}', 'FinanceController@markReembolsado');

$router->get('/dashboard', 'DashboardController@index');

// Perfil
$router->get('/profile', 'ProfileController@index');
$router->post('/profile', 'ProfileController@update');

// Finanzas: Reembolsos
$router->get('/finance/reembolsos', 'FinanceController@index');
$router->post('/finance/reembolsos/mark/{id}', 'FinanceController@markReembolsado');
