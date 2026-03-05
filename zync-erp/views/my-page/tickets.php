<?php
$categoryMap = [
    'complaint' => 'Klagomål',
    'inquiry'   => 'Förfrågan',
    'return'    => 'Retur',
    'warranty'  => 'Garanti',
    'support'   => 'Support',
    'other'     => 'Övrigt',
];
$priorityMap = [
    'low'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Låg'],
    'normal' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Normal'],
    'high'   => ['bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'Hög'],
    'urgent' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Brådskande'],
];
$statusMap = [
    'open'             => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Öppet'],
    'in_progress'      => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'Pågående'],
    'waiting_customer' => ['bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'Väntar kund'],
    'waiting_internal' => ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'Väntar internt'],
    'resolved'         => ['bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400', 'Löst'],
    'closed'           => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Stängt'],
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mina ärenden</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ärenden du skapat eller är tilldelade</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/cs/tickets/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt ärende</a>
            <a href="/my-page" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Min Sida</a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ärendenr</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Titel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Prioritet</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Datum</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($tickets as $t): ?>
                    <?php $p = $priorityMap[$t['priority']] ?? ['bg-gray-100 text-gray-600', $t['priority']]; ?>
                    <?php $s = $statusMap[$t['status']] ?? ['bg-gray-100 text-gray-600', $t['status']]; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/cs/tickets/<?= (int)$t['id'] ?>" class="hover:underline"><?= htmlspecialchars($t['ticket_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white max-w-[250px] truncate">
                            <a href="/cs/tickets/<?= (int)$t['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                <?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($categoryMap[$t['category']] ?? $t['category'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $p[0] ?>"><?= $p[1] ?></span></td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $s[0] ?>"><?= $s[1] ?></span></td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars(substr($t['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/cs/tickets/<?= (int)$t['id'] ?>" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Visa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">
                            Inga ärenden hittades.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
