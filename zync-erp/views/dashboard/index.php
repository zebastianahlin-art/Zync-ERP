<?php
$swedishDays = ['Sunday'=>'Söndag','Monday'=>'Måndag','Tuesday'=>'Tisdag','Wednesday'=>'Onsdag','Thursday'=>'Torsdag','Friday'=>'Fredag','Saturday'=>'Lördag'];
$swedishMonths = ['January'=>'januari','February'=>'februari','March'=>'mars','April'=>'april','May'=>'maj','June'=>'juni','July'=>'juli','August'=>'augusti','September'=>'september','October'=>'oktober','November'=>'november','December'=>'december'];
$dayName = $swedishDays[date('l')] ?? date('l');
$monthName = $swedishMonths[date('F')] ?? date('F');
$dateStr = $dayName . ' ' . date('j') . ' ' . $monthName . ' ' . date('Y');

$shortcutLinks = [
    'shortcut_workorder'   => '/maintenance/work-orders/create',
    'shortcut_fault'       => '/maintenance/faults/create',
    'shortcut_invoice'     => '/finance/invoices-out/create',
    'shortcut_requisition' => '/purchasing/requisitions/create',
];
$mandatoryLinks = [
    'risk_report' => '/safety/reports/create',
    'crisis_plan' => '/safety',
];
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Välkommen, <?= htmlspecialchars($currentUser['full_name'] ?? 'Användare', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a href="/dashboard/configure"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Anpassa dashboard
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($widgets as $w):
            $slug     = $w['slug'];
            $data     = $kpiData[$slug] ?? [];
            $category = $w['category'];
            $spanTwo  = ((int)($w['user_width'] ?? $w['default_width'])) >= 2 ? 'sm:col-span-2' : '';
        ?>

        <?php if ($category === 'mandatory'): ?>
            <div class="<?= $spanTwo ?> rounded-xl bg-red-600 dark:bg-red-700 p-5 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-white/20 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg"><?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                </div>
                <p class="text-sm text-red-100 mb-4"><?= htmlspecialchars($w['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <a href="<?= htmlspecialchars($mandatoryLinks[$slug] ?? '/', ENT_QUOTES, 'UTF-8') ?>"
                   class="inline-block bg-white text-red-700 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-red-50 transition">
                    <?= $slug === 'risk_report' ? 'Rapportera nu' : 'Visa plan' ?>
                </a>
            </div>

        <?php elseif ($category === 'kpi'): ?>
            <div class="<?= $spanTwo ?> bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-4">
                    <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <?php if (empty($data)): ?>
                    <p class="text-gray-400 text-sm">Ingen data tillgänglig</p>
                <?php else: ?>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($data as $key => $val): ?>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    <?php if (is_float($val)): ?>
                                        <?= number_format($val, 0, ',', ' ') ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?= htmlspecialchars(str_replace('_', ' ', $key), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($category === 'shortcut'): ?>
            <div class="<?= $spanTwo ?> bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex flex-col justify-between">
                <div>
                    <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-1">
                        <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                        <?= htmlspecialchars($w['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <a href="<?= htmlspecialchars($shortcutLinks[$slug] ?? '/', ENT_QUOTES, 'UTF-8') ?>"
                   class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Skapa ny
                </a>
            </div>

        <?php elseif ($category === 'list'): ?>
            <div class="<?= $spanTwo ?> bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">
                        <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                </div>
                <?php $items = $data['items'] ?? []; ?>
                <?php if (empty($items)): ?>
                    <p class="px-5 py-4 text-sm text-gray-400">Inga poster hittades.</p>
                <?php else: ?>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($items as $item): ?>
                            <div class="px-5 py-3 flex items-center justify-between text-sm">
                                <?php if ($slug === 'recent_workorders'): ?>
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            <?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <?php if (!empty($item['equipment_name'])): ?>
                                            <span class="text-xs text-gray-400 ml-2"><?= htmlspecialchars($item['equipment_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        <?= htmlspecialchars($item['status'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php elseif ($slug === 'recent_invoices'): ?>
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">
                                            <?= htmlspecialchars($item['invoice_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-xs text-gray-400 ml-2"><?= htmlspecialchars($item['customer_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <span class="font-mono text-xs text-gray-600 dark:text-gray-300">
                                        <?= number_format((float)($item['total_amount'] ?? 0), 2, ',', ' ') ?> kr
                                    </span>
                                <?php elseif ($slug === 'overdue_resources'): ?>
                                    <div>
                                        <span class="font-medium text-red-600 dark:text-red-400">
                                            <?= htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-xs text-gray-400 ml-2"><?= htmlspecialchars($item['location'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <span class="text-xs text-red-500">
                                        <?= htmlspecialchars($item['next_inspection_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <?= htmlspecialchars((string)reset($item), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($category === 'chart'): ?>
            <div class="<?= $spanTwo ?> bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-3">
                    <?= htmlspecialchars($w['name'], ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <div class="flex items-center justify-center h-32 border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg text-gray-400 text-sm">
                    Diagram kommer snart
                </div>
            </div>
        <?php endif; ?>

        <?php endforeach; ?>

        <?php if (empty($widgets)): ?>
            <div class="col-span-full text-center py-12 text-gray-400">
                <p class="mb-4">Inga widgets konfigurerade.</p>
                <a href="/dashboard/configure" class="text-indigo-600 hover:underline">Anpassa dashboard →</a>
            </div>
        <?php endif; ?>
    </div>
</div>
