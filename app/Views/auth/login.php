<?php
use App\Core\Session;

$flashError = Session::getFlash('error');
$flashSuccess = Session::getFlash('success');
$old = Session::getFlash('old', []);
?>
<div class="bg-white rounded-2xl shadow-xl overflow-hidden p-8 border border-slate-800">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-500 text-white text-2xl shadow-lg mb-3">
            <i class="fa-solid fa-shop"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800">MiniMarket Pro</h2>
        <p class="text-sm text-slate-500 mt-1">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <?php if ($flashError): ?>
        <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>
            <?= htmlspecialchars($flashError) ?>
        </div>
    <?php endif; ?>

    <?php if ($flashSuccess): ?>
        <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?= htmlspecialchars($flashSuccess) ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="space-y-5">
        <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-regular fa-envelope"></i>
                </span>
                <input type="email" name="email" required autofocus
                       value="<?= htmlspecialchars($old['email'] ?? 'admin@minimarket.com') ?>"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm"
                       placeholder="usuario@minimarket.com">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Contraseña</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password" name="password" required
                       value="password"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm"
                       placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl shadow-lg transition duration-150 flex items-center justify-center space-x-2">
            <span>Iniciar Sesión</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </form>

    <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-500 text-center">
        <p class="font-medium">Usuarios Demo por defecto:</p>
        <p class="mt-1">Admin: <span class="text-slate-700">admin@minimarket.com</span> | <span class="text-slate-700">password</span></p>
        <p>Cajero: <span class="text-slate-700">cajero@minimarket.com</span> | <span class="text-slate-700">password</span></p>
    </div>
</div>
