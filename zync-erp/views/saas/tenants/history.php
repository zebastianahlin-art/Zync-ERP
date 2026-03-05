<?php
$e = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$actionLabels = [
    'created'            => ['🆕', 'Kund skapad',          'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'],
    'status_change'      => ['🔄', 'Statusändring',         'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'],
    'plan_change'        => ['📋', 'Planändring',           'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'],
    'module_activated'   => ['✅', 'Modul aktiverad',       'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'],
    'module_deactivated' => ['⛔', 'Modul inaktiverad',     'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'],
];

$statusLabels = [
    'trial'     => 'Trial',
    'active'    => 'Aktiv',
    'suspended' => 'Pausad',
    'cancelled' => 'Avslutad',
];
?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <a href="/saas-admin/tenants" class="hover:text-indigo-600 dark:hover:text-indigo-400">Kunder</a>
        <span>/</span>
        <a href="/saas-admin/tenants/<?= (int) $tenant['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= $e((string) $tenant['company_name']) ?></a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">Historik</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Historik — <?= $e((string) $tenant['company_name']) ?></h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Alla statusändringar och händelser för denna kund</p>
        </div>
        <a href="/saas-admin/tenants/<?= (int) $tenant['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Tillbaka till kund</a>
    </div>

    <!-- Status flow visualization -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Statusflöde</h2>
        <div class="flex items-center gap-2 flex-wrap">
            <?php
            $statuses = ['trial', 'active', 'suspended', 'cancelled'];
            $currentStatus = $tenant['status'] ?? 'trial';
            $statusColors2 = [
                'trial'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-300 dark:border-yellow-700',
                'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-300 dark:border-green-700',
                'suspended' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-300 dark:border-red-700',
                'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600',
            ];
            foreach ($statuses as $i => $s):
            ?>
            <span class="rounded-full border px-3 py-1 text-xs font-medium <?= $statusColors2[$s] ?? '' ?> <?= $s === $currentStatus ? 'ring-2 ring-offset-2 ring-indigo-500 dark:ring-offset-gray-800' : 'opacity-60' ?>">
                <?= $s === $currentStatus ? '● ' : '' ?><?= $e($statusLabels[$s] ?? $s) ?>
            </span>
            <?php if ($i < count($statuses) - 1): ?>
            <span class="text-gray-400 dark:text-gray-600 text-sm">→</span>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Nuvarande status är markerad med ring. Kunden kan röra sig mellan alla statusar.</p>
    </div>

    <!-- History timeline -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Händelselogg</h2>
        </div>

        <?php if (empty($history)): ?>
        <div class="p-10 text-center">
            <p class="text-gray-500 dark:text-gray-400">Ingen historik registrerad ännu.</p>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Händelser loggas automatiskt när status eller moduler ändras.</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($history as $entry):
                $meta = $actionLabels[$entry['action']] ?? ['📌', ucfirst(str_replace('_', ' ', $entry['action'])), 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                [$icon, $label, $badgeClass] = $meta;
            ?>
            <div class="flex items-start gap-4 px-6 py-4">
                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-lg leading-none">
                    <?= $icon ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $badgeClass ?>">
                            <?= $e($label) ?>
                        </span>
                        <?php if (!empty($entry['old_value']) && !empty($entry['new_value'])): ?>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="line-through"><?= $e((string) ($statusLabels[$entry['old_value']] ?? $entry['old_value'])) ?></span>
                            → <strong class="text-gray-700 dark:text-gray-300"><?= $e((string) ($statusLabels[$entry['new_value']] ?? $entry['new_value'])) ?></strong>
                        </span>
                        <?php elseif (!empty($entry['new_value'])): ?>
                        <span class="text-xs text-gray-700 dark:text-gray-300 font-medium"><?= $e((string) $entry['new_value']) ?></span>
                        <?php elseif (!empty($entry['old_value'])): ?>
                        <span class="text-xs text-gray-500 dark:text-gray-400 line-through"><?= $e((string) $entry['old_value']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($entry['notes'])): ?>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400"><?= $e((string) $entry['notes']) ?></p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        <?= $e((string) ($entry['changed_by_username'] ?? 'System')) ?> &middot;
                        <?= $e((string) $entry['created_at']) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
