<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="space-y-6" x-data="{ openPassModal: false, selectedUserId: null }">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-800">Usuarios del Sistema</h2>
        <a href="/users/create" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center space-x-2">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>Nuevo Usuario</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 border-b border-slate-100 text-xs text-slate-400 uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-6">Usuario</th>
                    <th class="py-3 px-6">Correo</th>
                    <th class="py-3 px-6">Rol</th>
                    <th class="py-3 px-6 text-center">Estado</th>
                    <th class="py-3 px-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-6 font-semibold text-slate-800"><?= htmlspecialchars($u['name']) ?></td>
                        <td class="py-3 px-6 text-slate-500 text-xs"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="py-3 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : ($u['role'] === 'cajero' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $u['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' ?>">
                                <?= $u['is_active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right space-x-3 text-xs font-semibold">
                            <button @click="openPassModal = true; selectedUserId = <?= $u['id'] ?>" class="text-indigo-600 hover:text-indigo-800">
                                Clave
                            </button>
                            <?php if ($u['id'] !== Auth::id()): ?>
                                <form action="/users/<?= $u['id'] ?>/toggle" method="POST" class="inline">
                                    <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                    <button type="submit" class="<?= $u['is_active'] ? 'text-rose-500 hover:text-rose-700' : 'text-emerald-600 hover:text-emerald-800' ?>">
                                        <?= $u['is_active'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Password Reset -->
    <div x-show="openPassModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="openPassModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-800 mb-3">Cambiar Contraseña</h3>
            <form :action="'/users/' + selectedUserId + '/reset-password'" method="POST" class="space-y-4">
                <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nueva Contraseña (mín 8 car.)</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="openPassModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
