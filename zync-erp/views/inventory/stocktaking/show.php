<?php
$statusLabels = [
    'draft'       => 'Utkast',
    'in_progress' => 'Pågående',
    'completed'   => 'Slutförd',
    'approved'    => 'Godkänd',
];
$statusBadgeClasses = [
    'draft'       => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
    'completed'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
    'approved'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
];
$status = $stocktaking['status'] ?? 'draft';
$canCount   = in_array($status, ['draft', 'in_progress'], true);
$canApprove = $status === 'completed';
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            <?= htmlspecialchars($stocktaking['name'] ?? 'Inventering', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <a href="/inventory/stocktaking"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            ← Tillbaka
        </a>
    </div>

    <!-- Details card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lagerställe</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($stocktaking['warehouse_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= htmlspecialchars($statusBadgeClasses[$status] ?? 'bg-gray-100 text-gray-700', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($statusLabels[$status] ?? $status, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Startdatum</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($stocktaking['started_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Slutförd</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($stocktaking['completed_at'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <?php if ($status === 'approved'): ?>
            <div class="col-span-2 sm:col-span-4">
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Godkänd av</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= htmlspecialchars($stocktaking['approved_by_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                </dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>

    <!-- Flash messages -->
    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- Lines table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Inventeringsrader</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Artikel</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Art.nr</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Systemmängd</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Räknad mängd</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Differens</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Noteringar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($lines as $line): ?>
                    <?php
                    $counted = $line['counted_quantity'] ?? null;
                    $system  = $line['system_quantity'] ?? 0;
                    if ($counted !== null) {
                        $diff = (float) $counted - (float) $system;
                        if (abs($diff) < 0.001) {
                            $diffClass = 'text-green-600 dark:text-green-400';
                            $diffText  = '0';
                        } elseif ($diff < 0) {
                            $diffClass = 'text-red-600 dark:text-red-400';
                            $diffText  = number_format($diff, 2, ',', ' ');
                        } else {
                            $diffClass = 'text-orange-600 dark:text-orange-400';
                            $diffText  = '+' . number_format($diff, 2, ',', ' ');
                        }
                    }
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($line['article_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                            <?= htmlspecialchars($line['article_number'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars((string) $system, ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-semibold">
                            <?= $counted !== null ? htmlspecialchars((string) $counted, ENT_QUOTES, 'UTF-8') : '—' ?>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold <?= $counted !== null ? htmlspecialchars($diffClass, ENT_QUOTES, 'UTF-8') : 'text-gray-400' ?>">
                            <?= $counted !== null ? htmlspecialchars($diffText, ENT_QUOTES, 'UTF-8') : '—' ?>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($line['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lines)): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Inga inventeringsrader</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add count form (draft or in_progress) -->
    <?php if ($canCount): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Lägg till räkning</h2>
        <form method="POST" action="/inventory/stocktaking/<?= htmlspecialchars((string) $stocktaking['id'], ENT_QUOTES, 'UTF-8') ?>/count" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Artikel <span class="text-red-500">*</span></label>
                    <select name="article_id" required
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— Välj artikel —</option>
                        <?php foreach ($articles as $article): ?>
                        <option value="<?= htmlspecialchars((string) $article['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars(($article['article_number'] ?? '') . ' – ' . ($article['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Räknad mängd <span class="text-red-500">*</span></label>
                    <input type="number" name="counted_quantity" required step="0.01" min="0"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Noteringar</label>
                    <input type="text" name="notes"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>
            <div>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Spara räkning
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Approve button (completed status) -->
    <?php if ($canApprove): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Godkänn inventering</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            När du godkänner inventeringen låses den och lagersaldona justeras utifrån räknade värden.
        </p>
        <form method="POST" action="/inventory/stocktaking/<?= htmlspecialchars((string) $stocktaking['id'], ENT_QUOTES, 'UTF-8') ?>/approve"
              onsubmit="return confirm('Är du säker på att du vill godkänna inventeringen? Lagersaldona kommer att uppdateras.')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit"
                    class="rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                Godkänn inventering
            </button>
        </form>
    </div>
    <?php endif; ?>
</div>
