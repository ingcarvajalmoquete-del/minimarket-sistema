<?php
use App\Core\Auth;
use App\Core\Session;

$user = Auth::user();
$flashSuccess = Session::getFlash('success');
$flashError = Session::getFlash('error');
$flashErrors = Session::getFlash('errors', []);
?>
<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Sistema Mini Market') ?> - MiniMarket Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full flex flex-col font-sans text-slate-800">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0">
            <div class="h-16 flex items-center px-6 bg-slate-950 font-bold text-lg text-white border-b border-slate-800">
                <i class="fa-solid fa-shop text-emerald-400 mr-3"></i> MiniMarket Pro
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="/dashboard" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/dashboard') ? 'bg-slate-800 text-white font-semibold' : '' ?>">
                    <i class="fa-solid fa-gauge w-6 text-slate-400"></i> Dashboard
                </a>
                <a href="/pos" class="flex items-center px-3 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition mt-2 mb-2 shadow">
                    <i class="fa-solid fa-cash-register w-6"></i> Punto de Venta (POS)
                </a>
                <a href="/products" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/products') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-box-open w-6 text-slate-400"></i> Productos
                </a>
                <a href="/categories" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/categories') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-tags w-6 text-slate-400"></i> Categorías
                </a>
                <a href="/sales" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/sales') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-receipt w-6 text-slate-400"></i> Ventas
                </a>
                <a href="/inventory" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/inventory') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-boxes-stacked w-6 text-slate-400"></i> Inventario
                </a>
                <a href="/customers" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/customers') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-users w-6 text-slate-400"></i> Clientes
                </a>
                <a href="/suppliers" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/suppliers') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-truck w-6 text-slate-400"></i> Proveedores
                </a>
                <?php if (Auth::isAdmin()): ?>
                <div class="pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Administración</div>
                <a href="/reports/sales" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/reports/sales') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-chart-line w-6 text-slate-400"></i> Reportes Ventas
                </a>
                <a href="/reports/inventory" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/reports/inventory') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-chart-pie w-6 text-slate-400"></i> Valor Inventario
                </a>
                <a href="/users" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-slate-800 hover:text-white transition <?= str_starts_with($_SERVER['REQUEST_URI'], '/users') ? 'bg-slate-800 text-white' : '' ?>">
                    <i class="fa-solid fa-user-shield w-6 text-slate-400"></i> Usuarios
                </a>
                <?php endif; ?>
            </nav>
            <div class="p-4 border-t border-slate-800 flex items-center justify-between text-sm">
                <div>
                    <div class="font-medium text-white"><?= htmlspecialchars($user['name'] ?? 'Usuario') ?></div>
                    <div class="text-xs text-slate-400 capitalize"><?= htmlspecialchars($user['role'] ?? '') ?></div>
                </div>
                <a href="/logout" title="Cerrar Sesión" class="text-slate-400 hover:text-rose-400 transition p-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
                <h1 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($title ?? '') ?></h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-slate-500"><i class="fa-regular fa-calendar mr-1"></i> <?= date('d/m/Y') ?></span>
                    <a href="/pos" class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                        <i class="fa-solid fa-plus mr-1"></i> Nueva Venta
                    </a>
                </div>
            </header>

            <!-- Alerts / Flashes -->
            <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
                <?php if ($flashSuccess): ?>
                    <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-3 text-lg"></i>
                        <span><?= htmlspecialchars($flashSuccess) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($flashError): ?>
                    <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 mr-3 text-lg"></i>
                        <span><?= htmlspecialchars($flashError) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flashErrors)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800">
                        <div class="font-semibold mb-1">Por favor corrige los siguientes errores:</div>
                        <ul class="list-disc pl-5 text-sm space-y-1">
                            <?php foreach ($flashErrors as $err): ?>
                                <li><?= htmlspecialchars((string)$err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
</body>
</html>
