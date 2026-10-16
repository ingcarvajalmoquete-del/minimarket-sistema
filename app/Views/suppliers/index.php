<?php
use App\Core\Auth;
use App\Core\Session;
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-800">Proveedores del Minimarket</h2>
        <?php if (Auth::isAdmin()): ?>
            <a href="/suppliers/create" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Nuevo Proveedor</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($suppliers)): ?>
            <div class="col-span-full py-12 text-center text-slate-400">No hay proveedores registrados.</div>
        <?php else: ?>
            <?php foreach ($suppliers as $s): ?>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-mono text-slate-400"><?= htmlspecialchars($s['rnc'] ?? 'Sin RNC') ?></span>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">
                                <?= (int)$s['product_count'] ?> productos
                            </span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base"><?= htmlspecialchars($s['name']) ?></h3>
                        <p class="text-xs text-slate-500 mt-1"><i class="fa-solid fa-user-tie mr-1 text-slate-400"></i> <?= htmlspecialchars($s['contact_name'] ?? 'Contacto no asignado') ?></p>
                        
                        <div class="mt-4 space-y-1.5 text-xs text-slate-600">
                            <?php if ($s['phone']): ?>
                                <div><i class="fa-solid fa-phone w-4 text-slate-400"></i> <?= htmlspecialchars($s['phone']) ?></div>
                            <?php endif; ?>
                            <?php if ($s['email']): ?>
                                <div><i class="fa-solid fa-envelope w-4 text-slate-400"></i> <?= htmlspecialchars($s['email']) ?></div>
                            <?php endif; ?>
                            <?php if ($s['address']): ?>
                                <div><i class="fa-solid fa-location-dot w-4 text-slate-400"></i> <?= htmlspecialchars($s['address']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (Auth::isAdmin()): ?>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                            <a href="/suppliers/<?= $s['id'] ?>/edit" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Editar</a>
                            <span class="text-slate-300">|</span>
                            <form action="/suppliers/<?= $s['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('¿Eliminar proveedor?')">
                                <input type="hidden" name="_csrf" value="<?= Session::csrf() ?>">
                                <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">Eliminar</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
