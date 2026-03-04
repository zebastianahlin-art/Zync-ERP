<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100"><?= htmlspecialchars($report['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Nr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($report['report_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <div class="flex gap-2">
            <?php if ($report['status'] === 'draft'): ?>
                <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/submit">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Skicka in</button>
                </form>
            <?php endif; ?>
            <?php if ($report['status'] === 'submitted'): ?>
                <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/approve">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">Godkänn</button>
                </form>
                <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/reject">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Avslå</button>
                </form>
            <?php endif; ?>
            <a href="/hr/expenses/<?= (int) $report['id'] ?>/edit" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Redigera</a>
        </div>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-2">Rapportdetaljer</h2>
            <?php
                $sc = ['draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400', 'submitted' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400', 'approved' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400', 'rejected' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400', 'paid' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400'];
            ?>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium <?= $sc[$report['status']] ?? '' ?>"><?= htmlspecialchars($report['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Anställd:</span> <span><?= htmlspecialchars($report['employee_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Destination:</span> <span><?= htmlspecialchars($report['destination'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Syfte:</span> <span><?= htmlspecialchars($report['purpose'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Resa:</span> <span><?= htmlspecialchars($report['trip_start'] ?? '–', ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($report['trip_end'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-3 text-sm">
            <h2 class="font-semibold text-gray-900 dark:text-gray-100 text-base mb-2">Sammanfattning</h2>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Totalt belopp:</span> <span class="font-bold text-xl text-gray-900 dark:text-gray-100"><?= number_format((float) $report['total_amount'], 2, ',', ' ') ?> <?= htmlspecialchars($report['currency'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php if ($report['approved_by']): ?>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Godkänd av:</span> <span><?= htmlspecialchars($report['approved_by_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Godkänd:</span> <span><?= htmlspecialchars(substr($report['approved_at'] ?? '–', 0, 10), ENT_QUOTES, 'UTF-8') ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lines table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Utgiftsrader</div>
        <?php if (!empty($lines)): ?>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Datum</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kategori</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Beskrivning</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Belopp</th>
                    <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($lines as $line): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($line['expense_date'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($line['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-6 py-4 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($line['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100"><?= number_format((float) $line['amount'], 2, ',', ' ') ?> <?= htmlspecialchars($line['currency'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/lines/<?= (int) $line['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna rad?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga utgiftsrader ännu.</p>
        <?php endif; ?>

        <!-- Add line form -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Lägg till rad</p>
            <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/lines" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Datum</label>
                    <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required
                           class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Kategori</label>
                    <select name="category" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <?php foreach (['travel' => 'Resa', 'accommodation' => 'Boende', 'meals' => 'Måltider', 'fuel' => 'Bränsle', 'parking' => 'Parkering', 'taxi' => 'Taxi', 'other' => 'Övrigt'] as $val => $label): ?>
                            <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-1 min-w-40">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Beskrivning</label>
                    <input type="text" name="description" placeholder="Beskrivning" required
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Belopp</label>
                    <input type="number" name="amount" value="0" min="0" step="0.01" required
                           class="w-28 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">+ Lägg till</button>
            </form>
        </div>
    </div>

    <div class="flex gap-4">
        <a href="/hr/expenses" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till reseräkningar</a>
        <form method="POST" action="/hr/expenses/<?= (int) $report['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna reseräkning?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer">Ta bort</button>
        </form>
    </div>
</div>
