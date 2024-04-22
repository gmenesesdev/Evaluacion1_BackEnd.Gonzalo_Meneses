-- mysql
CREATE DATABASE ciisa_backend_eval_u1;
CREATE USER 'ciisa_backend_eval_u1'@'localhost' IDENTIFIED BY 'cl4v3-r00t';
GRANT ALL PRIVILEGES ON ciisa_backend_eval_u1.* TO 'ciisa_backend_eval_u1'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE mantenedor(
    id INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT FALSE
);

INSERT INTO mantenedor (id, nombre) VALUES 
(1, 'Dato 1'),
(2, 'Dato 2'),
(3, 'Dato 3');