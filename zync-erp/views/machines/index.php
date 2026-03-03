<?php
function machineStatusBadge(string $s): string {
    $m = [
        'running'       => ['Igång','bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
        'idle'          => ['Idle','bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'],
        'maintenance'   => ['Underhåll','bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
        'breakdown'     => ['Haveri','bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
        'decommissioned'=> ['Avvecklad','bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'],
    ];
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium '.($m[$s][1]??'bg-gray-100 text-gray-700').'">'.($m[$s][0]??$s).'</span>';
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Maskiner</h1>
        <a href="/machines/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Ny maskin</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nummer</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Plats</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Avdelning</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Nästa service</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($machines as $m): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/machines/<?= $m['id'] ?>"><?= htmlspecialchars($m['machine_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <a href="/machines/<?= $m['id'] ?>" class="hover:underline"><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($m['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($m['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><?= machineStatusBadge($m['status']) ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?php if (!empty($m['next_maintenance_date'])): ?>
                                <?php $overdue = strtotime($m['next_maintenance_date']) < time(); ?>
                                <span class="<?= $overdue ? 'text-red-600 dark:text-red-400 font-medium' : '' ?>">
                                    <?= htmlspecialchars($m['next_maintenance_date'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/machines/<?= $m['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-3">Redigera</a>
                            <form method="POST" action="/machines/<?= $m['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort maskin?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($machines)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga maskiner registrerade ännu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
