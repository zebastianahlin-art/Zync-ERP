<?php
$typeLabels  = ['facility' => 'Anläggning', 'line' => 'Linje', 'machine' => 'Maskin', 'component' => 'Komponent', 'tool' => 'Verktyg'];
$statusLabel = ['operational' => 'I drift', 'maintenance' => 'Underhåll', 'breakdown' => 'Haveri', 'decommissioned' => 'Avvecklad'];
$statusBadge = [
    'operational'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'maintenance'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    'breakdown'      => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'decommissioned' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/30 dark:text-gray-400',
];
$docTypeLabel = ['drawing' => 'Ritning', 'manual' => 'Manual', 'datasheet' => 'Datablad', 'photo' => 'Foto', 'certificate' => 'Certifikat', 'other' => 'Övrigt'];
?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$eq['status']] ?? '' ?>"><?= $statusLabel[$eq['status']] ?? $eq['status'] ?></span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($eq['equipment_number'], ENT_QUOTES, 'UTF-8') ?> · <?= $typeLabels[$eq['type']] ?? $eq['type'] ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/equipment" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Alla</a>
            <a href="/equipment/<?= (int) $eq['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Redigera</a>
        </div>
    </div>

    <!-- Info-kort -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Teknisk information</h2>
            <dl class="space-y-3 text-sm">
                <?php
                $fields = [
                    'Tillverkare'      => $eq['manufacturer'],
                    'Modell'           => $eq['model'],
                    'Serienummer'      => $eq['serial_number'],
                    'Installationsår'  => $eq['year_installed'],
                    'Plats'            => $eq['location'],
                    'Avdelning'        => $eq['department_name'],
                    'Överordnad'       => $eq['parent_name'] ? $eq['parent_name'] . ' (' . $eq['parent_number'] . ')' : null,
                ];
                foreach ($fields as $label => $val): if ($val): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400"><?= $label ?></dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                <?php endif; endforeach; ?>
            </dl>
            <?php if ($eq['notes']): ?>
                <div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-3 text-sm text-gray-600 dark:text-gray-400"><?= nl2br(htmlspecialchars($eq['notes'], ENT_QUOTES, 'UTF-8')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Barn/underordnade -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Underordnad utrustning (<?= count($children) ?>)</h2>
            <?php if (empty($children)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ingen underordnad utrustning.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($children as $child): ?>
                        <li class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-700/30 px-3 py-2">
                            <a href="/equipment/<?= (int) $child['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                <?= htmlspecialchars($child['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <span class="text-xs text-gray-500"><?= $typeLabels[$child['type']] ?? $child['type'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <a href="/equipment/create?type=component&parent=<?= (int) $eq['id'] ?>" class="mt-3 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">+ Lägg till komponent</a>
        </div>
    </div>

    <!-- Dokument -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Dokument (<?= count($documents) ?>)</h2>
        <?php if (!empty($documents)): ?>
            <div class="mb-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b dark:border-gray-700">
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Namn</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Typ</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Fil</th>
                        <th class="px-3 py-2"></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2 text-gray-900 dark:text-white"><?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2 text-gray-500"><?= $docTypeLabel[$doc['type']] ?? $doc['type'] ?></td>
                                <td class="px-3 py-2">
                                    <a href="/equipment/<?= (int) $eq['id'] ?>/documents/<?= (int) $doc['id'] ?>/download" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">📎 <?= htmlspecialchars($doc['file_name'], ENT_QUOTES, 'UTF-8') ?></a>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/documents/<?= (int) $doc['id'] ?>/delete" onsubmit="return confirm('Radera dokument?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="text-red-500 hover:underline text-xs">Radera</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/documents" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-4">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Namn</label>
                <input name="doc_name" type="text" required class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Typ</label>
                <select name="doc_type" class="mt-1 rounded-lg border border-gray-300
cat > /var/www/zync-erp/zync-erp/views/equipment/show.php << 'VIEWPHP'
<?php
$typeLabels  = ['facility' => 'Anläggning', 'line' => 'Linje', 'machine' => 'Maskin', 'component' => 'Komponent', 'tool' => 'Verktyg'];
$statusLabel = ['operational' => 'I drift', 'maintenance' => 'Underhåll', 'breakdown' => 'Haveri', 'decommissioned' => 'Avvecklad'];
$statusBadge = [
    'operational'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'maintenance'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    'breakdown'      => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    'decommissioned' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/30 dark:text-gray-400',
];
$docTypeLabel = ['drawing' => 'Ritning', 'manual' => 'Manual', 'datasheet' => 'Datablad', 'photo' => 'Foto', 'certificate' => 'Certifikat', 'other' => 'Övrigt'];
?>
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($eq['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$eq['status']] ?? '' ?>"><?= $statusLabel[$eq['status']] ?? $eq['status'] ?></span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($eq['equipment_number'], ENT_QUOTES, 'UTF-8') ?> · <?= $typeLabels[$eq['type']] ?? $eq['type'] ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/equipment" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Alla</a>
            <a href="/equipment/<?= (int) $eq['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Redigera</a>
        </div>
    </div>

    <!-- Info + Barn -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Teknisk information</h2>
            <dl class="space-y-3 text-sm">
                <?php
                $fields = [
                    'Tillverkare'      => $eq['manufacturer'],
                    'Modell'           => $eq['model'],
                    'Serienummer'      => $eq['serial_number'],
                    'Installationsår'  => $eq['year_installed'],
                    'Plats'            => $eq['location'],
                    'Avdelning'        => $eq['department_name'],
                    'Överordnad'       => $eq['parent_name'] ? $eq['parent_name'] . ' (' . $eq['parent_number'] . ')' : null,
                    'Kritikalitet'     => ['A' => 'A – Kritisk', 'B' => 'B – Viktig', 'C' => 'C – Övrigt'][$eq['criticality']] ?? $eq['criticality'],
                ];
                foreach ($fields as $label => $val): if ($val): ?>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400"><?= $label ?></dt>
                        <dd class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                <?php endif; endforeach; ?>
            </dl>
            <?php if ($eq['notes']): ?>
                <div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-3 text-sm text-gray-600 dark:text-gray-400"><?= nl2br(htmlspecialchars($eq['notes'], ENT_QUOTES, 'UTF-8')) ?></div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Underordnad utrustning (<?= count($children) ?>)</h2>
            <?php if (empty($children)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ingen underordnad utrustning.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($children as $child): ?>
                        <li class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-700/30 px-3 py-2">
                            <a href="/equipment/<?= (int) $child['id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                <?= htmlspecialchars($child['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <span class="text-xs text-gray-500"><?= $typeLabels[$child['type']] ?? $child['type'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <a href="/equipment/create?type=component" class="mt-3 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">+ Lägg till komponent</a>
        </div>
    </div>

    <!-- Dokument -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Dokument (<?= count($documents) ?>)</h2>
        <?php if (!empty($documents)): ?>
            <div class="mb-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b dark:border-gray-700">
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Namn</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Typ</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Fil</th>
                        <th class="px-3 py-2"></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2 text-gray-900 dark:text-white"><?= htmlspecialchars($doc['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2 text-gray-500"><?= $docTypeLabel[$doc['type']] ?? $doc['type'] ?></td>
                                <td class="px-3 py-2">
                                    <a href="/equipment/<?= (int) $eq['id'] ?>/documents/<?= (int) $doc['id'] ?>/download" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs"><?= htmlspecialchars($doc['file_name'], ENT_QUOTES, 'UTF-8') ?></a>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/documents/<?= (int) $doc['id'] ?>/delete" onsubmit="return confirm('Radera dokument?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="text-red-500 hover:underline text-xs">Radera</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/documents" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-4">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Namn</label>
                <input name="doc_name" type="text" required class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Typ</label>
                <select name="doc_type" class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <?php foreach ($docTypeLabel as $val => $lbl): ?>
                        <option value="<?= $val ?>"><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Fil</label>
                <input type="file" name="document_file" required class="mt-1 block text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Ladda upp</button>
        </form>
    </div>

    <!-- Reservdelar -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
        <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Kopplade reservdelar (<?= count($spareParts) ?>)</h2>
        <?php if (!empty($spareParts)): ?>
            <div class="mb-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b dark:border-gray-700">
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Artikelnr</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Namn</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Antal</th>
                        <th class="px-3 py-2 text-left text-gray-600 dark:text-gray-400">Enhet</th>
                        <th class="px-3 py-2"></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($spareParts as $sp): ?>
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-3 py-2 font-mono text-xs text-gray-500"><?= htmlspecialchars($sp['article_number'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2 text-gray-900 dark:text-white"><?= htmlspecialchars($sp['article_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400"><?= $sp['quantity_needed'] ?></td>
                                <td class="px-3 py-2 text-gray-500"><?= htmlspecialchars($sp['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-2 text-right">
                                    <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/spare-parts/<?= (int) $sp['id'] ?>/delete" onsubmit="return confirm('Ta bort koppling?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="text-red-500 hover:underline text-xs">Ta bort</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <form method="POST" action="/equipment/<?= (int) $eq['id'] ?>/spare-parts" class="flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-4">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Artikel-ID</label>
                <input name="article_id" type="number" min="1" required class="mt-1 w-24 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Antal</label>
                <input name="quantity_needed" type="number" step="0.01" min="0.01" value="1" class="mt-1 w-20 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Anteckning</label>
                <input name="notes" type="text" class="mt-1 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Koppla</button>
        </form>
    </div>
</div>
