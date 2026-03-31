# Sistema de Gestion de Gastos

Aplicacion web en PHP (arquitectura MVC ligera) para administrar centros de costo, proyectos, gastos y flujo de aprobacion/reembolso con control por roles.

## Quick Start

Guia corta para que cualquier persona pueda descargar, configurar y ejecutar el proyecto localmente.

### 1) Descargar el proyecto

Opcion A: si ya tienes el codigo local, entra al directorio del proyecto.

Opcion B: clonar desde GitHub:

```bash
git clone https://github.com/USUARIO/NOMBRE-REPOSITORIO.git
cd NOMBRE-REPOSITORIO
```

### 2) Configurar entorno

```bash
cp .env.example .env
```

Edita `.env` con tus credenciales locales de base de datos.

### 3) Crear base de datos y cargar esquema

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS gastos_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p gastos_app < database/schema.sql
```

### 4) Levantar servidor local

```bash
./run-server.sh
```

### 5) Abrir aplicacion

```text
http://127.0.0.1:8000/login
```

### 6) Credenciales de prueba

Usuarios de desarrollo incluidos en el seed:

- admin-001 / admin123
- manager-001 / manager123
- user-001 / user123
- finance-001 / admin123

### 7) Empezar a modificar

Puntos recomendados para desarrollo:

- Rutas: `routes/web.php`
- Controladores: `app/Controllers/`
- Vistas: `app/Views/`
- Estilos: `public/css/`
- JavaScript: `public/js/app.js`
- Modelo de datos: `database/schema.sql`

## Caracteristicas principales

- Autenticacion con sesion y control de acceso por rol.
- CRUD de centros de costo y proyectos (con activacion/desactivacion logica).
- Registro de gastos por tipo:
	- `adelanto`
	- `reembolso`
	- `registro`
- Flujo de aprobacion por manager/encargado.
- Flujo de reembolso para rol de finanzas.
- Perfil de usuario con datos de transferencia.
- UI con Bootstrap, Select2, DataTables y SweetAlert2.

## Stack tecnologico

- Backend: PHP 8+ con PDO (MySQL/MariaDB)
- Frontend: Bootstrap 5, jQuery, DataTables, Select2, Font Awesome, SweetAlert2
- Routing: Router propio con parametros tipo `/ruta/{id}`
- Autoload: Cargador propio para namespace `App\\*` (sin Composer autoload)

## Requisitos

- PHP 8.0 o superior
- Extension PDO MySQL habilitada
- MySQL 8+ o MariaDB compatible

## Estructura del proyecto

```text
.
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Core/
│   ├── Models/
│   ├── Services/
│   └── Views/
├── database/
│   └── schema.sql
├── public/
│   ├── index.php
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── storage/
│   └── documents/
└── run-server.sh
```

## Instalacion rapida

1. Clonar o abrir el proyecto.
2. Copiar variables de entorno:

```bash
cp .env.example .env
```

3. Ajustar credenciales en `.env`.
4. Crear base de datos e importar esquema:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS gastos_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p gastos_app < database/schema.sql
```

5. Levantar servidor local:

```bash
./run-server.sh
```

6. Abrir en navegador:

```text
http://127.0.0.1:8000/login
```

## Variables de entorno

Basadas en `.env.example`:

```env
APP_NAME=GastosApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_NAME=gastos_app
DB_USER=root
DB_PASS=secret
```

## Usuarios de prueba (seed)

Definidos en `database/schema.sql`:

- Admin: `admin-001`
- Manager: `manager-001`
- User: `user-001`
- Finance: `finance-001`

> Las contrasenas vienen hasheadas en el seed SQL.

## Flujo funcional

1. Usuario crea gasto (`pendiente`).
2. Manager o encargado aprueba/rechaza.
3. Si es tipo `reembolso` y fue aprobado, finanzas lo marca como `reembolsado`.

Estados disponibles del gasto:

- `pendiente`
- `aprobado`
- `rechazado`
- `anulado`
- `reembolsado`

## Modulos y rutas principales

- Auth: `/login`, `/logout`
- Dashboard: `/dashboard`
- Centros de costo: `/centros_costo`
- Proyectos: `/proyectos`
- Gastos: `/gastos`
- Aprobaciones: `/approve/gastos`
- Miembros de proyecto (admin/manager):
	- `/admin/project-members`
	- `/manager/project-members`
- Finanzas (reembolsos): `/finance/reembolsos`
- Perfil: `/profile`

## Comandos utiles

Levantar servidor en otro puerto:

```bash
HOST=127.0.0.1 PORT=8080 ./run-server.sh
```

Detener servidor:

- Si se ejecuto en foreground: `Ctrl + C`
- Si se ejecuto en background: terminar el proceso PHP correspondiente.

## Notas tecnicas

- Entry point: `public/index.php`
- Router: `routes/web.php` + `app/Core/Router.php`
- Persistencia: PDO con sentencias preparadas
- Soft-delete: campos `activo` (no se elimina fisicamente)

## Seguridad

- Passwords almacenadas con hash.
- Validacion de sesion para rutas privadas.
- Recomendado revisar `SECURITY_CHECKLIST.md` antes de despliegue.

## Estado del proyecto

Base funcional para operacion de gastos por roles con flujo completo de aprobacion y reembolso. Ideal para extender con reportes, exportaciones y pruebas automatizadas.

