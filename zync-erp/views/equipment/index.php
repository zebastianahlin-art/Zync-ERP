<?php
$typeLabels  = ['facility' => 'Anläggning', 'line' => 'Linje', 'machine' => 'Maskin', 'component' => 'Komponent', 'tool' => 'Verktyg'];
$statusLabel = ['operational' => 'I drift', 'maintenance' => 'Underhåll', 'breakdown' => 'Haveri', 'decommissioned' => 'Avvecklad'];
$statusBadge = [
    'operational'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'maintenance'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    'breakdown'      => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'decommissioned' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/30 dark:text-gray-400',
];
$critLabel = ['A' => 'Kritisk', 'B' => 'Viktig', 'C' => 'Övrigt'];
$critBadge = ['A' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'B' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'C' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/30 dark:text-gray-400'];
?>
<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <?php
        $cards = [
            ['label' => 'Totalt',     'value' => $stats['total'],          'color' => 'bg-indigo-500'],
            ['label' => 'I drift',    'value' => $stats['operational'],    'color' => 'bg-green-500'],
            ['label' => 'Underhåll',  'value' => $stats['maintenance'],    'color' => 'bg-yellow-500'],
            ['label' => 'Haveri',     'value' => $stats['breakdown'],      'color' => 'bg-red-500'],
            ['label' => 'Avvecklade', 'value' => $stats['decommissioned'], 'color' => 'bg-gray-500'],
        ];
        foreach ($cards as $card): ?>
            <div class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 rounded-full <?= $card['color'] ?>"></span>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $card['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Utrustning</h1>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="/equipment" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla typer</option>
                    <?php foreach ($typeLabels as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($filter['type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla statusar</option>
                    <?php foreach ($statusLabel as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($filter['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="department" onchange="this.form.submit()" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla avdelningar</option>
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?= (int) $dep['id'] ?>" <?= ($filter['department'] ?? '') == $dep['id'] ? 'selected' : '' ?>><?= htmlspecialchars($dep['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="/equipment/tree" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">🌳 Trädvy</a>
            <a href="/equipment/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ny utrustning
            </a>
        </div>
    </div>

    <!-- Table -->
    <?php if (empty($equipment)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Ingen utrustning hittades.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Nr</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Namn</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden md:table-cell">Plats</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden lg:table-cell">Överordnad</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Krit.</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($equipment as $eq): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($eq['equipment_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3">
                                <a href="/equipment/<?= (int) $eq['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= $typeLabels[$eq['type']] ?? $eq['type'] ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell"><?= htmlspecialchars($eq['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                                <?= $eq['parent_name'] ? htmlspecialchars($eq['parent_name'], ENT_QUOTES, 'UTF-8') : '—' ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium <?= $critBadge[$eq['criticality']] ?? '' ?>"><?= $critLabel[$eq['criticality']] ?? $eq['criticality'] ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$eq['status']] ?? '' ?>"><?= $statusLabel[$eq['status']] ?? $eq['status'] ?></span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/equipment/<?= (int) $eq['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Redigera</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
