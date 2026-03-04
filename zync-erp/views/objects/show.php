<?php
$typeLabels = [
    'machine'    => 'Maskin',
    'equipment'  => 'Utrustning',
    'article'    => 'Artikel',
    'customer'   => 'Kund',
    'supplier'   => 'Leverantör',
    'employee'   => 'Anställd',
    'work_order' => 'Arbetsorder',
];

$typeIcons = [
    'machine'    => '⚙️',
    'equipment'  => '🔧',
    'article'    => '📦',
    'customer'   => '🏢',
    'supplier'   => '🚚',
    'employee'   => '👤',
    'work_order' => '📋',
];

$nativeLinks = [
    'machine'    => '/machines',
    'equipment'  => '/equipment',
    'article'    => '/articles',
    'customer'   => '/customers',
    'supplier'   => '/suppliers',
    'employee'   => '/hr/employees',
    'work_order' => '/maintenance/work-orders',
];

$meta = isset($object['metadata']) && $object['metadata'] ? json_decode($object['metadata'], true) : [];
?>
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="/objects" class="hover:text-indigo-600 dark:hover:text-indigo-400">ObjektNavigator</a>
        <span>›</span>
        <span class="text-gray-900 dark:text-white"><?= htmlspecialchars($object['display_name'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="text-4xl"><?= $typeIcons[$object['object_type']] ?? '📄' ?></span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($object['display_name'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <span class="inline-flex mt-1 px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        <?= htmlspecialchars($typeLabels[$object['object_type']] ?? $object['object_type'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
            <?php if (isset($nativeLinks[$object['object_type']])): ?>
            <a href="<?= $nativeLinks[$object['object_type']] ?>/<?= (int) $object['object_id'] ?>"
               class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
               Öppna detaljsida →
            </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($meta)): ?>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach ($meta as $key => $val): ?>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key)), ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Parent link -->
        <?php if ($object['parent_type'] && $object['parent_id']): ?>
        <div class="mt-4 flex items-center gap-2 text-sm">
            <span class="text-gray-500 dark:text-gray-400">Tillhör:</span>
            <a href="/objects/<?= htmlspecialchars($object['parent_type'], ENT_QUOTES, 'UTF-8') ?>/<?= (int) $object['parent_id'] ?>"
               class="text-indigo-600 dark:text-indigo-400 hover:underline">
                <?= htmlspecialchars($typeLabels[$object['parent_type']] ?? $object['parent_type'], ENT_QUOTES, 'UTF-8') ?> #<?= (int) $object['parent_id'] ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Children / Related objects -->
    <?php if (!empty($children)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">Relaterade objekt (<?= count($children) ?>)</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($children as $child): ?>
            <a href="/objects/<?= htmlspecialchars($child['object_type'], ENT_QUOTES, 'UTF-8') ?>/<?= (int) $child['object_id'] ?>"
               class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center gap-3">
                    <span><?= $typeIcons[$child['object_type']] ?? '📄' ?></span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($child['display_name'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    <?= htmlspecialchars($typeLabels[$child['object_type']] ?? $child['object_type'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center text-gray-500 dark:text-gray-400 text-sm">
        Inga relaterade objekt hittades.
    </div>
    <?php endif; ?>
</div>
