<?php
$statusColors = [
    'trial'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
    'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'suspended' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
    'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
];
$statusLabels = [
    'trial'     => 'Trial',
    'active'    => 'Aktiv',
    'suspended' => 'Pausad',
    'cancelled' => 'Avslutad',
];
$planLabels = [
    'starter'      => 'Starter',
    'professional' => 'Professional',
    'enterprise'   => 'Enterprise',
];
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">SaaS Admin – Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Översikt av kundbasen och abonnemang</p>
        </div>
        <div class="flex gap-2">
            <a href="/saas-admin/tenants/create" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">+ Ny kund</a>
            <a href="/saas-admin/invoices/create" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">+ Ny faktura</a>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Totalt kunder</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= (int) ($stats['total_tenants'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aktiva</p>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400"><?= (int) ($stats['active_tenants'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Trial</p>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?= (int) ($stats['trial_tenants'] ?? 0) ?></p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Öppna ärenden</p>
            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400"><?= (int) ($stats['open_tickets'] ?? 0) ?></p>
        </div>
    </div>

    <!-- Monthly revenue -->
    <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 shadow-md text-white">
        <p class="text-sm font-medium text-indigo-200 uppercase tracking-wide">Månadens intäkter (betalda fakturor)</p>
        <p class="mt-2 text-4xl font-bold"><?= number_format((float) ($stats['monthly_revenue'] ?? 0), 2, ',', ' ') ?> kr</p>
    </div>

    <!-- Quick links -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <a href="/saas-admin/tenants" class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-md text-center hover:shadow-lg transition-shadow">
            <div class="text-2xl mb-2">🏢</div>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Kunder</p>
        </a>
        <a href="/saas-admin/invoices" class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-md text-center hover:shadow-lg transition-shadow">
            <div class="text-2xl mb-2">📄</div>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Fakturering</p>
        </a>
        <a href="/saas-admin/support" class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-md text-center hover:shadow-lg transition-shadow">
            <div class="text-2xl mb-2">🎫</div>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Support</p>
        </a>
        <a href="/admin" class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-md text-center hover:shadow-lg transition-shadow">
            <div class="text-2xl mb-2">⚙️</div>
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Admin</p>
        </a>
    </div>

    <!-- Recent tenants + tickets -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senaste kunder</h2>
                <a href="/saas-admin/tenants" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
            </div>
            <?php if (empty($stats['recent_tenants'])): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga kunder registrerade.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($stats['recent_tenants'] as $t): ?>
                        <a href="/saas-admin/tenants/<?= (int) $t['id'] ?>" class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $t['company_name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars((string) ($t['contact_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $statusColors[$t['status']] ?? '' ?>">
                                <?= htmlspecialchars($statusLabels[$t['status']] ?? $t['status'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Senaste support-ärenden</h2>
                <a href="/saas-admin/support" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
            </div>
            <?php if (empty($stats['recent_tickets'])): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga öppna ärenden.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($stats['recent_tickets'] as $t): ?>
                        <a href="/saas-admin/support/<?= (int) $t['id'] ?>" class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $t['subject'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars((string) ($t['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars((string) $t['ticket_number'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
