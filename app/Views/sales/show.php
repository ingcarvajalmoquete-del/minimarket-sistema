<div class="max-w-xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <a href="/sales" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a ventas
        </a>
        <button onclick="window.print()" class="bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-700 transition">
            <i class="fa-solid fa-print mr-1"></i> Imprimir Ticket
        </button>
    </div>

    <!-- Ticket / Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 font-sans">
        <!-- Receipt Header -->
        <div class="text-center pb-6 border-b border-dashed border-slate-200">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-500 text-white text-xl mb-2">
                <i class="fa-solid fa-shop"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-800">MINI MARKET PRO</h2>
            <p class="text-xs text-slate-400">RNC: 1-01-99999-9 | Tel: (809) 555-0199</p>
            <p class="text-xs text-slate-400">Av. Principal #123, Santo Domingo</p>

            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 flex justify-between">
                <span>Factura: <b><?= htmlspecialchars($sale['invoice_number']) ?></b></span>
                <span><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></span>
            </div>
            <div class="text-xs text-slate-500 flex justify-between mt-1">
                <span>Cajero: <?= htmlspecialchars($sale['cashier']) ?></span>
                <span>Cliente: <?= htmlspecialchars($sale['customer_name'] ?? 'Consumidor Final') ?></span>
            </div>
        </div>

        <!-- Receipt Items -->
        <div class="py-4 border-b border-dashed border-slate-200">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase">
                        <th class="text-left pb-2">Cant</th>
                        <th class="text-left pb-2">Descripción</th>
                        <th class="text-right pb-2">Precio</th>
                        <th class="text-right pb-2">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($details as $d): ?>
                        <tr>
                            <td class="py-2 text-slate-600 font-bold"><?= (int)$d['quantity'] ?></td>
                            <td class="py-2 text-slate-800"><?= htmlspecialchars($d['product_name']) ?></td>
                            <td class="py-2 text-right text-slate-500">$<?= number_format((float)$d['unit_price'], 2) ?></td>
                            <td class="py-2 text-right font-semibold text-slate-800">$<?= number_format((float)$d['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Receipt Totals -->
        <div class="py-4 space-y-1.5 text-xs text-slate-600 border-b border-dashed border-slate-200">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>$<?= number_format((float)$sale['subtotal'], 2) ?></span>
            </div>
            <div class="flex justify-between">
                <span>ITBIS (18%)</span>
                <span>$<?= number_format((float)$sale['tax'], 2) ?></span>
            </div>
            <?php if ($sale['discount'] > 0): ?>
                <div class="flex justify-between text-rose-600">
                    <span>Descuento</span>
                    <span>-$<?= number_format((float)$sale['discount'], 2) ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between text-base font-bold text-slate-800 pt-2 border-t border-slate-100">
                <span>TOTAL RD$</span>
                <span class="text-emerald-600">$<?= number_format((float)$sale['total'], 2) ?></span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="pt-4 text-xs text-slate-500 space-y-1">
            <div class="flex justify-between">
                <span>Método de pago:</span>
                <span class="capitalize font-semibold text-slate-700"><?= htmlspecialchars($sale['payment_method']) ?></span>
            </div>
            <div class="flex justify-between">
                <span>Monto pagado:</span>
                <span>$<?= number_format((float)$sale['amount_paid'], 2) ?></span>
            </div>
            <div class="flex justify-between">
                <span>Cambio devuelto:</span>
                <span class="font-bold text-blue-600">$<?= number_format((float)$sale['change_amount'], 2) ?></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
            <p>¡Gracias por su compra en MiniMarket!</p>
            <p class="mt-0.5">Conserve este comprobante para cualquier reclamación</p>
        </div>
    </div>
</div>
