<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/equipment" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($equipment['equipment_number'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($equipment['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <?= eqShowStatusBadge($equipment['status'] ?? '') ?>
        </div>
        <div class="flex gap-2">
            <a href="/equipment/<?= (int)$equipment['id'] ?>/edit" class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Redigera</a>
            <form method="POST" action="/equipment/<?= (int)$equipment['id'] ?>/delete" class="inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna utrustning?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-3 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Info card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Information</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-3 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kategori:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars(eqShowCategoryLabel($equipment['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Avdelning:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Kritikalitet:</span>
                <span class="ml-1"><?= eqShowCriticalityBadge($equipment['criticality'] ?? '') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Plats:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Byggnad:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['building'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Våning:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['floor'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tillverkare:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['manufacturer'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Modell:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['model'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Serienummer:</span>
                <span class="text-gray-900 dark:text-white ml-1 font-mono text-xs"><?= htmlspecialchars($equipment['serial_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tillverkningsår:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['year_of_manufacture'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Installerad:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['installed_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Garanti till:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= htmlspecialchars($equipment['warranty_until'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Aktiv:</span>
                <span class="text-gray-900 dark:text-white ml-1"><?= !empty($equipment['is_active']) ? 'Ja' : 'Nej' ?></span>
            </div>
        </div>
        <?php if (!empty($equipment['description'])): ?>
        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="font-medium text-gray-500 dark:text-gray-400 mb-1">Beskrivning</p>
            <?= nl2br(htmlspecialchars($equipment['description'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($equipment['notes'])): ?>
        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="font-medium text-gray-500 dark:text-gray-400 mb-1">Anteckningar</p>
            <?= nl2br(htmlspecialchars($equipment['notes'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Linked machines -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kopplade maskiner</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nästa underhåll</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($machines as $m): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3"><a href="/machines/<?= (int)$m['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($m['machine_number'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= machineStatusBadge($m['status'] ?? '') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($m['next_maintenance_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($machines)): ?>
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Inga maskiner kopplade till denna utrustning</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function eqShowStatusBadge(string $s): string {
    $m = [
        'operational'    => ['Driftsatt',  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'maintenance'    => ['Underhåll',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'out_of_service' => ['Ur drift',   'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned' => ['Avvecklad',  'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$s][0] ?? $s, ENT_QUOTES, 'UTF-8').'</span>';
}
function eqShowCriticalityBadge(string $c): string {
    $m = [
        'low'      => ['Låg',     'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
        'medium'   => ['Medel',   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
        'high'     => ['Hög',     'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'],
        'critical' => ['Kritisk', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$c][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$c][0] ?? $c, ENT_QUOTES, 'UTF-8').'</span>';
}
function eqShowCategoryLabel(string $c): string {
    $m = ['production' => 'Produktion', 'facility' => 'Fastighet', 'utility' => 'Försörjning', 'safety' => 'Säkerhet', 'transport' => 'Transport', 'it' => 'IT', 'other' => 'Övrigt'];
    return $m[$c] ?? $c;
}
function machineStatusBadge(string $s): string {
    $m = [
        'running'        => ['I drift',    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'idle'           => ['Stationär',  'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'maintenance'    => ['Underhåll',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'breakdown'      => ['Driftstopp', 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned' => ['Avvecklad',  'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-1 rounded-full text-xs font-medium '.($m[$s][1] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300').'">'.htmlspecialchars($m[$s][0] ?? $s, ENT_QUOTES, 'UTF-8').'</span>';
}
?>
