<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <form action="/reports/sales" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Desde</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                       class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Hasta</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                       class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                Generar Reporte
            </button>
        </form>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Transacciones</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= (int)($summary['transactions'] ?? 0) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Subtotal Neto</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">$<?= number_format((float)($summary['subtotal'] ?? 0), 2) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">ITBIS Total</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">$<?= number_format((float)($summary['tax'] ?? 0), 2) ?></div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-xs text-slate-400 uppercase font-semibold">Ventas Totales</div>
            <div class="text-2xl font-bold text-emerald-600 mt-1">$<?= number_format((float)($summary['total'] ?? 0), 2) ?></div>
        </div>
    </div>

    <!-- Products Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Productos con Mayor Facturación</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-6">Producto</th>
                        <th class="py-3 px-6">Categoría</th>
                        <th class="py-3 px-6 text-center">Unidades Vendidas</th>
                        <th class="py-3 px-6 text-right">Precio Promedio</th>
                        <th class="py-3 px-6 text-right">Facturación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($topProducts)): ?>
                        <tr><td colspan="5" class="py-6 text-center text-slate-400">Sin datos de ventas en este período</td></tr>
                    <?php else: ?>
                        <?php foreach ($topProducts as $tp): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($tp['name']) ?></td>
                                <td class="py-3 px-6 text-slate-500 text-xs"><?= htmlspecialchars($tp['category_name']) ?></td>
                                <td class="py-3 px-6 text-center font-bold text-slate-700"><?= (int)$tp['total_qty'] ?></td>
                                <td class="py-3 px-6 text-right text-slate-500">$<?= number_format((float)$tp['avg_price'], 2) ?></td>
                                <td class="py-3 px-6 text-right font-bold text-emerald-600">$<?= number_format((float)$tp['revenue'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
