<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Maskiner</h1>
        <a href="/machines/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny maskin
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Utrustning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avdelning</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nästa underhåll</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($machines as $m): ?>
                <?php
                    $nextMaint = $m['next_maintenance_date'] ?? null;
                    $isPastDue = $nextMaint && strtotime($nextMaint) < time();
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3"><a href="/machines/<?= (int)$m['id'] ?>" class="text-blue-600 hover:underline font-mono text-xs"><?= htmlspecialchars($m['machine_number'], ENT_QUOTES, 'UTF-8') ?></a></td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        <?php if (!empty($m['equipment_id'])): ?>
                        <a href="/equipment/<?= (int)$m['equipment_id'] ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($m['equipment_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></a>
                        <?php else: ?>
                        <span>—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($m['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3"><?= mIdxStatusBadge($m['status'] ?? '') ?></td>
                    <td class="px-4 py-3 <?= $isPastDue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-600 dark:text-gray-400' ?>">
                        <?= htmlspecialchars($nextMaint ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($isPastDue): ?><span class="ml-1 text-xs">(förfallen)</span><?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <a href="/machines/<?= (int)$m['id'] ?>" class="text-blue-600 hover:underline text-xs">Visa</a>
                            <a href="/machines/<?= (int)$m['id'] ?>/edit" class="text-gray-600 dark:text-gray-400 hover:underline text-xs">Redigera</a>
                            <form method="POST" action="/machines/<?= (int)$m['id'] ?>/delete" class="inline" onsubmit="return confirm('Är du säker på att du vill ta bort denna maskin?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($machines)): ?>
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga maskiner registrerade ännu. <a href="/machines/create" class="text-blue-600 hover:underline">Lägg till den första →</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
function mIdxStatusBadge(string $s): string {
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
