<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Category Form -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 h-fit">
        <h3 class="text-base font-bold text-slate-800 mb-4">Nueva Categoría</h3>
        <form action="/categories" method="POST" class="space-y-4">
            <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Nombre *</label>
                <input type="text" name="name" required placeholder="ej. Frutas y Vegetales"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Descripción</label>
                <textarea name="description" rows="3" placeholder="Breve detalle..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow transition">
                Agregar Categoría
            </button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="md:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Categorías Registradas</h3>
            <span class="text-xs text-slate-400"><?= count($categories) ?> en total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-6">Nombre</th>
                        <th class="py-3 px-6">Descripción</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($categories as $c): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($c['name']) ?></td>
                            <td class="py-3 px-6 text-slate-500 text-xs"><?= htmlspecialchars($c['description'] ?? '-') ?></td>
                            <td class="py-3 px-6 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    <?= (int)$c['product_count'] ?>
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <?php if (Auth::isAdmin()): ?>
                                    <form action="/categories/<?= $c['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                        <button type="submit" class="text-rose-400 hover:text-rose-600 text-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
