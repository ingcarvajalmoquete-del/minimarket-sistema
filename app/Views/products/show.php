<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="/products" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a productos
        </a>
        <a href="/products/<?= $product['id'] ?>/edit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
            <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
        </a>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono text-slate-400 uppercase"><?= htmlspecialchars($product['code'] ?? 'Sin código') ?></span>
                <h2 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars($product['name']) ?></h2>
                <div class="text-sm text-slate-500 mt-1">Categoría: <span class="font-medium text-slate-700"><?= htmlspecialchars($product['category_name']) ?></span></div>
            </div>
            <div class="flex items-center space-x-6">
                <div>
                    <div class="text-xs text-slate-400">Precio Venta</div>
                    <div class="text-2xl font-bold text-emerald-600">$<?= number_format((float)$product['sale_price'], 2) ?></div>
                </div>
                <div>
                    <div class="text-xs text-slate-400">Costo</div>
                    <div class="text-xl font-bold text-slate-600">$<?= number_format((float)$product['cost_price'], 2) ?></div>
                </div>
                <div>
                    <div class="text-xs text-slate-400">Stock Actual</div>
                    <div class="text-2xl font-bold <?= $product['stock'] <= $product['min_stock'] ? 'text-rose-600' : 'text-slate-800' ?>">
                        <?= (int)$product['stock'] ?> <span class="text-xs text-slate-400 font-normal"><?= htmlspecialchars($product['unit']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movements History for this product -->
        <div class="pt-6">
            <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                <i class="fa-solid fa-history mr-2 text-indigo-500"></i> Historial Reciente de Movimientos
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-slate-400 uppercase border-b border-slate-100">
                        <tr>
                            <th class="py-2.5">Fecha</th>
                            <th class="py-2.5">Tipo</th>
                            <th class="py-2.5">Cantidad</th>
                            <th class="py-2.5">Antes</th>
                            <th class="py-2.5">Después</th>
                            <th class="py-2.5">Referencia / Nota</th>
                            <th class="py-2.5">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($movements)): ?>
                            <tr><td colspan="7" class="py-6 text-center text-slate-400 text-xs">Sin movimientos registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($movements as $m): ?>
                                <tr>
                                    <td class="py-2.5 text-slate-500"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                                    <td class="py-2.5">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold <?= $m['type'] === 'entrada' ? 'bg-emerald-100 text-emerald-700' : ($m['type'] === 'salida' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') ?>">
                                            <?= ucfirst($m['type']) ?>
                                        </span>
                                    </td>
                                    <td class="py-2.5 font-bold <?= $m['type'] === 'entrada' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $m['type'] === 'entrada' ? '+' : '-' ?><?= abs($m['quantity']) ?>
                                    </td>
                                    <td class="py-2.5 text-slate-500"><?= $m['stock_before'] ?></td>
                                    <td class="py-2.5 font-semibold text-slate-700"><?= $m['stock_after'] ?></td>
                                    <td class="py-2.5 text-slate-600 text-xs"><?= htmlspecialchars($m['reference'] ?? '') ?> <?= htmlspecialchars($m['notes'] ?? '') ?></td>
                                    <td class="py-2.5 text-slate-500 text-xs"><?= htmlspecialchars($m['user_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
