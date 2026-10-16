<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form action="/inventory" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Buscar por producto o referencia..."
                   class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">

            <select name="type" onchange="this.form.submit()"
                    class="py-2 px-3 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todos los movimientos</option>
                <option value="entrada" <?= ($type ?? '') === 'entrada' ? 'selected' : '' ?>>Entradas</option>
                <option value="salida" <?= ($type ?? '') === 'salida' ? 'selected' : '' ?>>Salidas</option>
                <option value="ajuste" <?= ($type ?? '') === 'ajuste' ? 'selected' : '' ?>>Ajustes</option>
            </select>

            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filtrar</button>
        </form>

        <a href="/inventory/adjust" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Nuevo Ajuste / Entrada</span>
        </a>
    </div>

    <!-- Inventory Movements Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-6">Fecha</th>
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6">Tipo</th>
                        <th class="py-3 px-6 text-center">Cantidad</th>
                        <th class="py-3 px-6 text-center">Stock Antes</th>
                        <th class="py-3 px-6 text-center">Stock Después</th>
                        <th class="py-3 px-6">Referencia</th>
                        <th class="py-3 px-6">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($movements)): ?>
                        <tr><td colspan="8" class="py-8 text-center text-slate-400">No hay movimientos registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($movements as $m): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-6 text-slate-500 text-xs"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                                <td class="py-3 px-6 font-semibold text-slate-800">
                                    <?= htmlspecialchars($m['product_name']) ?>
                                    <span class="block text-xs font-mono text-slate-400"><?= htmlspecialchars($m['product_code'] ?? '') ?></span>
                                </td>
                                <td class="py-3 px-6">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold <?= $m['type'] === 'entrada' ? 'bg-emerald-100 text-emerald-700' : ($m['type'] === 'salida' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') ?>">
                                        <?= ucfirst($m['type']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center font-bold <?= $m['type'] === 'entrada' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                    <?= $m['type'] === 'entrada' ? '+' : '-' ?><?= abs($m['quantity']) ?>
                                </td>
                                <td class="py-3 px-6 text-center text-slate-500"><?= $m['stock_before'] ?></td>
                                <td class="py-3 px-6 text-center font-semibold text-slate-800"><?= $m['stock_after'] ?></td>
                                <td class="py-3 px-6 text-xs text-slate-600">
                                    <?= htmlspecialchars($m['reference'] ?? '-') ?>
                                    <?php if (!empty($m['notes'])): ?>
                                        <span class="block text-slate-400"><?= htmlspecialchars($m['notes']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-6 text-xs text-slate-500"><?= htmlspecialchars($m['user_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div>Página <?= $page ?> de <?= $totalPages ?> (Total: <?= $total ?>)</div>
                <div class="space-x-1">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&type=<?= $type ?>&search=<?= urlencode($search) ?>"
                           class="px-3 py-1.5 rounded-lg border <?= $i == $page ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
