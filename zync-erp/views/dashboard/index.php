<?php
/** @var string $title */
/** @var int $userId */
/** @var array $currentUser */
/** @var int $customers */
/** @var int $suppliers */
/** @var int $articles */
/** @var int $users */
/** @var array $recentCustomers */
/** @var array $recentSuppliers */
/** @var array $recentActivity */

$firstName = explode(' ', $currentUser['full_name'] ?? $currentUser['username'] ?? 'Användare')[0];
$greeting  = match (true) {
    (int) date('H') < 5   => '🌙 God natt',
    (int) date('H') < 12  => '☀️ God morgon',
    (int) date('H') < 18  => '👋 God eftermiddag',
    default                => '🌆 God kväll',
};
?>

<!-- Greeting -->
<div class="mb-8">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
        <?= $greeting ?>, <?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>!
    </h1>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Här är en överblick av ditt ZYNC ERP-system.
    </p>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-10">
    <!-- Kunder -->
    <a href="/sales/customers" class="group rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-md hover:ring-indigo-300 dark:hover:ring-indigo-600 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kunder</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"><?= $customers ?></p>
            </div>
            <div class="rounded-lg bg-indigo-50 dark:bg-indigo-900/30 p-3">
                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-indigo-600 dark:text-indigo-400 group-hover:underline">Visa alla →</p>
    </a>

    <!-- Leverantörer -->
    <a href="/suppliers" class="group rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-md hover:ring-emerald-300 dark:hover:ring-emerald-600 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Leverantörer</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"><?= $suppliers ?></p>
            </div>
            <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/30 p-3">
                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-emerald-600 dark:text-emerald-400 group-hover:underline">Visa alla →</p>
    </a>

    <!-- Artiklar -->
    <a href="/articles" class="group rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-md hover:ring-amber-300 dark:hover:ring-amber-600 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Artiklar</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"><?= $articles ?></p>
            </div>
            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/30 p-3">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-amber-600 dark:text-amber-400 group-hover:underline">Visa alla →</p>
    </a>

    <!-- Aktiva användare -->
    <a href="/admin/users" class="group rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-md hover:ring-purple-300 dark:hover:ring-purple-600 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktiva användare</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"><?= $users ?></p>
            </div>
            <div class="rounded-lg bg-purple-50 dark:bg-purple-900/30 p-3">
                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <p class="mt-3 text-xs text-purple-600 dark:text-purple-400 group-hover:underline">Hantera →</p>
    </a>
</div>

<!-- Quick Actions -->
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Snabbåtgärder</h2>
    <div class="flex flex-wrap gap-3">
        <a href="/sales/customers/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny kund
        </a>
        <a href="/suppliers/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny leverantör
        </a>
        <a href="/articles/create" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny artikel
        </a>
        <?php if (($currentUser['role_level'] ?? 0) >= 7): ?>
        <a href="/admin/users/create" class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny användare
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Two-column layout: Recent Customers & Suppliers -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

    <!-- Recent Customers -->
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Senaste kunder</h2>
            <a href="/sales/customers" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla →</a>
        </div>
        <?php if (empty($recentCustomers)): ?>
            <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Inga kunder ännu.</p>
        <?php else: ?>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($recentCustomers as $c): ?>
                    <li class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <a href="/sales/customers/<?= (int) $c['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Recent Suppliers -->
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Senaste leverantörer</h2>
            <a href="/suppliers" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Visa alla →</a>
        </div>
        <?php if (empty($recentSuppliers)): ?>
            <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Inga leverantörer ännu.</p>
        <?php else: ?>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($recentSuppliers as $s): ?>
                    <li class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($s['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <a href="/suppliers/<?= (int) $s['id'] ?>/edit" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Redigera</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Activity (Audit Log) -->
<div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Senaste aktivitet</h2>
    </div>
    <?php if (empty($recentActivity)): ?>
        <p class="px-6 py-8 text-sm text-gray-400 dark:text-gray-500 text-center">Ingen aktivitet loggad ännu.</p>
    <?php else: ?>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($recentActivity as $a): ?>
                <li class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            <?php
                            echo match ($a['action'] ?? '') {
                                'create' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'update' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'delete' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'login'  => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
                                default  => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };
                            ?>">
                            <?= htmlspecialchars($a['action'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div>
                            <p class="text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($a['entity_type'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                <span class="text-gray-400">#<?= (int) ($a['entity_id'] ?? 0) ?></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                av <?= htmlspecialchars($a['username'] ?? $a['user_email'] ?? 'System', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($a['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
