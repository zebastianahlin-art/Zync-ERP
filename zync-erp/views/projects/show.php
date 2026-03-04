<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Projektnr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($project['project_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <a href="/projects/<?= $project['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <?php if (isset($success) && $success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="ml-1 font-medium"><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Kund:</span> <span class="ml-1"><?= htmlspecialchars($project['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Projektledare:</span> <span class="ml-1"><?= htmlspecialchars($project['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Budget:</span> <span class="ml-1"><?= number_format((float) $project['budget'], 0, ',', ' ') ?> kr</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Start:</span> <span class="ml-1"><?= htmlspecialchars($project['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Slut:</span> <span class="ml-1"><?= htmlspecialchars($project['end_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
    </div>

    <!-- Tasks section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white flex items-center justify-between">
            <span>Uppgifter</span>
            <span class="text-xs font-normal text-gray-500 dark:text-gray-400"><?= count($tasks) ?> uppgifter</span>
        </div>
        <?php if (!empty($tasks)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Titel</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Ansvarig</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Deadline</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Prioritet</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($tasks as $task): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($task['assigned_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($task['due_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($task['priority'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($task['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/projects/<?= (int) $project['id'] ?>/tasks/<?= (int) $task['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna uppgift?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga uppgifter ännu.</p>
        <?php endif; ?>

        <!-- Add task form -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Lägg till uppgift</p>
            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/tasks" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-48">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Titel</label>
                    <input type="text" name="title" placeholder="Uppgiftstitel" required
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Deadline</label>
                    <input type="date" name="due_date"
                           class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Prioritet</label>
                    <select name="priority" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="low">Låg</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">Hög</option>
                        <option value="urgent">Brådskande</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="todo" selected>Att göra</option>
                        <option value="in_progress">Pågående</option>
                        <option value="done">Klar</option>
                        <option value="cancelled">Avbruten</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">+ Lägg till</button>
            </form>
        </div>
    </div>

    <!-- Budget section -->
    <?php
        $totalBudgeted = array_sum(array_column($budget, 'budgeted_amount'));
        $totalActual   = array_sum(array_column($budget, 'actual_amount'));
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">Budget</div>
        <?php if (!empty($budget)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Beskrivning</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Budgeterat</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Faktiskt</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($budget as $line): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($line['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float) $line['budgeted_amount'], 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float) $line['actual_amount'], 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/projects/<?= (int) $project['id'] ?>/budget/<?= (int) $line['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna budgetrad?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">Totalt</td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100"><?= number_format($totalBudgeted, 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right <?= $totalActual > $totalBudgeted ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' ?>"><?= number_format($totalActual, 0, ',', ' ') ?> kr</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga budgetrader ännu.</p>
        <?php endif; ?>

        <!-- Add budget line form -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Lägg till budgetrad</p>
            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/budget" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-48">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Beskrivning</label>
                    <input type="text" name="description" placeholder="Beskrivning" required
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Budgeterat (kr)</label>
                    <input type="number" name="budgeted_amount" value="0" min="0" step="0.01"
                           class="w-32 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Faktiskt (kr)</label>
                    <input type="number" name="actual_amount" value="0" min="0" step="0.01"
                           class="w-32 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">+ Lägg till</button>
            </form>
        </div>
    </div>

    <div>
        <a href="/projects" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till projekt</a>
    </div>
</div>
