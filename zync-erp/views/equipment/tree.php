<?php
$typeIcons = ['facility' => '🏭', 'line' => '🔗', 'machine' => '⚙️', 'component' => '🔧', 'tool' => '🛠️'];
$statusDot = ['operational' => 'bg-green-500', 'maintenance' => 'bg-yellow-500', 'breakdown' => 'bg-red-500', 'decommissioned' => 'bg-gray-400'];

function renderTree(array $items, array $typeIcons, array $statusDot, int $level = 0): void {
    foreach ($items as $item): ?>
        <div class="<?= $level > 0 ? 'ml-6 border-l-2 border-gray-200 dark:border-gray-700 pl-4' : '' ?>">
            <div class="flex items-center gap-2 rounded-lg py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <span class="h-2.5 w-2.5 rounded-full <?= $statusDot[$item['status']] ?? 'bg-gray-400' ?>"></span>
                <span><?= $typeIcons[$item['type']] ?? '📦' ?></span>
                <a href="/equipment/<?= (int) $item['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
                <span class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($item['equipment_number'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php if (!empty($item['children'])): ?>
                <?php renderTree($item['children'], $typeIcons, $statusDot, $level + 1); ?>
            <?php endif; ?>
        </div>
    <?php endforeach;
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">🌳 Utrustningsträd</h1>
        <div class="flex items-center gap-3">
            <a href="/equipment" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Listvy</a>
            <a href="/equipment/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny utrustning</a>
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <?php if (empty($tree)): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ingen utrustning registrerad ännu.</p>
        <?php else: ?>
            <?php renderTree($tree, $typeIcons, $statusDot); ?>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-green-500"></span> I drift</span>
        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span> Underhåll</span>
        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-red-500"></span> Haveri</span>
        <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-gray-400"></span> Avvecklad</span>
    </div>
</div>
