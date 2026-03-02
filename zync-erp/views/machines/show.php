<?php
$typeLabels = ['site'=>'Anläggning','area'=>'Område','line'=>'Linje','machine'=>'Maskin','sub_machine'=>'Delmaskin','component'=>'Komponent'];
$statusLabels = ['operational'=>['Drift','bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'],'degraded'=>['Nedsatt','bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'],'down'=>['Stillastående','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],'decommissioned'=>['Avvecklad','bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400']];
$critColors = ['A'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400','B'=>'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400','C'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'];
$sl = $statusLabels[$machine['status']] ?? ['?','bg-gray-100 text-gray-500'];
$cc = $critColors[$machine['criticality']] ?? $critColors['B'];
$docTypes = ['drawing'=>'Ritning','manual'=>'Manual','datasheet'=>'Datablad','photo'=>'Foto','certificate'=>'Certifikat','other'=>'Övrigt'];
?>

<div class="space-y-6">
    <!-- Breadcrumb + actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <a href="/machines" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <?php if ($machine['parent_name']): ?>
                <a href="/machines/<?= $machine['parent_id'] ?>" class="text-sm text-gray-500 hover:text-indigo-600"><?= htmlspecialchars($machine['parent_name'], ENT_QUOTES, 'UTF-8') ?></a>
                <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <?php endif; ?>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars($machine['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>
        <div class="flex gap-2">
            <a href="/machines/create?parent=<?= $machine['id'] ?>" class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Lägg till under
            </a>
            <a href="/machines/<?= $machine['id'] ?>/edit" class="inline-flex items-center gap-1 rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Redigera</a>
            <form method="POST" action="/machines/<?= $machine['id'] ?>/delete" onsubmit="return confirm('Är du säker?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 ring-1 ring-red-200 dark:ring-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Info card -->
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
        <div class="grid grid-cols-2 gap-x-8 gap-y-4 sm:grid-cols-4">
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kod</p>
                <p class="mt-1 font-mono font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($machine['code'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Typ</p>
                <p class="mt-1 text-gray-900 dark:text-white"><?= $typeLabels[$machine['type']] ?? $machine['type'] ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</p>
                <p class="mt-1"><span class="inline-flex rounded-full <?= $sl[1] ?> px-2 py-0.5 text-xs font-medium"><?= $sl[0] ?></span></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kritikalitet</p>
                <p class="mt-1"><span class="inline-flex rounded-full <?= $cc ?> px-2 py-0.5 text-xs font-bold"><?= $machine['criticality'] ?></span></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tillverkare</p>
                <p class="mt-1 text-gray-900 dark:text-white"><?= htmlspecialchars($machine['manufacturer'] ?: '—', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Modell</p>
                <p class="mt-1 text-gray-900 dark:text-white"><?= htmlspecialchars($machine['model'] ?: '—', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Serienummer</p>
                <p class="mt-1 font-mono text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($machine['serial_number'] ?: '—', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Installationsår</p>
                <p class="mt-1 text-gray-900 dark:text-white"><?= $machine['year_installed'] ?: '—' ?></p>
            </div>
            <div class="sm:col-span-4">
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Plats</p>
                <p class="mt-1 text-gray-900 dark:text-white"><?= htmlspecialchars($machine['location'] ?: '—', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if ($machine['description']): ?>
            <div class="sm:col-span-4">
                <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Beskrivning</p>
                <p class="mt-1 text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($machine['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Children -->
    <?php if (!empty($children)): ?>
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Underliggande utrustning (<?= count($children) ?>)</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($children as $c):
                $csl = $statusLabels[$c['status']] ?? ['?','bg-gray-100 text-gray-500'];
            ?>
                <a href="/machines/<?= $c['id'] ?>" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div>
                        <span class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="ml-2 font-mono text-xs text-gray-400"><?= htmlspecialchars($c['code'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="inline-flex rounded-full <?= $csl[1] ?> px-2 py-0.5 text-xs font-medium"><?= $csl[0] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Spare parts -->
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reservdelar (<?= count($spareParts) ?>)</h2>
        </div>
        <?php if (!empty($spareParts)): ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Artikelnr</th>
                        <th class="px-6 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Namn</th>
                        <th class="px-6 py-2 text-right font-semibold text-gray-600 dark:text-gray-300">Antal</th>
                        <th class="px-6 py-2 text-left font-semibold text-gray-600 dark:text-gray-300">Not</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($spareParts as $sp): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-2 font-mono text-xs text-gray-500"><?= htmlspecialchars($sp['article_number'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-2 text-gray-900 dark:text-white"><?= htmlspecialchars($sp['article_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-2 text-right text-gray-900 dark:text-white"><?= $sp['quantity'] ?> <?= htmlspecialchars($sp['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-2 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($sp['note'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-2 text-right">
                                <form method="POST" action="/machines/<?= $machine['id'] ?>/spare-parts/<?= $sp['id'] ?>/delete" onsubmit="return confirm('Ta bort koppling?')">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button class="text-xs text-red-600 hover:underline">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4" x-data="{ open: false }">
            <button @click="open = !open" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">+ Koppla reservdel</button>
            <form x-show="open" x-cloak method="POST" action="/machines/<?= $machine['id'] ?>/spare-parts" class="mt-3 flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Artikel</label>
                    <select name="article_id" required class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">— Välj —</option>
                        <?php
                        $artStmt = \App\Core\Database::pdo()->query('SELECT id, article_number, name FROM articles WHERE is_deleted=0 ORDER BY name');
                        foreach ($artStmt as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Antal</label>
                    <input type="number" name="quantity" value="1" min="0.01" step="0.01" class="w-20 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Notering</label>
                    <input type="text" name="note" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Lägg till</button>
            </form>
        </div>
    </div>

    <!-- Documents -->
    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Dokument (<?= count($documents) ?>)</h2>
        </div>
        <?php if (!empty($documents)): ?>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($documents as $doc): ?>
                    <div class="flex items-center justify-between px-6 py-3">
                        <div>
                            <a href="/<?= htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            <span class="ml-2 text-xs text-gray-400"><?= $docTypes[$doc['type']] ?? $doc['type'] ?></span>
                            <?php if ($doc['file_size']): ?>
                                <span class="ml-1 text-xs text-gray-400">(<?= round($doc['file_size'] / 1024) ?> KB)</span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="/machines/<?= $machine['id'] ?>/documents/<?= $doc['id'] ?>/delete" onsubmit="return confirm('Ta bort dokument?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="text-xs text-red-600 hover:underline">Ta bort</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4" x-data="{ open: false }">
            <button @click="open = !open" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">+ Ladda upp dokument</button>
            <form x-show="open" x-cloak method="POST" action="/machines/<?= $machine['id'] ?>/documents" enctype="multipart/form-data" class="mt-3 flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Titel *</label>
                    <input type="text" name="title" required class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Typ</label>
                    <select name="doc_type" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($docTypes as $k => $v): ?>
                            <option value="<?= $k ?>"><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Fil *</label>
                    <input type="file" name="file" required class="text-sm text-gray-600 dark:text-gray-400">
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Ladda upp</button>
            </form>
        </div>
    </div>
</div>
