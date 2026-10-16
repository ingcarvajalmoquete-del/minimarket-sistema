<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="space-y-6">
    <!-- Filter Bar & Summary -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <form action="/sales" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Desde</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Hasta</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Estado</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Todos los estados</option>
                    <option value="completada" <?= $status === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="anulada" <?= $status === 'anulada' ? 'selected' : '' ?>>Anulada</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white py-2 rounded-xl text-sm font-semibold transition">
                    Filtrar
                </button>
                <a href="/pos" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center">
                    <i class="fa-solid fa-plus mr-1"></i> POS
                </a>
            </div>
        </form>

        <!-- Totals summary badge -->
        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-sm">
            <div class="text-slate-500">
                Mostrando ventas completadas: <b class="text-slate-700"><?= (int)($summary['count'] ?? 0) ?> facturas</b>
            </div>
            <div class="text-base font-bold text-slate-800">
                Total del Período: <span class="text-emerald-600">$<?= number_format((float)($summary['total'] ?? 0), 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-6">Factura</th>
                        <th class="py-3 px-6">Fecha</th>
                        <th class="py-3 px-6">Cliente</th>
                        <th class="py-3 px-6">Cajero</th>
                        <th class="py-3 px-6">Método</th>
                        <th class="py-3 px-6 text-right">Total</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($sales)): ?>
                        <tr><td colspan="8" class="py-8 text-center text-slate-400">No se encontraron ventas para este período</td></tr>
                    <?php else: ?>
                        <?php foreach ($sales as $s): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-6 font-mono font-bold text-slate-800">
                                    <a href="/sales/<?= $s['id'] ?>" class="text-emerald-600 hover:underline"><?= htmlspecialchars($s['invoice_number']) ?></a>
                                </td>
                                <td class="py-3 px-6 text-slate-500 text-xs"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                                <td class="py-3 px-6 text-slate-700"><?= htmlspecialchars($s['customer_name'] ?? 'Cliente General') ?></td>
                                <td class="py-3 px-6 text-slate-500 text-xs"><?= htmlspecialchars($s['cashier']) ?></td>
                                <td class="py-3 px-6 capitalize text-xs text-slate-600"><?= htmlspecialchars($s['payment_method']) ?></td>
                                <td class="py-3 px-6 text-right font-bold text-slate-800">$<?= number_format((float)$s['total'], 2) ?></td>
                                <td class="py-3 px-6 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $s['status'] === 'completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' ?>">
                                        <?= ucfirst($s['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <a href="/sales/<?= $s['id'] ?>" class="text-slate-400 hover:text-emerald-600 mr-2" title="Ver Factura">
                                        <i class="fa-solid fa-receipt"></i>
                                    </a>
                                    <?php if ($s['status'] === 'completada' && Auth::isAdmin()): ?>
                                        <form action="/sales/<?= $s['id'] ?>/cancel" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas anular esta venta y devolver el stock?')">
                                            <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                            <button type="submit" class="text-slate-400 hover:text-rose-600 text-xs" title="Anular Factura">
                                                <i class="fa-solid fa-ban"></i>
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

        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div>Página <?= $page ?> de <?= $totalPages ?></div>
                <div class="space-x-1">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&status=<?= $status ?>"
                           class="px-3 py-1.5 rounded-lg border <?= $i == $page ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
