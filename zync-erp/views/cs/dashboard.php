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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kundtjänst</h1>
        <div class="flex gap-3">
            <a href="/cs/tickets/my" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition text-gray-700 dark:text-gray-300">Mina ärenden</a>
            <a href="/cs/tickets" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition text-gray-700 dark:text-gray-300">Alla ärenden</a>
            <a href="/cs/tickets/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt ärende</a>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Öppna</p>
            <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400"><?= htmlspecialchars((string) $stats['open'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pågående</p>
            <p class="mt-1 text-3xl font-bold text-yellow-500 dark:text-yellow-400"><?= htmlspecialchars((string) $stats['in_progress'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lösta</p>
            <p class="mt-1 text-3xl font-bold text-teal-600 dark:text-teal-400"><?= htmlspecialchars((string) $stats['resolved'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Stängda</p>
            <p class="mt-1 text-3xl font-bold text-gray-500 dark:text-gray-400"><?= htmlspecialchars((string) $stats['closed'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <!-- Recent open tickets -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Senaste öppna ärenden</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Ärendenr</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Titel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Kund</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Prioritet</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($recent_tickets as $t): ?>
                    <?php $p = $priorityMap[$t['priority']] ?? ['bg-gray-100 text-gray-600', $t['priority']]; ?>
                    <?php $s = $statusMap[$t['status']] ?? ['bg-gray-100 text-gray-600', $t['status']]; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <a href="/cs/tickets/<?= $t['id'] ?>" class="hover:underline"><?= htmlspecialchars($t['ticket_number'], ENT_QUOTES, 'UTF-8') ?></a>
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($t['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs <?= $p[0] ?>"><?= $p[1] ?></span></td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_tickets)): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500">Inga öppna ärenden</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
