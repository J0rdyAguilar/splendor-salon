CREATE DATABASE IF NOT EXISTS splendor_db CHARACTER SET utf8mb4 COLLATE
utf8mb4_general_ci; USE splendor_db;

DROP TABLE IF EXISTS clientes; CREATE TABLE clientes ( id INT NOT NULL
AUTO_INCREMENT, nombre VARCHAR(100) NOT NULL, telefono VARCHAR(30)
DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, notas TEXT,
fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id) )
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS servicios; CREATE TABLE servicios ( id INT NOT NULL
AUTO_INCREMENT, nombre VARCHAR(100) NOT NULL, precio DECIMAL(10,2) NOT
NULL, duracion_min INT NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS citas; CREATE TABLE citas ( id INT NOT NULL
AUTO_INCREMENT, cliente_id INT NOT NULL, servicio_id INT DEFAULT NULL,
fecha DATE NOT NULL, hora TIME NOT NULL, estado
ENUM(‘pendiente’,‘pagado’,‘cancelado’,‘transferencia_pendiente’) NOT
NULL DEFAULT ‘pendiente’, monto DECIMAL(10,2) DEFAULT NULL, notas TEXT,
creado_en DATETIME DEFAULT CURRENT_TIMESTAMP, duracion INT DEFAULT 60,
hora_fin TIME DEFAULT NULL, PRIMARY KEY (id), KEY cliente_id
(cliente_id), KEY servicio_id (servicio_id), CONSTRAINT fk_citas_cliente
FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
CONSTRAINT fk_citas_servicio FOREIGN KEY (servicio_id) REFERENCES
servicios(id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS movimientos; CREATE TABLE movimientos ( id INT NOT
NULL AUTO_INCREMENT, tipo ENUM(‘ingreso’,‘gasto’) NOT NULL, fecha DATE
NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, monto DECIMAL(10,2) NOT
NULL, cita_id INT DEFAULT NULL, PRIMARY KEY (id), KEY cita_id (cita_id),
CONSTRAINT fk_mov_cita FOREIGN KEY (cita_id) REFERENCES citas(id) )
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS usuarios; CREATE TABLE usuarios ( id INT NOT NULL
AUTO_INCREMENT, username VARCHAR(50) NOT NULL, password_hash
VARCHAR(255) NOT NULL, rol ENUM(‘admin’,‘empleado’) DEFAULT ‘admin’,
PRIMARY KEY (id), UNIQUE KEY username (username) ) ENGINE=InnoDB DEFAULT
CHARSET=utf8mb4;

INSERT INTO usuarios (id, username, password_hash, rol) VALUES
(1,‘splendor’,‘$2y10Lo9IgKEyClucu2Twv/JR..fVvOFNbuP/zQCvTLsO6A83ulU1XA9aW’,‘admin’),
(2,‘empleado’,‘$2y10knBkuYnM0m4O7hGWMo48guyK.ACRz1nFLYcd/tpj///j7jqfpd9fy’,‘empleado’);
