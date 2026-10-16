<?php
use App\Core\Session;
$old = Session::getFlash('old', []);
?>
<div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Crear Nuevo Usuario</h2>
        <a href="/users" class="text-sm text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <form action="/users" method="POST" class="space-y-4">
        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Nombre Completo *</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                   placeholder="ej. Laura Sánchez"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Correo Electrónico *</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   placeholder="ej. laura@minimarket.com"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Contraseña (Mínimo 8 car.) *</label>
            <input type="password" name="password" required minlength="8"
                   placeholder="••••••••"
                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Rol en el Sistema *</label>
            <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="cajero">Cajero (Ventas y POS)</option>
                <option value="almacen">Almacén (Inventario y Productos)</option>
                <option value="admin">Administrador (Acceso Completo)</option>
            </select>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
            <a href="/users" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow transition">
                Crear Usuario
            </button>
        </div>
    </form>
</div>
