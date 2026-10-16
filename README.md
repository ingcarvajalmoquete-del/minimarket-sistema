# 🛒 MiniMarket Pro - Sistema de Gestión Comercial

Sistema web integral de punto de venta (POS), facturación, inventario y gestión para mini markets y bodegas. Desarrollado en **PHP 8.2 (MVC Puro, sin frameworks pesados)** y **MySQL 8.0**, con interfaz moderna responsiva en **Tailwind CSS + Alpine.js**.

Listo para ejecutar localmente mediante **MySQL Workbench / XAMPP / Docker** y desplegar inmediatamente a **Railway** y **GitHub**.

---

## 🌟 Características Principales

- **📊 Dashboard Interactivo:** Métricas en tiempo real (Ventas del día, transacciones del mes, stock crítico, gráfico de ventas de los últimos 7 días con Chart.js).
- **💳 Punto de Venta (POS):** Carrito de compras reactivo, búsqueda instantánea por nombre o código de barra, cálculo automático de ITBIS (18%), cobro en efectivo con cálculo de cambio en vivo y comprobante/ticket imprimible.
- **📦 Gestión de Productos (CRUD):** Control de precios de costo, precios de venta, stock mínimo y catálogo activo con paginación y búsqueda.
- **📈 Kardex e Inventario:** Historial detallado de movimientos (entradas, salidas por mermas y ajustes por conteo físico).
- **👥 Módulos de Soporte:** Gestión de Categorías, Proveedores (con RNC), Clientes y Usuarios con control de accesos (`admin`, `cajero`, `almacen`).
- **📋 Reportes Financieros:** Facturación por fechas, productos más vendidos y valoración de inventario (costo vs. ganancia estimada).

---

## 🚀 Despliegue en Railway (Paso a Paso)

1. **Subir a GitHub:**
   Crea un nuevo repositorio en GitHub y sube esta carpeta:
   ```bash
   git init
   git add .
   git commit -m "Initial commit - MiniMarket Pro"
   git branch -M main
   git remote add origin https://github.com/TU_USUARIO/TU_REPOSITORIO.git
   git push -u origin main
   ```

2. **Crear Proyecto en Railway:**
   - Ve a [Railway.app](https://railway.app/) y crea un nuevo proyecto (**New Project**).
   - Selecciona **Deploy from GitHub repo** y elige tu repositorio.
   - En el mismo proyecto, haz clic en **+ New** y añade un servicio de **Database -> MySQL**.

3. **Cargar la Base de Datos:**
   - Abre la pestaña del servicio MySQL en Railway, ve a **Connect** y copia las credenciales de conexión o conéctate con **MySQL Workbench**.
   - Ejecuta primero el archivo `database/schema.sql` y luego `database/seed.sql`.

4. **Variables de Entorno:**
   - Railway conectará automáticamente la base de datos a tu servicio web mediante la variable `MYSQL_URL` o `MYSQLHOST` (la aplicación ya está programada para detectarlas automáticamente).
   - ¡Listo! Abre el dominio público generado por Railway.

---

## 💻 Instalación y Uso Local

### Opción A: Con MySQL Workbench / XAMPP
1. Abre **MySQL Workbench** o **phpMyAdmin**.
2. Abre y ejecuta el archivo [`database/schema.sql`](database/schema.sql).
3. Abre y ejecuta el archivo [`database/seed.sql`](database/seed.sql).
4. Duplica el archivo `.env.example`, renómbralo a `.env` y coloca las credenciales de tu MySQL local:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=minimarket_db
   DB_USER=root
   DB_PASSWORD=tu_clave
   ```
5. Inicia el servidor integrado de PHP apuntando a la carpeta `public`:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Abre en tu navegador: [http://localhost:8000](http://localhost:8000)

---

### Opción B: Con Docker Compose (1 solo comando)
Si tienes Docker Desktop instalado:
```bash
docker-compose up -d --build
```
Entra a [http://localhost:8080](http://localhost:8080). La base de datos y los datos iniciales se cargarán automáticamente.

---

## 🔑 Credenciales de Acceso por Defecto

| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@minimarket.com` | `password` |
| **Cajero** | `cajero@minimarket.com` | `password` |
| **Almacén** | `almacen@minimarket.com` | `password` |

---

## 🗄️ Estructura del Código

```
minimarket/
├── app/
│   ├── Core/           # Motor MVC, PDO Database Singleton, Router, Auth, Session
│   ├── Controllers/    # Lógica de negocio (Auth, POS, Productos, Reportes, etc.)
│   └── Views/          # Vistas Blade-like limpias con Tailwind y Alpine.js
├── database/
│   ├── schema.sql      # Estructura DDL para MySQL Workbench
│   └── seed.sql        # Datos de prueba para arrancar de inmediato
├── public/             # Raíz web pública (index.php, .htaccess)
├── Dockerfile          # Imagen Docker para Railway y producción
├── railway.json        # Configuración nativa de Railway
└── docker-compose.yml  # Entorno local reproducible
```
