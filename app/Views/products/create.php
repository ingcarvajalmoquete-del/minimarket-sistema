<?php
use App\Core\Session;
$old = Session::getFlash('old', []);
?>
<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Crear Nuevo Producto</h2>
        <a href="/products" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <form action="/products" method="POST" class="space-y-6">
        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Código de Barra / SKU</label>
                <input type="text" name="code" value="<?= htmlspecialchars($old['code'] ?? '') ?>"
                       placeholder="ej. BEB-006"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nombre del Producto *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                       placeholder="Nombre descriptivo"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Categoría *</label>
                <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="">Seleccione Categoría</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($old['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Proveedor</label>
                <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                    <option value="">Ninguno / Proveedor Local</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($old['supplier_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Unidad</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($old['unit'] ?? 'unidad') ?>"
                       placeholder="unidad, kg, litro"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Precio Costo ($)</label>
                <input type="number" step="0.01" name="cost_price" value="<?= htmlspecialchars($old['cost_price'] ?? '0.00') ?>"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Precio Venta ($) *</label>
                <input type="number" step="0.01" name="sale_price" required value="<?= htmlspecialchars($old['sale_price'] ?? '0.00') ?>"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Stock Inicial</label>
                <input type="number" name="stock" value="<?= htmlspecialchars($old['stock'] ?? '0') ?>"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Stock Mínimo de Alerta</label>
            <input type="number" name="min_stock" value="<?= htmlspecialchars($old['min_stock'] ?? '5') ?>"
                   class="w-full max-w-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Descripción</label>
            <textarea name="description" rows="3"
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white"
                      placeholder="Detalles adicionales del producto..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="/products" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow transition">
                Guardar Producto
            </button>
        </div>
    </form>
</div>
