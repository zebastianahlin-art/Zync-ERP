<div class="space-y-6">

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <?php
        $statCards = [
            ['label' => 'Totalt',    'value' => $stats['total'],    'color' => 'bg-indigo-500'],
            ['label' => 'Giltiga',   'value' => $stats['active'],   'color' => 'bg-green-500'],
            ['label' => 'Utgår snart','value' => $stats['expiring'],'color' => 'bg-yellow-500'],
            ['label' => 'Utgångna',  'value' => $stats['expired'],  'color' => 'bg-red-500'],
        ];
        foreach ($statCards as $card): ?>
            <div class="rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 rounded-full <?= $card['color'] ?>"></span>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400"><?= $card['label'] ?></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white"><?= $card['value'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Header + filters -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Certifikat</h1>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="/certificates" class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla statusar</option>
                    <option value="active"   <?= ($filter['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Giltiga</option>
                    <option value="expiring" <?= ($filter['status'] ?? '') === 'expiring' ? 'selected' : '' ?>>Utgår snart</option>
                    <option value="expired"  <?= ($filter['status'] ?? '') === 'expired'  ? 'selected' : '' ?>>Utgångna</option>
                    <option value="revoked"  <?= ($filter['status'] ?? '') === 'revoked'  ? 'selected' : '' ?>>Återkallade</option>
                </select>
                <select name="type" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla typer</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>" <?= ($filter['type'] ?? '') === $t['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="employee" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">Alla anställda</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= (int) $emp['id'] ?>" <?= ($filter['employee'] ?? '') == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['full_name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <a href="/certificates/create"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nytt certifikat
            </a>
        </div>
    </div>

    <!-- Table -->
    <?php if (empty($certificates)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga certifikat hittades.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Anställd</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Certifikat</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden md:table-cell">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 hidden lg:table-cell">Utfärdat</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Utgår</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Fil</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php
                    $statusBadge = [
                        'active'   => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                        'expiring' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                        'expired'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        'revoked'  => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
                    ];
                    $statusLabel = ['active' => 'Giltig', 'expiring' => 'Utgår snart', 'expired' => 'Utgången', 'revoked' => 'Återkallad'];
                    foreach ($certificates as $c): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['employee_name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['employee_number'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell"><?= htmlspecialchars($c['type'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell"><?= $c['issued_date'] ?? '—' ?></td>
                            <td class="px-4 py-3">
                                <?php if ($c['expiry_date']): ?>
                                    <span class="<?= $c['status'] === 'expired' ? 'text-red-600 font-semibold' : ($c['status'] === 'expiring' ? 'text-yellow-600 font-semibold' : 'text-gray-600 dark:text-gray-400') ?>">
                                        <?= $c['expiry_date'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">Obegränsat</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusBadge[$c['status']] ?? '' ?>">
                                    <?= $statusLabel[$c['status']] ?? $c['status'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($c['file_path']): ?>
                                    <a href="/certificates/<?= (int) $c['id'] ?>/download" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">📎 Visa</a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/certificates/<?= (int) $c['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Redigera</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
