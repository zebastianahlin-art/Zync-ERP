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
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Kunder</h1>
        <div class="flex gap-2">
            <a href="/saas-admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Dashboard</a>
            <a href="/saas-admin/tenants/create" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">+ Ny kund</a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/saas-admin/tenants" class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
        <div class="flex flex-wrap gap-3">
            <select name="status" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla statusar</option>
                <option value="trial" <?= ($filters['status'] ?? '') === 'trial' ? 'selected' : '' ?>>Trial</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Pausad</option>
                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Avslutad</option>
            </select>
            <select name="plan" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Alla abonnemang</option>
                <option value="starter" <?= ($filters['plan'] ?? '') === 'starter' ? 'selected' : '' ?>>Starter</option>
                <option value="professional" <?= ($filters['plan'] ?? '') === 'professional' ? 'selected' : '' ?>>Professional</option>
                <option value="enterprise" <?= ($filters['plan'] ?? '') === 'enterprise' ? 'selected' : '' ?>>Enterprise</option>
            </select>
            <input type="text" name="search" value="<?= htmlspecialchars((string) ($filters['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Sök företag, e-post..."
                   class="flex-1 min-w-32 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Filtrera</button>
        </div>
    </form>

    <!-- Tenants table -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Företag</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kontakt</th>
                    <th class="px-5 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">E-post</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Plan</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                    <th class="px-5 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Max users</th>
                    <th class="px-5 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <?php if (empty($tenants)): ?>
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Inga kunder hittades.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tenants as $t): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">
                                <a href="/saas-admin/tenants/<?= (int) $t['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    <?= htmlspecialchars((string) $t['company_name'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                                <?php if (!empty($t['org_number'])): ?>
                                    <p class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars((string) $t['org_number'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string) ($t['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string) $t['contact_email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400"><?= htmlspecialchars($planLabels[$t['plan']] ?? $t['plan'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusColors[$t['status']] ?? '' ?>">
                                    <?= htmlspecialchars($statusLabels[$t['status']] ?? $t['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-400"><?= (int) $t['max_users'] ?></td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/saas-admin/tenants/<?= (int) $t['id'] ?>" class="rounded px-2.5 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Visa</a>
                                    <a href="/saas-admin/tenants/<?= (int) $t['id'] ?>/edit" class="rounded px-2.5 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">Redigera</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
