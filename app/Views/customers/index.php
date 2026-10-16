<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="space-y-6" x-data="{ openModal: false, editMode: false, currentCustomer: {} }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search -->
        <form action="/customers" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                       placeholder="Buscar por nombre, cédula o teléfono..."
                       class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </form>

        <button @click="openModal = true; editMode = false; currentCustomer = { name: '', phone: '', email: '', cedula: '', address: '' }"
                class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center space-x-2">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Nuevo Cliente</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-6">Cliente</th>
                        <th class="py-3 px-6">Cédula / RNC</th>
                        <th class="py-3 px-6">Contacto</th>
                        <th class="py-3 px-6 text-center">Compras</th>
                        <th class="py-3 px-6 text-right">Total Consumido</th>
                        <th class="py-3 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="6" class="py-8 text-center text-slate-400">No se encontraron clientes</td></tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($c['name']) ?></td>
                                <td class="py-3 px-6 font-mono text-xs text-slate-500"><?= htmlspecialchars($c['cedula'] ?? '-') ?></td>
                                <td class="py-3 px-6 text-xs text-slate-600">
                                    <div><?= htmlspecialchars($c['phone'] ?? '') ?></div>
                                    <div class="text-slate-400"><?= htmlspecialchars($c['email'] ?? '') ?></div>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        <?= (int)$c['total_purchases'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right font-bold text-emerald-600">
                                    $<?= number_format((float)$c['total_spent'], 2) ?>
                                </td>
                                <td class="py-3 px-6 text-right space-x-2">
                                    <button @click="openModal = true; editMode = true; currentCustomer = <?= htmlspecialchars(json_encode($c)) ?>"
                                            class="text-blue-500 hover:text-blue-700 text-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <?php if (Auth::isAdmin()): ?>
                                        <form action="/customers/<?= $c['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('¿Eliminar cliente?')">
                                            <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                            <button type="submit" class="text-rose-400 hover:text-rose-600 text-sm">
                                                <i class="fa-solid fa-trash"></i>
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
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="openModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-800 mb-4" x-text="editMode ? 'Editar Cliente' : 'Registrar Cliente'"></h3>

            <form :action="editMode ? '/customers/' + currentCustomer.id + '/update' : '/customers'" method="POST" class="space-y-4">
                <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nombre Completo *</label>
                    <input type="text" name="name" required x-model="currentCustomer.name"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Cédula / RNC</label>
                        <input type="text" name="cedula" x-model="currentCustomer.cedula"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Teléfono</label>
                        <input type="text" name="phone" x-model="currentCustomer.phone"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Correo</label>
                    <input type="email" name="email" x-model="currentCustomer.email"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Dirección</label>
                    <input type="text" name="address" x-model="currentCustomer.address"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-500">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
