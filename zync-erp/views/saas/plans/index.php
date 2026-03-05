<?php
$planColors = [
    'starter'      => 'indigo',
    'professional' => 'purple',
    'enterprise'   => 'amber',
];

// Static class maps to avoid Tailwind purge issues (CDN: not needed, but good practice)
$headerBg = [
    'indigo' => 'bg-indigo-600',
    'purple' => 'bg-purple-600',
    'amber'  => 'bg-amber-600',
    'gray'   => 'bg-gray-600',
];
$headerText = [
    'indigo' => 'text-indigo-100',
    'purple' => 'text-purple-100',
    'amber'  => 'text-amber-100',
    'gray'   => 'text-gray-100',
];
$badgeBg = [
    'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300',
    'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
    'amber'  => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
    'gray'   => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
];
?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">Abonnemangsplaner</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Abonnemangsplaner</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hantera tillgängliga planer och priser</p>
        </div>
        <a href="/saas-admin/plans/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">+ Ny plan</a>
    </div>

    <?php if (empty($plans)): ?>
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-10 text-center shadow-md">
            <p class="text-gray-500 dark:text-gray-400">Inga planer konfigurerade ännu.</p>
            <a href="/saas-admin/plans/create" class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Skapa första planen →</a>
        </div>
    <?php else: ?>

        <!-- Plan cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <?php foreach ($plans as $plan):
                $color = $planColors[$plan['slug']] ?? 'gray';
                $count = (int) ($countByPlan[$plan['slug']] ?? 0);
                $features = [];
                if (!empty($plan['features'])) {
                    $features = is_string($plan['features']) ? json_decode($plan['features'], true) : $plan['features'];
                }
                $modules = [];
                if (!empty($plan['included_modules'])) {
                    $modules = is_string($plan['included_modules']) ? json_decode($plan['included_modules'], true) : $plan['included_modules'];
                }
                $hBg    = $headerBg[$color]  ?? $headerBg['gray'];
                $hText  = $headerText[$color] ?? $headerText['gray'];
                $mBadge = $badgeBg[$color]    ?? $badgeBg['gray'];
            ?>
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-hidden flex flex-col">
                <div class="<?= $hBg ?> px-6 py-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php if (!$plan['is_active']): ?>
                            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs text-white">Inaktiv</span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-1 <?= $hText ?> text-sm"><?= htmlspecialchars((string) ($plan['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="p-6 flex-1 space-y-4">
                    <!-- Price -->
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white"><?= number_format((float) $plan['price_monthly'], 0, ',', ' ') ?></span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">kr/mån</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= number_format((float) $plan['price_yearly'], 0, ',', ' ') ?> kr/år</p>

                    <!-- Tenants count -->
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 px-4 py-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-semibold text-gray-900 dark:text-white"><?= $count ?></span> kund<?= $count !== 1 ? 'er' : '' ?>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= (int) $plan['max_users'] ?> max användare &middot; <?= (int) $plan['max_storage_gb'] ?> GB lagring</p>
                    </div>

                    <!-- Features -->
                    <?php if (!empty($features)): ?>
                    <ul class="space-y-1">
                        <?php foreach (array_slice($features, 0, 5) as $feature): ?>
                        <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <span class="mt-0.5 text-green-500">✓</span>
                            <?= htmlspecialchars((string) $feature, ENT_QUOTES, 'UTF-8') ?>
                        </li>
                        <?php endforeach; ?>
                        <?php if (count($features) > 5): ?>
                        <li class="text-xs text-gray-500 dark:text-gray-400">+<?= count($features) - 5 ?> fler...</li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>

                    <!-- Included modules -->
                    <?php if (!empty($modules)): ?>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Inkluderade moduler</p>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach ($modules as $mod): ?>
                            <span class="rounded-full px-2 py-0.5 text-xs <?= $mBadge ?>">
                                <?= htmlspecialchars((string) $mod, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex gap-2">
                    <a href="/saas-admin/plans/<?= (int) $plan['id'] ?>/edit" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Redigera</a>
                    <?php if ($count === 0): ?>
                    <form method="POST" action="/saas-admin/plans/<?= (int) $plan['id'] ?>/delete" class="flex-1"
                          onsubmit="return confirm('Ta bort planen <?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?>?')">
                        <?= \App\Core\Csrf::field() ?>
                        <button type="submit" class="w-full rounded-lg border border-red-300 dark:border-red-700 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
                    </form>
                    <?php else: ?>
                    <span class="flex-1 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-center text-xs text-gray-400 dark:text-gray-500 cursor-not-allowed" title="Kan inte ta bort — kunder finns på planen">Ta bort</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Feature comparison table -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Planjämförelse</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Funktion</th>
                            <?php foreach ($plans as $plan): ?>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"><?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300">Pris/mån</td>
                            <?php foreach ($plans as $plan): ?>
                            <td class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white"><?= number_format((float) $plan['price_monthly'], 0, ',', ' ') ?> kr</td>
                            <?php endforeach; ?>
                        </tr>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/20">
                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300">Max användare</td>
                            <?php foreach ($plans as $plan): ?>
                            <td class="px-4 py-3 text-center text-gray-900 dark:text-white"><?= (int) $plan['max_users'] >= 999 ? 'Obegränsat' : (int) $plan['max_users'] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300">Lagring</td>
                            <?php foreach ($plans as $plan): ?>
                            <td class="px-4 py-3 text-center text-gray-900 dark:text-white"><?= (int) $plan['max_storage_gb'] ?> GB</td>
                            <?php endforeach; ?>
                        </tr>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/20">
                            <td class="px-6 py-3 text-gray-700 dark:text-gray-300">Aktiva kunder</td>
                            <?php foreach ($plans as $plan): ?>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                                    <?= (int) ($countByPlan[$plan['slug']] ?? 0) ?>
                                </span>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <?php endif; ?>
</div>
