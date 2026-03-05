<?php
$totalPages = $total > 0 ? (int) ceil($total / 50) : 1;
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Audit-logg</h1>
        <a href="/admin" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Admin</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/admin/audit-log" class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-md">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
            <input type="text" name="module" value="<?= htmlspecialchars((string) ($filters['module'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Modul"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <input type="text" name="action" value="<?= htmlspecialchars((string) ($filters['action'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   placeholder="Åtgärd"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <input type="date" name="date_from" value="<?= htmlspecialchars((string) ($filters['date_from'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <input type="date" name="date_to" value="<?= htmlspecialchars((string) ($filters['date_to'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                Filtrera
            </button>
        </div>
    </form>

    <!-- Clear log button -->
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">Visar <?= count($rows) ?> av <?= $total ?> poster</p>
        <div x-data="{ open: false }">
            <button @click="open = true" class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                Rensa gammal logg
            </button>
            <div x-show="open" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rensa audit-logg</h3>
                    <form method="POST" action="/admin/audit-log/clear">
                        <?= \App\Core\Csrf::field() ?>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ta bort poster äldre än (dagar)</label>
                            <input type="number" name="days" value="365" min="1"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Avbryt</button>
                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">Rensa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Log table -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Tid</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Användare</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Modul</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärd</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Tabell/ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Inga loggposter hittades.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs"><?= htmlspecialchars((string) ($row['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($row['username'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400"><?= htmlspecialchars((string) ($row['module'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($row['action'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs">
                                <?= htmlspecialchars((string) ($row['table_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                <?php if (!empty($row['record_id'])): ?>
                                    #<?= (int) $row['record_id'] ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars((string) ($row['ip_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-1">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
                $q = http_build_query(array_merge($filters, ['page' => $p]));
                ?>
                <a href="/admin/audit-log?<?= $q ?>"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors <?= $p === $page ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>
