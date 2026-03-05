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
$byPlan = $stats['by_plan'] ?? [];
$totalTenants = (int) ($stats['total_tenants'] ?? 0);
?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <span class="text-gray-900 dark:text-white font-medium">SaaS Admin — Dashboard</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">SaaS Admin – Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Översikt av kundbasen, abonnemang och intäkter</p>
        </div>
        <div class="flex gap-2">
            <a href="/saas-admin/tenants/provision" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">+ Ny kund</a>
            <a href="/saas-admin/invoices/generate" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">Batch-fakturera</a>
        </div>
    </div>

    <!-- Trial expiry warning -->
    <?php if (!empty($stats['expiring_trials'])): ?>
    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700 p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl">⚠️</span>
            <div>
                <p class="text-sm font-semibold text-amber-800 dark:text-amber-400">
                    <?= count($stats['expiring_trials']) ?> trial-kund(er) löper ut inom 7 dagar
                </p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <?php foreach ($stats['expiring_trials'] as $et): ?>
                    <a href="/saas-admin/tenants/<?= (int) $et['id'] ?>"
                       class="rounded-full bg-amber-100 dark:bg-amber-900/40 px-3 py-0.5 text-xs text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/60 transition-colors">
                        <?= htmlspecialchars((string) $et['company_name'], ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars((string) $et['trial_ends_at'], ENT_QUOTES, 'UTF-8') ?>)
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- KPI cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Totalt kunder</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= $totalTenants ?></p>
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

    <!-- Revenue & Invoice KPIs -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 shadow-md text-white sm:col-span-1">
            <p class="text-xs font-medium text-indigo-200 uppercase tracking-wide">MRR (denna månad)</p>
            <p class="mt-2 text-3xl font-bold"><?= number_format((float) ($stats['monthly_revenue'] ?? 0), 0, ',', ' ') ?> kr</p>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Obetalda fakturor</p>
            <p class="mt-2 text-3xl font-bold text-orange-600 dark:text-orange-400"><?= (int) ($stats['unpaid_invoices'] ?? 0) ?></p>
            <a href="/saas-admin/invoices?status=sent" class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa →</a>
        </div>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Förfallna fakturor</p>
            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400"><?= (int) ($stats['overdue_invoices'] ?? 0) ?></p>
            <a href="/saas-admin/invoices?status=overdue" class="mt-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa →</a>
        </div>
    </div>

    <!-- Plan distribution (CSS bars) + Quick links -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Kunder per plan -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Kunder per plan</h2>
                <a href="/saas-admin/plans" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Hantera planer →</a>
            </div>
            <?php
            $planBarColors = [
                'starter'      => 'bg-indigo-500',
                'professional' => 'bg-purple-500',
                'enterprise'   => 'bg-amber-500',
            ];
            $planOrder = ['starter', 'professional', 'enterprise'];
            if (empty($byPlan)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Inga kunder registrerade ännu.</p>
            <?php else:
                foreach ($planOrder as $slug):
                    $cnt = (int) ($byPlan[$slug] ?? 0);
                    if ($cnt === 0 && !isset($byPlan[$slug])) continue;
                    $pct = $totalTenants > 0 ? round($cnt / $totalTenants * 100) : 0;
                    $barColor = $planBarColors[$slug] ?? 'bg-gray-400';
            ?>
            <div class="mb-3">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($planLabels[$slug] ?? $slug, ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-xs text-gray-500 dark:text-gray-400"><?= $cnt ?> kund<?= $cnt !== 1 ? 'er' : '' ?> (<?= $pct ?>%)</span>
                </div>
                <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    <div class="h-full rounded-full <?= $barColor ?> transition-all duration-500" style="width: <?= $pct ?>%"></div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <!-- Quick links -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Snabblänkar</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="/saas-admin/tenants" class="flex items-center gap-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-2xl">🏢</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Kunder</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= $totalTenants ?> totalt</p>
                    </div>
                </a>
                <a href="/saas-admin/invoices" class="flex items-center gap-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-2xl">📄</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Fakturering</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= (int) ($stats['unpaid_invoices'] ?? 0) ?> obetalda</p>
                    </div>
                </a>
                <a href="/saas-admin/support" class="flex items-center gap-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-2xl">🎫</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Support</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= (int) ($stats['open_tickets'] ?? 0) ?> öppna</p>
                    </div>
                </a>
                <a href="/saas-admin/plans" class="flex items-center gap-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <span class="text-2xl">📋</span>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Planer</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= count($plans ?? []) ?> konfigurerade</p>
                    </div>
                </a>
            </div>
        </div>
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
