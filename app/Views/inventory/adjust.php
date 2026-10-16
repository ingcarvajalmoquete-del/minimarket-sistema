<?php
use App\Core\Session;
?>
<div class="max-w-xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Registrar Ajuste / Entrada de Stock</h2>
        <a href="/inventory" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <form action="/inventory/adjust" method="POST" class="space-y-6">
        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Producto *</label>
            <select name="product_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Seleccione un producto</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['name']) ?> (Stock actual: <?= $p['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Tipo de Movimiento *</label>
                <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="entrada">Entrada (Compra / Devolución)</option>
                    <option value="salida">Salida (Merma / Pérdida / Dañado)</option>
                    <option value="ajuste">Ajuste Directo (Conteo Físico)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Cantidad *</label>
                <input type="number" name="quantity" min="1" required placeholder="ej. 10"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Referencia / Comprobante</label>
            <input type="text" name="reference" placeholder="ej. Factura Proveedor #4092, Conteo mensual"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Motivo / Notas</label>
            <textarea name="notes" rows="3" placeholder="Observaciones sobre este ajuste..."
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="/inventory" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow transition">
                Guardar Ajuste
            </button>
        </div>
    </form>
</div>
