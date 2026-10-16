<!-- KPI Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ventas Hoy</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">$<?= number_format((float)($todaySales['total'] ?? 0), 2) ?></div>
            <div class="text-xs text-emerald-600 mt-1 font-medium"><i class="fa-solid fa-arrow-trend-up mr-1"></i><?= (int)($todaySales['count'] ?? 0) ?> ventas hoy</div>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-dollar-sign"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ventas del Mes</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">$<?= number_format((float)($monthSales['total'] ?? 0), 2) ?></div>
            <div class="text-xs text-slate-500 mt-1 font-medium"><?= (int)($monthSales['count'] ?? 0) ?> transacciones</div>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Productos</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= (int)($totalProducts['count'] ?? 0) ?></div>
            <div class="text-xs text-slate-500 mt-1 font-medium">En catálogo activo</div>
        </div>
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Stock Bajo</div>
            <div class="text-2xl font-bold text-rose-600 mt-1"><?= (int)($lowStockCount['count'] ?? 0) ?></div>
            <div class="text-xs text-rose-500 mt-1 font-medium">Requiere reabastecer</div>
        </div>
        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
    </div>
</div>

<!-- Charts & Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart: Ventas 7 Días -->
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
            <i class="fa-solid fa-chart-area mr-2 text-emerald-500"></i> Tendencia de Ventas (Últimos 7 días)
        </h3>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top Productos -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
            <i class="fa-solid fa-trophy mr-2 text-amber-500"></i> Más Vendidos
        </h3>
        <div class="space-y-4">
            <?php if (empty($topProducts)): ?>
                <div class="text-sm text-slate-400 py-4 text-center">No hay datos de ventas aún</div>
            <?php else: ?>
                <?php foreach ($topProducts as $idx => $p): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center">
                                <?= $idx + 1 ?>
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="text-xs text-slate-400"><?= (int)$p['total_sold'] ?> unidades</div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-emerald-600">$<?= number_format((float)$p['revenue'], 2) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bottom Section: Stock Bajo y Últimas Ventas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Alertas Stock Bajo -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-bell mr-2 text-rose-500"></i> Alerta de Stock Bajo
            </h3>
            <a href="/inventory/adjust" class="text-xs text-emerald-600 font-semibold hover:underline">Reabastecer</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs text-slate-400 uppercase border-b border-slate-100 pb-2">
                        <th class="py-2">Producto</th>
                        <th class="py-2 text-center">Stock</th>
                        <th class="py-2 text-center">Mínimo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($lowStockProducts)): ?>
                        <tr><td colspan="3" class="py-4 text-center text-slate-400 text-xs">Inventario en niveles óptimos</td></tr>
                    <?php else: ?>
                        <?php foreach ($lowStockProducts as $lp): ?>
                            <tr>
                                <td class="py-2.5 font-medium text-slate-800"><?= htmlspecialchars($lp['name']) ?></td>
                                <td class="py-2.5 text-center font-bold text-rose-600"><?= (int)$lp['stock'] ?></td>
                                <td class="py-2.5 text-center text-slate-500"><?= (int)$lp['min_stock'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Últimas Ventas -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-indigo-500"></i> Últimas Ventas
            </h3>
            <a href="/sales" class="text-xs text-emerald-600 font-semibold hover:underline">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs text-slate-400 uppercase border-b border-slate-100 pb-2">
                        <th class="py-2">Factura</th>
                        <th class="py-2">Cajero</th>
                        <th class="py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($recentSales)): ?>
                        <tr><td colspan="3" class="py-4 text-center text-slate-400 text-xs">Sin transacciones recientes</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentSales as $rs): ?>
                            <tr>
                                <td class="py-2.5 font-medium text-slate-800">
                                    <a href="/sales/<?= $rs['id'] ?>" class="text-emerald-600 hover:underline"><?= htmlspecialchars($rs['invoice_number']) ?></a>
                                </td>
                                <td class="py-2.5 text-slate-600"><?= htmlspecialchars($rs['cashier']) ?></td>
                                <td class="py-2.5 text-right font-bold text-slate-800">$<?= number_format((float)$rs['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        const dataRaw = <?= json_encode($salesLast7Days ?? []) ?>;
        const labels = dataRaw.map(d => d.date);
        const totals = dataRaw.map(d => parseFloat(d.total));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Sin datos'],
                datasets: [{
                    label: 'Ventas ($)',
                    data: totals.length ? totals : [0],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
