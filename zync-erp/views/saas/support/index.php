<?php
$priorityColors = [
    'low'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'normal' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'high'   => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400',
    'urgent' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
];
$priorityLabels = ['low' => 'Låg', 'normal' => 'Normal', 'high' => 'Hög', 'urgent' => 'Kritisk'];
$statusColors = [
    'open'        => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'waiting'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
    'resolved'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'closed'      => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-500',
];
$statusLabels = ['open' => 'Öppen', 'in_progress' => 'Pågår', 'waiting' => 'Väntar', 'resolved' => 'Löst', 'closed' => 'Stängd'];
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Support</h1>
        <a href="/saas-admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Dashboard</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/saas-admin/support" class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
        <div class="flex flex-wrap gap-3">
            <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla statusar</option>
                <?php foreach ($statusLabels as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= ($filters['status'] ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($lbl, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <select name="priority" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla prioriteter</option>
                <?php foreach ($priorityLabels as $val => $lbl): ?>
                    <option value="<?= $val ?>" <?= ($filters['priority'] ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($lbl, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tenant_id" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla kunder</option>
                <?php foreach ($tenants as $t): ?>
                    <option value="<?= (int) $t['id'] ?>" <?= ($filters['tenant_id'] ?? '') === (string) $t['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $t['company_name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Filtrera</button>
        </div>
    </form>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Ärendenr</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Ämne</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kund</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Prioritet</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Skapad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Inga ärenden hittades.</td></tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="window.location='/saas-admin/support/<?= (int) $t['id'] ?>'">
                            <td class="px-5 py-3 font-mono text-xs text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $t['ticket_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-gray-900 dark:text-gray-100 max-w-xs truncate">
                                <a href="/saas-admin/support/<?= (int) $t['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    <?= htmlspecialchars((string) $t['subject'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string) ($t['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $priorityColors[$t['priority']] ?? '' ?>">
                                    <?= htmlspecialchars($priorityLabels[$t['priority']] ?? $t['priority'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusColors[$t['status']] ?? '' ?>">
                                    <?= htmlspecialchars($statusLabels[$t['status']] ?? $t['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars((string) ($t['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
