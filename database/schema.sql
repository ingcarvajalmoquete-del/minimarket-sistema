-- ============================================================
-- MINI MARKET MANAGEMENT SYSTEM - DATABASE SCHEMA
-- Compatible con MySQL 8.0 / MySQL Workbench
-- Autor: Sistema generado con PHP + MySQL
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Base de datos
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `minimarket_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `minimarket_db`;

-- ------------------------------------------------------------
-- Tabla: users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(100) NOT NULL,
  `email`        VARCHAR(150) NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,
  `role`         ENUM('admin','cajero','almacen') NOT NULL DEFAULT 'cajero',
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role`  (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: categories
-- ------------------------------------------------------------
CREATE TABLE `categories` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(100) NOT NULL UNIQUE,
  `description`  TEXT,
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: suppliers (Proveedores)
-- ------------------------------------------------------------
CREATE TABLE `suppliers` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(150) NOT NULL,
  `contact_name` VARCHAR(100),
  `phone`        VARCHAR(20),
  `email`        VARCHAR(150),
  `address`      TEXT,
  `rnc`          VARCHAR(20) COMMENT 'RNC o NIT del proveedor',
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: customers (Clientes)
-- ------------------------------------------------------------
CREATE TABLE `customers` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(150) NOT NULL,
  `phone`        VARCHAR(20),
  `email`        VARCHAR(150),
  `address`      TEXT,
  `cedula`       VARCHAR(20) COMMENT 'Cédula/DNI del cliente',
  `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_name`   (`name`),
  INDEX `idx_cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: products (Productos)
-- ------------------------------------------------------------
CREATE TABLE `products` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`   INT UNSIGNED NOT NULL,
  `supplier_id`   INT UNSIGNED,
  `code`          VARCHAR(50) UNIQUE COMMENT 'Código de barras o código interno',
  `name`          VARCHAR(200) NOT NULL,
  `description`   TEXT,
  `unit`          VARCHAR(30) NOT NULL DEFAULT 'unidad' COMMENT 'unidad, kg, litro, caja, etc.',
  `cost_price`    DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de costo',
  `sale_price`    DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de venta',
  `stock`         INT NOT NULL DEFAULT 0 COMMENT 'Cantidad en inventario',
  `min_stock`     INT NOT NULL DEFAULT 5 COMMENT 'Stock mínimo para alerta',
  `image`         VARCHAR(255) DEFAULT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_code`        (`code`),
  INDEX `idx_name`        (`name`),
  INDEX `idx_category`    (`category_id`),
  INDEX `idx_stock_alert` (`stock`, `min_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: sales (Ventas / Facturas)
-- ------------------------------------------------------------
CREATE TABLE `sales` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(20) NOT NULL UNIQUE,
  `customer_id`    INT UNSIGNED DEFAULT NULL COMMENT 'NULL = cliente genérico',
  `user_id`        INT UNSIGNED NOT NULL COMMENT 'Cajero que realizó la venta',
  `subtotal`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax`            DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'ITBIS 18%',
  `discount`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` ENUM('efectivo','tarjeta','transferencia','mixto') NOT NULL DEFAULT 'efectivo',
  `amount_paid`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `change_amount`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`         ENUM('completada','anulada','pendiente') NOT NULL DEFAULT 'completada',
  `notes`          TEXT,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sales_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_sales_user`     FOREIGN KEY (`user_id`)     REFERENCES `users`     (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_invoice`     (`invoice_number`),
  INDEX `idx_customer`    (`customer_id`),
  INDEX `idx_user`        (`user_id`),
  INDEX `idx_created`     (`created_at`),
  INDEX `idx_status`      (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: sale_details (Detalle de Ventas)
-- ------------------------------------------------------------
CREATE TABLE `sale_details` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id`     INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED NOT NULL,
  `quantity`    INT NOT NULL,
  `unit_price`  DECIMAL(10,2) NOT NULL COMMENT 'Precio al momento de la venta',
  `discount`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `subtotal`    DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_detail_sale`    FOREIGN KEY (`sale_id`)    REFERENCES `sales`    (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_sale`    (`sale_id`),
  INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabla: inventory_movements (Movimientos de Inventario)
-- ------------------------------------------------------------
CREATE TABLE `inventory_movements` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`   INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED NOT NULL,
  `type`         ENUM('entrada','salida','ajuste') NOT NULL,
  `quantity`     INT NOT NULL COMMENT 'Positivo=entrada, Negativo=salida',
  `stock_before` INT NOT NULL,
  `stock_after`  INT NOT NULL,
  `reference`    VARCHAR(100) COMMENT 'Nro. factura, orden compra, etc.',
  `notes`        TEXT,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_inv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_product`    (`product_id`),
  INDEX `idx_type`       (`type`),
  INDEX `idx_created`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
