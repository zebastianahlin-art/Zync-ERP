<?php
$statuses = [
    '' => 'Alla',
    'draft' => 'Utkast',
    'planned' => 'Planerade',
    'released' => 'Frisläppta',
    'in_progress' => 'Pågående',
    'completed' => 'Klara',
    'cancelled' => 'Avbrutna',
];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produktionsordrar</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= count($orders) ?> ordrar</p>
        </div>
        <div class="flex gap-2">
            <a href="/production" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Översikt</a>
            <a href="/production/orders/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny order
            </a>
        </div>
    </div>

    <!-- Status filter -->
    <div class="flex flex-wrap gap-2">
        <?php foreach ($statuses as $val => $label): ?>
        <a href="/production/orders<?= $val ? '?status=' . $val : '' ?>"
           class="rounded-full px-3 py-1 text-sm font-medium transition-colors <?= ($currentStatus ?? '') === $val ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($orders)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Order</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Artikel</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Linje</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ansvarig</th>
                    <th class="px-5 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prio</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Framsteg</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Planerad start</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($orders as $o): ?>
                <?php
                    $pct = $o['quantity_planned'] > 0 ? round(($o['quantity_produced'] / $o['quantity_planned']) * 100) : 0;
                    $prioColor = match($o['priority']) {
                        'urgent' => 'text-red-600 dark:text-red-400',
                        'high'   => 'text-orange-600 dark:text-orange-400',
                        'normal' => 'text-gray-500 dark:text-gray-400',
                        'low'    => 'text-gray-400 dark:text-gray-500',
                        default  => 'text-gray-500',
                    };
                    $prioLabel = match($o['priority']) {
                        'urgent' => '🔴 Brådskande', 'high' => '🟠 Hög', 'normal' => 'Normal', 'low' => 'Låg', default => $o['priority'],
                    };
                    $oColor = match($o['status']) {
                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                        'planned'     => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                        'released'    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                        'completed'   => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'cancelled'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        default       => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                    };
                    $oLabel = match($o['status']) {
                        'draft' => 'Utkast', 'planned' => 'Planerad', 'released' => 'Frisläppt',
                        'in_progress' => 'Pågår', 'completed' => 'Klar', 'cancelled' => 'Avbruten', default => $o['status'],
                    };
                ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <td class="px-5 py-3 text-sm">
                        <a href="/production/orders/<?= $o['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($o['order_number']) ?></a>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300">
                        <?php if ($o['article_name']): ?>
                            <?= htmlspecialchars($o['article_name']) ?>
                            <span class="text-xs text-gray-400">(<?= htmlspecialchars($o['article_number'] ?? '') ?>)</span>
                        <?php else: ?>
                            <span class="text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($o['line_name'] ?? '—') ?></td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($o['assigned_name'] ?? '—') ?></td>
                    <td class="px-5 py-3 text-sm text-center <?= $prioColor ?>"><?= $prioLabel ?></td>
                    <td class="px-5 py-3 text-sm">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $oColor ?>"><?= $oLabel ?></span>
                    </td>
                    <td class="px-5 py-3 text-sm text-right">
                        <div class="flex items-center justify-end gap-2">
                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= min($pct, 100) ?>%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 w-8 text-right"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= $o['planned_start'] ? date('Y-m-d', strtotime($o['planned_start'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Inga ordrar</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= $currentStatus ? 'Inga ordrar med denna status.' : 'Skapa din första produktionsorder.' ?></p>
    </div>
    <?php endif; ?>
</div>
