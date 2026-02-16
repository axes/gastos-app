-- Schema inicial para gastos-app

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rut VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    departamento VARCHAR(100) NULL COMMENT 'Departamento o área del usuario',
    banco VARCHAR(120) NULL,
    tipo_cuenta VARCHAR(50) NULL,
    numero_cuenta VARCHAR(50) NULL,
    titular_cuenta VARCHAR(150) NULL,
    rut_titular VARCHAR(20) NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    role ENUM('admin', 'manager', 'user', 'finance') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS centros_costo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    centro_costo_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    fecha_inicio DATE NULL,
    fecha_termino DATE NULL,
    presupuesto DECIMAL(12, 2) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_proyectos_centro_costo
        FOREIGN KEY (centro_costo_id)
        REFERENCES centros_costo(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS centro_costo_managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    centro_costo_id INT NOT NULL,
    user_id INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_manager_cc (centro_costo_id, user_id),
    CONSTRAINT fk_ccm_centro_costo
        FOREIGN KEY (centro_costo_id)
        REFERENCES centros_costo(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_ccm_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS proyecto_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    user_id INT NOT NULL,
    role_in_project ENUM('member', 'encargado') NOT NULL DEFAULT 'member',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_member_project (proyecto_id, user_id),
    INDEX idx_proyecto_role (proyecto_id, role_in_project),
    CONSTRAINT fk_pm_proyecto
        FOREIGN KEY (proyecto_id)
        REFERENCES proyectos(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_pm_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de prueba para categorias
INSERT INTO categorias (nombre, activo) VALUES
('Viaticos', 1),
('Compras de Productos', 1),
('Pagos de Servicios', 1),
('Arriendo', 1),
('Transporte', 1),
('Capacitacion', 1);

CREATE TABLE IF NOT EXISTS medios_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de prueba para medios de pago
INSERT INTO medios_pago (nombre, activo) VALUES
('Pago en efectivo', 1),
('Pago por Transferencia', 1),
('Pago con tarjeta de credito', 1),
('Pago con tarjeta de debito', 1);

-- Datos de prueba para usuarios (roles: admin, manager, user)
-- Admin: rut=admin-001, password=admin123, departamento=Admin
-- Manager/Gino: rut=manager-001, password=manager123, departamento=IT (Jefe de área)
-- User/Nelson: rut=user-001, password=user123, departamento=IT (Miembro de IT)
INSERT INTO users (rut, nombre, email, password_hash, departamento, estado, role) VALUES
('admin-001', 'Administrador', 'admin@example.com', '$2y$10$ebf4AW0korAVht7ZY5J1yueU49qCgaC2C.jY1dtwVKkaaYU2GhqMq', 'Administración', 'activo', 'admin'),
('manager-001', 'Gino (Jefe IT)', 'gino@example.com', '$2y$10$xyWOkYQ2QLF0mdWd7wQnL.rm/g.QshpjP154agVs4ukoBGKm7oufK', 'IT', 'activo', 'manager'),
('user-001', 'Nelson (IT)', 'nelson@example.com', '$2y$10$Vfp76.tkoN5Ggv8mnlGtI.metyx5Dfs/PmaH1gcbxg2P47rpaCaZi', 'IT', 'activo', 'user'),
('finance-001', 'Equipo Finanzas', 'finance@example.com', '$2y$10$ebf4AW0korAVht7ZY5J1yueU49qCgaC2C.jY1dtwVKkaaYU2GhqMq', 'Finanzas', 'activo', 'finance');

CREATE TABLE IF NOT EXISTS gastos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    fecha DATE NOT NULL,
    monto DECIMAL(12, 2) NOT NULL,
    categoria_id INT NOT NULL,
    medio_pago_id INT NOT NULL,
    descripcion TEXT NULL,
    documento VARCHAR(255) NULL,
    tipo ENUM('adelanto', 'reembolso', 'registro') NOT NULL DEFAULT 'registro',
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'anulado', 'reembolsado') NOT NULL DEFAULT 'pendiente',
    created_by INT NOT NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    review_comment TEXT NULL,
    reembolsado_by INT NULL,
    reembolsado_at DATETIME NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_estado (estado),
    INDEX idx_proyecto (proyecto_id),
    CONSTRAINT fk_gastos_proyecto
        FOREIGN KEY (proyecto_id)
        REFERENCES proyectos(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_gastos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_gastos_medio_pago
        FOREIGN KEY (medio_pago_id)
        REFERENCES medios_pago(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_gastos_created_by
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_gastos_reviewed_by
        FOREIGN KEY (reviewed_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT fk_gastos_reembolsado_by
        FOREIGN KEY (reembolsado_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);
