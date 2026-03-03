<?php
function inspResultBadgeShow(string $r): string {
    $m = [
        'passed'      => ['Godkänd','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'failed'      => ['Underkänd','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'conditional' => ['Villkorlig','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$r][1]??'bg-gray-100 text-gray-700').'">'.($m[$r][0]??htmlspecialchars($r, ENT_QUOTES, 'UTF-8')).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/maintenance/inspections" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if (!empty($inspection['last_inspection_result'])): ?>
            <?= inspResultBadgeShow($inspection['last_inspection_result']) ?>
            <?php endif; ?>
        </div>
        <div class="flex gap-2">
            <a href="/maintenance/inspections/<?= $inspection['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500 dark:text-gray-400">Typ:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['type'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Serienummer:</span> <span class="ml-1 font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['serial_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Tillverkare:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['manufacturer'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Plats:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Besiktningsorgan:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['certification_body'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Intervall:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['inspection_interval_months'], ENT_QUOTES, 'UTF-8') ?> månader</span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Max last:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= $inspection['max_load_kg'] ? htmlspecialchars($inspection['max_load_kg'], ENT_QUOTES, 'UTF-8') . ' kg' : '—' ?></span></div>
            <div><span class="text-gray-500 dark:text-gray-400">Senaste besiktning:</span> <span class="ml-1 text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['last_inspection_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Nästa besiktning:</span>
                <?php if (!empty($inspection['next_inspection_date'])): ?>
                    <?php $overdue = strtotime($inspection['next_inspection_date']) < time(); ?>
                    <span class="ml-1 <?= $overdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white' ?>"><?= htmlspecialchars($inspection['next_inspection_date'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php else: ?>
                    <span class="ml-1 text-gray-900 dark:text-white">—</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($inspection['certificate_number'])): ?>
        <div class="mt-3 text-sm"><span class="text-gray-500 dark:text-gray-400">Certifikatnummer:</span> <span class="ml-1 font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($inspection['certificate_number'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php endif; ?>
    </div>

    <!-- Record new inspection -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Registrera besiktning</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="/maintenance/inspections/<?= $inspection['id'] ?>/record" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsdatum *</label>
                    <input type="date" name="inspection_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Resultat</label>
                    <select name="result" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="passed">Godkänd</option>
                        <option value="failed">Underkänd</option>
                        <option value="conditional">Villkorlig</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsman</label>
                    <input type="text" name="inspector" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besiktningsföretag</label>
                    <input type="text" name="inspection_company" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Certifikatnummer</label>
                    <input type="text" name="certificate_number" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giltig till</label>
                    <input type="date" name="valid_until" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Registrera besiktning</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inspection history -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Besiktningshistorik</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Datum</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Resultat</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Besiktningsman</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Företag</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Giltig till</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Registrerad av</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($inspections as $i): ?>
                    <tr>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($i['inspection_date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= inspResultBadgeShow($i['result']) ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($i['inspector'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($i['inspection_company'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($i['valid_until'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($i['recorded_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($inspections)): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Inga besiktningar registrerade</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
