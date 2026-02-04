
-- Script de creación de base de datos y tabla Producto 
CREATE DATABASE IF NOT EXISTS daw_products_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE daw_products_db;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  category VARCHAR(80) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO products (name, price, stock, category) VALUES
('Teclado inalámbrico', 25.90, 30, 'Periféricos'),
('Mouse gamer', 19.50, 50, 'Periféricos'),
('Cargador USB-C 45W', 29.99, 15, 'Accesorios');

-- Script de creación de base de datos y tabla Usuarios

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;