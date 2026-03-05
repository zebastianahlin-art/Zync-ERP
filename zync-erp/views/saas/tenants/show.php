<?php
$statusColors = [
    'trial'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
    'active'    => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'suspended' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
    'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
];
$statusLabels = ['trial' => 'Trial', 'active' => 'Aktiv', 'suspended' => 'Pausad', 'cancelled' => 'Avslutad'];
$planLabels   = ['starter' => 'Starter', 'professional' => 'Professional', 'enterprise' => 'Enterprise'];
$activeModules = array_column(array_filter($tenant['modules'] ?? [], fn($m) => $m['is_active']), 'module_slug');
?>
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <a href="/saas-admin/tenants" class="hover:text-indigo-600 dark:hover:text-indigo-400">Kunder</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium"><?= htmlspecialchars((string) $tenant['company_name'], ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars((string) $tenant['company_name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium mt-1 <?= $statusColors[$tenant['status']] ?? '' ?>">
                <?= htmlspecialchars($statusLabels[$tenant['status']] ?? $tenant['status'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <div class="flex gap-2">
            <a href="/saas-admin/tenants/<?= (int) $tenant['id'] ?>/history" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Historik</a>
            <a href="/saas-admin/tenants/<?= (int) $tenant['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Redigera</a>
            <a href="/saas-admin/invoices/create?tenant_id=<?= (int) $tenant['id'] ?>" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">Ny faktura</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Tenant info -->
        <div class="lg:col-span-1 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-3">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Kundinfo</h2>
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Org.nr</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($tenant['org_number'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Kontaktperson</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($tenant['contact_name'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">E-post</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $tenant['contact_email'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Telefon</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($tenant['contact_phone'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Abonnemang</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars($planLabels[$tenant['plan']] ?? $tenant['plan'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Max användare</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= (int) $tenant['max_users'] ?></dd>
                </div>
                <?php if (!empty($tenant['trial_ends_at'])): ?>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Trial avslutas</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $tenant['trial_ends_at'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Kund sedan</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($tenant['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php if (!empty($tenant['notes'])): ?>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Anteckningar</dt>
                    <dd class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap text-xs"><?= htmlspecialchars((string) $tenant['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
            </dl>

            <!-- Delete -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="/saas-admin/tenants/<?= (int) $tenant['id'] ?>/delete"
                      onsubmit="return confirm('Är du säker på att du vill ta bort denna kund?')">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="w-full rounded-lg bg-red-100 dark:bg-red-900/30 px-3 py-2 text-sm font-medium text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                        Ta bort kund
                    </button>
                </form>
            </div>
        </div>

        <!-- Module activation -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Modulaktivering</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <?php foreach ($all_modules as $mod): ?>
                    <?php $isActive = in_array($mod['slug'], $activeModules, true); ?>
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $mod['name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars((string) $mod['slug'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <?php if ($isActive): ?>
                            <form method="POST" action="/saas-admin/tenants/<?= (int) $tenant['id'] ?>/modules/deactivate">
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="module_slug" value="<?= htmlspecialchars((string) $mod['slug'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="rounded-lg px-2.5 py-1 text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                                    Inaktivera
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/saas-admin/tenants/<?= (int) $tenant['id'] ?>/modules/activate">
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="module_slug" value="<?= htmlspecialchars((string) $mod['slug'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="rounded-lg px-2.5 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                    Aktivera
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>
