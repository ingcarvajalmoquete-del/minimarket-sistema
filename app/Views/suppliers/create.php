<?php
use App\Core\Session;
?>
<div class="max-w-xl mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800"><?= isset($supplier) ? 'Editar Proveedor' : 'Nuevo Proveedor' ?></h2>
        <a href="/suppliers" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <form action="<?= isset($supplier) ? '/suppliers/' . $supplier['id'] . '/update' : '/suppliers' ?>" method="POST" class="space-y-4">
        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nombre Comercial / Empresa *</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($supplier['name'] ?? '') ?>"
                   placeholder="ej. Distribuidora El Sol"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Persona de Contacto</label>
                <input type="text" name="contact_name" value="<?= htmlspecialchars($supplier['contact_name'] ?? '') ?>"
                       placeholder="ej. Juan Pérez"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">RNC / Identificación</label>
                <input type="text" name="rnc" value="<?= htmlspecialchars($supplier['rnc'] ?? '') ?>"
                       placeholder="ej. 1-01-12345-6"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Teléfono</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>"
                       placeholder="809-555-0101"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
                <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
                       placeholder="contacto@proveedor.com"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Dirección</label>
            <textarea name="address" rows="2" placeholder="Ubicación de entrega..."
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="/suppliers" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow transition">
                <?= isset($supplier) ? 'Actualizar' : 'Guardar' ?> Proveedor
            </button>
        </div>
    </form>
</div>
