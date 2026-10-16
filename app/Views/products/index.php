<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="space-y-6">
    <!-- Header / Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form action="/products" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                       placeholder="Buscar por código o nombre..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <select name="category" onchange="this.form.submit()"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todas las Categorías</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($category ?? 0) == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-slate-700 transition">
                Filtrar
            </button>
            <?php if (!empty($search) || !empty($category)): ?>
                <a href="/products" class="text-xs text-slate-500 hover:text-rose-500 underline">Limpiar filtros</a>
            <?php endif; ?>
        </form>

        <?php if (Auth::isAdmin() || Auth::role() === 'almacen'): ?>
            <a href="/products/create" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Nuevo Producto</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Products Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-6">Código</th>
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6">Categoría</th>
                        <th class="py-3 px-6 text-right">Costo</th>
                        <th class="py-3 px-6 text-right">Precio Venta</th>
                        <th class="py-3 px-6 text-center">Stock</th>
                        <th class="py-3 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No se encontraron productos registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-6 font-mono text-xs text-slate-500"><?= htmlspecialchars($p['code'] ?? 'S/C') ?></td>
                                <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="py-3 px-6 text-slate-500"><?= htmlspecialchars($p['category_name']) ?></td>
                                <td class="py-3 px-6 text-right text-slate-500">$<?= number_format((float)$p['cost_price'], 2) ?></td>
                                <td class="py-3 px-6 text-right font-bold text-emerald-600">$<?= number_format((float)$p['sale_price'], 2) ?></td>
                                <td class="py-3 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold <?= $p['stock'] <= $p['min_stock'] ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= (int)$p['stock'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right space-x-2">
                                    <a href="/products/<?= $p['id'] ?>" class="text-slate-400 hover:text-slate-600 text-sm" title="Detalles">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <?php if (Auth::isAdmin() || Auth::role() === 'almacen'): ?>
                                        <a href="/products/<?= $p['id'] ?>/edit" class="text-blue-500 hover:text-blue-700 text-sm" title="Editar">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (Auth::isAdmin()): ?>
                                        <form action="/products/<?= $p['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                            <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                            <button type="submit" class="text-rose-400 hover:text-rose-600 text-sm" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div>Página <?= $page ?> de <?= $totalPages ?> (Total: <?= $total ?> productos)</div>
                <div class="space-x-1">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category ?>"
                           class="px-3 py-1.5 rounded-lg border <?= $i == $page ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
