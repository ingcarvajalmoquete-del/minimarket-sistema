-- ============================================================
-- MINI MARKET - SEED DATA (Datos iniciales)
-- ============================================================

USE `minimarket_db`;

-- Usuarios (password: Admin123! hasheado con bcrypt)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrador', 'admin@minimarket.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Juan Cajero',   'cajero@minimarket.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cajero'),
('Maria Almacen', 'almacen@minimarket.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'almacen');

-- Categorías
INSERT INTO `categories` (`name`, `description`) VALUES
('Bebidas',             'Refrescos, jugos, agua y bebidas alcohólicas'),
('Lácteos',             'Leche, queso, yogurt y derivados'),
('Carnes y Embutidos',  'Carnes frescas, salchichas, jamón y similares'),
('Panadería',           'Pan, galletas, bizcochos y repostería'),
('Abarrotes',           'Granos, enlatados, condimentos y artículos de cocina'),
('Frutas y Verduras',   'Productos frescos de temporada'),
('Limpieza del Hogar',  'Detergentes, jabones y productos de limpieza'),
('Cuidado Personal',    'Shampoo, jabón corporal, desodorantes, etc.'),
('Snacks y Dulces',     'Papas fritas, chicles, chocolates y dulcería'),
('Congelados',          'Helados, productos congelados y precocinados');

-- Proveedores
INSERT INTO `suppliers` (`name`, `contact_name`, `phone`, `email`, `rnc`) VALUES
('Distribuidora El Sol S.A.',    'Pedro Martínez',   '809-555-0101', 'pedrom@elsol.com.do',    '1-01-12345-6'),
('Lácteos del Norte S.R.L.',     'Carmen Vásquez',   '809-555-0202', 'cvasquez@lacteosn.com',  '1-01-23456-7'),
('Alimentos Frescos C. por A.',  'Roberto Jiménez',  '809-555-0303', 'rjimenez@alfrescos.com', '1-01-34567-8'),
('Importadora Global S.A.',      'Sofía Guerrero',   '809-555-0404', 'sguerrero@imglobal.com', '1-01-45678-9'),
('Bebidas y Más S.R.L.',         'Luis Torres',      '809-555-0505', 'ltorres@bebidasmas.com', '1-01-56789-0');

-- Clientes
INSERT INTO `customers` (`name`, `phone`, `cedula`) VALUES
('Cliente General',    NULL,           NULL),
('Ana García',         '829-555-0001', '001-1234567-8'),
('Carlos Domínguez',   '829-555-0002', '001-2345678-9'),
('Lucía Peña',         '829-555-0003', '001-3456789-0'),
('Miguel Rodríguez',   '829-555-0004', '001-4567890-1');

-- Productos
INSERT INTO `products` (`category_id`, `supplier_id`, `code`, `name`, `unit`, `cost_price`, `sale_price`, `stock`, `min_stock`) VALUES
-- Bebidas
(1, 5, 'BEB-001', 'Agua Mineral 500ml',        'unidad', 12.00,  20.00,  80,  20),
(1, 5, 'BEB-002', 'Refresco Cola 355ml',       'unidad', 25.00,  40.00,  60,  15),
(1, 5, 'BEB-003', 'Jugo de Naranja 1L',        'unidad', 55.00,  85.00,  40,  10),
(1, 5, 'BEB-004', 'Cerveza Lata 350ml',        'unidad', 60.00,  95.00,  48,  12),
(1, 5, 'BEB-005', 'Agua de Coco 330ml',        'unidad', 35.00,  55.00,  30,   8),
-- Lácteos
(2, 2, 'LAC-001', 'Leche Entera 1L',           'litro',  55.00,  75.00,  50,  15),
(2, 2, 'LAC-002', 'Queso Cheddar 200g',        'unidad', 85.00, 130.00,  25,   8),
(2, 2, 'LAC-003', 'Yogurt Natural 500g',       'unidad', 65.00,  95.00,  30,  10),
(2, 2, 'LAC-004', 'Mantequilla 250g',          'unidad', 95.00, 140.00,  20,   5),
-- Abarrotes
(5, 1, 'ABA-001', 'Arroz Largo 5lb',           'bolsa', 130.00, 185.00,  45,  10),
(5, 1, 'ABA-002', 'Habichuelas Negras 1lb',    'bolsa',  35.00,  55.00,  60,  15),
(5, 1, 'ABA-003', 'Aceite Vegetal 1L',         'litro',  90.00, 135.00,  35,  10),
(5, 1, 'ABA-004', 'Azúcar Blanca 1lb',         'bolsa',  30.00,  45.00,  70,  20),
(5, 1, 'ABA-005', 'Sal Refinada 500g',         'bolsa',  15.00,  25.00,  80,  20),
(5, 4, 'ABA-006', 'Sopa de Pollo 85g',         'unidad',  20.00,  35.00, 100,  25),
-- Snacks
(9, 4, 'SNK-001', 'Papas Fritas 30g',          'unidad',  18.00,  30.00,  80,  20),
(9, 4, 'SNK-002', 'Galletas Oreo 120g',        'unidad',  40.00,  65.00,  50,  15),
(9, 4, 'SNK-003', 'Chocolate Hershey 40g',     'unidad',  45.00,  70.00,  40,  10),
(9, 4, 'SNK-004', 'Chicles 5 unidades',        'paquete',  5.00,  10.00, 200,  50),
-- Limpieza
(7, 4, 'LIM-001', 'Detergente en Polvo 500g',  'bolsa',  65.00,  95.00,  40,  10),
(7, 4, 'LIM-002', 'Jabón de Lavar 125g',       'unidad',  20.00,  35.00,  60,  15),
(7, 4, 'LIM-003', 'Cloro 1 Galón',             'galon',  70.00, 110.00,  25,   8),
(7, 4, 'LIM-004', 'Suavizante de Ropa 500ml',  'unidad',  75.00, 115.00,  30,   8),
-- Panadería
(4, 3, 'PAN-001', 'Pan de Agua x6',            'bolsa',  30.00,  45.00,  20,   5),
(4, 3, 'PAN-002', 'Pan de Molde Integral',     'bolsa',  65.00,  95.00,  15,   5),
-- Cuidado Personal
(8, 4, 'CUI-001', 'Shampoo 200ml',             'unidad',  95.00, 145.00,  25,   8),
(8, 4, 'CUI-002', 'Jabón Corporal 90g',        'unidad',  25.00,  40.00,  60,  15),
(8, 4, 'CUI-003', 'Desodorante Aerosol 150ml', 'unidad', 150.00, 220.00,  20,   5);

-- Movimientos iniciales de inventario (entrada por apertura)
INSERT INTO `inventory_movements` (`product_id`, `user_id`, `type`, `quantity`, `stock_before`, `stock_after`, `reference`, `notes`) 
SELECT p.id, 1, 'entrada', p.stock, 0, p.stock, 'APERTURA-001', 'Stock inicial del sistema'
FROM `products` p;
