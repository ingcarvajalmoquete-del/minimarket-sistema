<div class="space-y-6">
    <!-- Valuation Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Total Referencias</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= (int)($summary['total_products'] ?? 0) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Unidades Físicas</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= number_format((int)($summary['total_units'] ?? 0)) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Valor al Costo</div>
            <div class="text-2xl font-bold text-slate-700 mt-1">$<?= number_format((float)($summary['total_cost_value'] ?? 0), 2) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Valor Estimado Venta</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">$<?= number_format((float)($summary['total_sale_value'] ?? 0), 2) ?></div>
        </div>
    </div>

    <!-- Inventory Valuation Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Valoración Detallada por Producto</h3>
            <span class="text-xs text-rose-500 font-semibold"><?= (int)($summary['low_stock_count'] ?? 0) ?> con stock crítico</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6">Categoría</th>
                        <th class="py-3 px-6 text-center">Stock Actual</th>
                        <th class="py-3 px-6 text-right">Costo Unit.</th>
                        <th class="py-3 px-6 text-right">Valor Costo Total</th>
                        <th class="py-3 px-6 text-right">Precio Venta</th>
                        <th class="py-3 px-6 text-right">Valor Venta Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="py-3 px-6 text-slate-500 text-xs"><?= htmlspecialchars($p['category_name']) ?></td>
                            <td class="py-3 px-6 text-center font-bold <?= $p['stock'] <= $p['min_stock'] ? 'text-rose-600' : 'text-slate-700' ?>">
                                <?= (int)$p['stock'] ?>
                            </td>
                            <td class="py-3 px-6 text-right text-slate-500">$<?= number_format((float)$p['cost_price'], 2) ?></td>
                            <td class="py-3 px-6 text-right font-medium text-slate-700">$<?= number_format((float)$p['stock_value'], 2) ?></td>
                            <td class="py-3 px-6 text-right text-slate-500">$<?= number_format((float)$p['sale_price'], 2) ?></td>
                            <td class="py-3 px-6 text-right font-bold text-emerald-600">$<?= number_format((float)$p['sale_value'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
