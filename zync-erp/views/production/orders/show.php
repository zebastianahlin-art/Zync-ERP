<?php
$pct = $order['quantity_planned'] > 0 ? round(($order['quantity_produced'] / $order['quantity_planned']) * 100) : 0;
$statusColors = [
    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'planned' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
    'released' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
];
$statusLabels = [
    'draft' => 'Utkast', 'planned' => 'Planerad', 'released' => 'Frisläppt',
    'in_progress' => 'Pågår', 'completed' => 'Klar', 'cancelled' => 'Avbruten',
];
$nextStatuses = [
    'draft' => ['planned' => 'Planera'],
    'planned' => ['released' => 'Frisläpp'],
    'released' => ['in_progress' => 'Starta produktion'],
    'in_progress' => ['completed' => 'Markera klar', 'cancelled' => 'Avbryt'],
];
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($order['order_number']) ?></h1>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusColors[$order['status']] ?? '' ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?= $order['article_name'] ? htmlspecialchars($order['article_name']) . ' (' . htmlspecialchars($order['article_number'] ?? '') . ')' : 'Ingen artikel vald' ?>
                <?php if ($order['line_name']): ?> · Linje: <?= htmlspecialchars($order['line_name']) ?><?php endif; ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php if (isset($nextStatuses[$order['status']])): ?>
                <?php foreach ($nextStatuses[$order['status']] as $nextStatus => $label): ?>
                <form method="POST" action="/production/orders/<?= $order['id'] ?>/status" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="<?= $nextStatus ?>">
                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium shadow-sm
                        <?= $nextStatus === 'cancelled' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700' ?>"
                        <?= $nextStatus === 'cancelled' ? 'onclick="return confirm(\'Avbryta ordern?\')"' : '' ?>>
                        <?= $label ?>
                    </button>
                </form>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="/production/orders/<?= $order['id'] ?>/edit" class="inline-flex items-center gap-1 rounded-lg bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Redigera</a>
            <a href="/production/orders" class="inline-flex items-center gap-1 rounded-lg bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">← Tillbaka</a>
        </div>
    </div>

    <!-- Info cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Planerat</p>
            <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white"><?= number_format((float)$order['quantity_planned'], 0, ',', ' ') ?> <span class="text-sm font-normal text-gray-400"><?= htmlspecialchars($order['unit']) ?></span></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Producerat</p>
            <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400"><?= number_format((float)$order['quantity_produced'], 0, ',', ' ') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Kasserat</p>
            <p class="mt-1 text-xl font-bold text-red-600 dark:text-red-400"><?= number_format((float)$order['quantity_scrapped'], 0, ',', ' ') ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Framsteg</p>
            <p class="mt-1 text-xl font-bold text-indigo-600 dark:text-indigo-400"><?= $pct ?>%</p>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-2">
                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= min($pct, 100) ?>%"></div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Ansvarig</p>
            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($order['assigned_name'] ?? '—') ?></p>
            <?php if ($order['planned_start']): ?>
            <p class="text-xs text-gray-400 mt-1"><?= date('Y-m-d', strtotime($order['planned_start'])) ?> → <?= $order['planned_end'] ? date('Y-m-d', strtotime($order['planned_end'])) : '?' ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabs: Rapportera | Material | Tid | BOM -->
    <div x-data="{ tab: 'log' }" class="space-y-4">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-4 -mb-px">
                <button @click="tab = 'log'" :class="tab === 'log' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                    Produktionslogg (<?= count($log) ?>)
                </button>
                <button @click="tab = 'material'" :class="tab === 'material' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                    Material (<?= count($materials) ?>)
                </button>
                <button @click="tab = 'time'" :class="tab === 'time' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                    Tid (<?= count($timeLog) ?>)
                </button>
                <button @click="tab = 'bom'" :class="tab === 'bom' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                    BOM (<?= count($bom) ?>)
                </button>
            </nav>
        </div>

        <!-- Produktionslogg -->
        <div x-show="tab === 'log'" class="space-y-4">
            <?php if ($order['status'] === 'in_progress'): ?>
            <form method="POST" action="/production/orders/<?= $order['id'] ?>/log" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Rapportera produktion</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Godkänt antal</label>
                        <input type="number" step="0.001" name="quantity_good" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Kasserat</label>
                        <input type="number" step="0.001" name="quantity_scrap" value="0" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Skift</label>
                        <select name="shift" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="day">Dag</option>
                            <option value="evening">Kväll</option>
                            <option value="night">Natt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Anteckning</label>
                        <input type="text" name="notes" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <button type="submit" class="mt-3 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Rapportera</button>
            </form>
            <?php endif; ?>

            <?php if (!empty($log)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tid</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Godkänt</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kasserat</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Skift</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Av</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Notering</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($log as $entry): ?>
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-700 dark:text-gray-300"><?= date('Y-m-d H:i', strtotime($entry['logged_at'])) ?></td>
                            <td class="px-5 py-3 text-sm text-right text-green-600 dark:text-green-400 font-medium"><?= number_format((float)$entry['quantity_good'], 0, ',', ' ') ?></td>
                            <td class="px-5 py-3 text-sm text-right text-red-600 dark:text-red-400"><?= number_format((float)$entry['quantity_scrap'], 0, ',', ' ') ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= match($entry['shift']) { 'day' => 'Dag', 'evening' => 'Kväll', 'night' => 'Natt', default => '—' } ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($entry['logged_by_name'] ?? '—') ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($entry['notes'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Ingen produktion rapporterad ännu.</p>
            <?php endif; ?>
        </div>

        <!-- Material -->
        <div x-show="tab === 'material'" class="space-y-4">
            <?php if (in_array($order['status'], ['in_progress', 'released'])): ?>
            <form method="POST" action="/production/orders/<?= $order['id'] ?>/materials" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Registrera materialförbrukning</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Artikel</label>
                        <select name="article_id" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Välj —</option>
                            <?php foreach ($articles as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Antal</label>
                        <input type="number" step="0.001" name="quantity_used" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Lager</label>
                        <select name="warehouse_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Välj —</option>
                            <?php foreach ($warehouses as $w): ?>
                            <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Lägg till</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <?php if (!empty($materials)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Artikel</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Använt</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lager</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($materials as $m): ?>
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($m['article_name']) ?> <span class="text-xs text-gray-400">(<?= htmlspecialchars($m['article_number']) ?>)</span></td>
                            <td class="px-5 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$m['quantity_used'], 2, ',', ' ') ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($m['warehouse_name'] ?? '—') ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= date('Y-m-d H:i', strtotime($m['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Inget material registrerat.</p>
            <?php endif; ?>
        </div>

        <!-- Tid -->
        <div x-show="tab === 'time'" class="space-y-4">
            <?php if (in_array($order['status'], ['in_progress'])): ?>
            <form method="POST" action="/production/orders/<?= $order['id'] ?>/time" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Registrera tid</h3>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Personal</label>
                        <select name="employee_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Välj —</option>
                            <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Start</label>
                        <input type="datetime-local" name="start_time" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Slut</label>
                        <input type="datetime-local" name="end_time" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Rast (min)</label>
                        <input type="number" name="break_minutes" value="0" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Lägg till</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <?php if (!empty($timeLog)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Personal</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Start</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Slut</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Timmar</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Beskrivning</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($timeLog as $t): ?>
                        <?php
                            $hours = '—';
                            if ($t['end_time']) {
                                $diff = (strtotime($t['end_time']) - strtotime($t['start_time'])) / 3600;
                                $diff -= ($t['break_minutes'] ?? 0) / 60;
                                $hours = number_format(max(0, $diff), 1, ',', '') . 'h';
                            }
                        ?>
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($t['employee_name'] ?? '—') ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= date('Y-m-d H:i', strtotime($t['start_time'])) ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= $t['end_time'] ? date('Y-m-d H:i', strtotime($t['end_time'])) : 'Pågår' ?></td>
                            <td class="px-5 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= $hours ?></td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($t['description'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Ingen tid registrerad.</p>
            <?php endif; ?>
        </div>

        <!-- BOM -->
        <div x-show="tab === 'bom'" class="space-y-4">
            <?php if ($order['article_id']): ?>
            <form method="POST" action="/production/bom/<?= $order['article_id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <input type="hidden" name="return_order_id" value="<?= $order['id'] ?>">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Lägg till materialrad (BOM)</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Råmaterial</label>
                        <select name="material_article_id" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Välj —</option>
                            <?php foreach ($articles as $a): ?>
                            <?php if ($a['id'] != $order['article_id']): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Antal per enhet</label>
                        <input type="number" step="0.0001" name="quantity_per_unit" value="1" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Enhet</label>
                        <input type="text" name="unit" value="st" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Lägg till</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <?php if (!empty($bom)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Material</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Antal/enhet</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Totalt behov</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($bom as $b): ?>
                        <tr>
                            <td class="px-5 py-3 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($b['material_name']) ?> <span class="text-xs text-gray-400">(<?= htmlspecialchars($b['article_number']) ?>)</span></td>
                            <td class="px-5 py-3 text-sm text-right text-gray-700 dark:text-gray-300"><?= number_format((float)$b['quantity_per_unit'], 4, ',', '') ?> <?= htmlspecialchars($b['material_unit'] ?? 'st') ?></td>
                            <td class="px-5 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$b['quantity_per_unit'] * (float)$order['quantity_planned'], 2, ',', ' ') ?></td>
                            <td class="px-5 py-3 text-sm text-right">
                                <form method="POST" action="/production/bom/<?= $b['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="return_order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif ($order['article_id']): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Ingen BOM definierad för denna artikel.</p>
            <?php else: ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">Välj en artikel för att hantera BOM.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete -->
    <?php if (in_array($order['status'], ['draft', 'cancelled'])): ?>
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
        <form method="POST" action="/production/orders/<?= $order['id'] ?>/delete" onsubmit="return confirm('Radera denna order permanent?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Radera order</button>
        </form>
    </div>
    <?php endif; ?>
</div>
