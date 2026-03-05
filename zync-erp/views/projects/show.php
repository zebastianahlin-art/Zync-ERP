<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <?php if (($project['project_type'] ?? 'internal') === 'external'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Externt</span>
                <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Internt</span>
                <?php endif; ?>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Projektnr: <span class="font-mono text-indigo-600 dark:text-indigo-400"><?= htmlspecialchars($project['project_number'], ENT_QUOTES, 'UTF-8') ?></span></p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="/projects/<?= $project['id'] ?>/kanban" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">Kanban</a>
            <a href="/projects/<?= $project['id'] ?>/report" target="_blank" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition">&#128196; Ladda ner rapport</a>
            <a href="/projects/<?= $project['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
        </div>
    </div>

    <?php if (isset($success) && $success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><span class="text-gray-500 dark:text-gray-400">Status:</span> <span class="ml-1 font-medium"><?= htmlspecialchars($project['status'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Kund:</span> <span class="ml-1"><?= htmlspecialchars($project['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Projektledare:</span> <span class="ml-1"><?= htmlspecialchars($project['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Budget:</span> <span class="ml-1"><?= number_format((float) $project['budget'], 0, ',', ' ') ?> kr</span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Start:</span> <span class="ml-1"><?= htmlspecialchars($project['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
        <div><span class="text-gray-500 dark:text-gray-400">Slut:</span> <span class="ml-1"><?= htmlspecialchars($project['end_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span></div>
    </div>

    <!-- C6: Budget-widget -->
    <?php
        $planned = (float) ($project['planned_budget'] ?? $project['budget'] ?? 0);
        $actual  = (float) ($project['actual_cost'] ?? 0);
        $pctRaw  = $planned > 0 ? ($actual / $planned) * 100 : 0;
        $pctBar  = min($pctRaw, 100); // cap at 100 for bar width
        $overBudget = $actual > $planned;
        $nearBudget = !$overBudget && $planned > 0 && $actual > $planned * 0.8;
        $barColor  = $overBudget ? 'bg-red-500' : ($nearBudget ? 'bg-yellow-400' : 'bg-green-500');
        $textColor = $overBudget ? 'text-red-600 dark:text-red-400' : ($nearBudget ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400');
    ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6" id="costs">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Budgetöversikt</h2>
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-gray-600 dark:text-gray-400">Faktisk kostnad</span>
            <span class="<?= $textColor ?> font-semibold"><?= number_format($actual, 0, ',', ' ') ?> kr av <?= number_format($planned, 0, ',', ' ') ?> kr planerat</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
            <div class="<?= $barColor ?> h-3 rounded-full transition-all" style="width: <?= $pctBar ?>%"></div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1"><?= round($pctRaw, 1) ?>% av planerad budget förbrukad<?= $overBudget ? ' &mdash; <strong class="text-red-600 dark:text-red-400">Budgetöverskridning!</strong>' : '' ?></p>

        <!-- Add cost form -->
        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Lägg till kostnad</p>
            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/costs" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-40">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Beskrivning</label>
                    <input type="text" name="description" placeholder="Kostnadsbeskrivning" required
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Belopp (kr)</label>
                    <input type="number" name="amount" placeholder="0.00" min="0" step="0.01" required
                           class="w-32 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Datum</label>
                    <input type="date" name="cost_date"
                           class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Kategori</label>
                    <select name="category" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">— Välj —</option>
                        <option value="Personal">Personal</option>
                        <option value="Material">Material</option>
                        <option value="Utrustning">Utrustning</option>
                        <option value="Resor">Resor</option>
                        <option value="Konsulter">Konsulter</option>
                        <option value="Övrigt">Övrigt</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">+ Lägg till</button>
            </form>
        </div>

        <?php if (!empty($costs)): ?>
        <div class="mt-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Beskrivning</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Kategori</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Datum</th>
                        <th class="px-3 py-2 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Belopp</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($costs as $cost): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-3 py-2 text-gray-900 dark:text-gray-100"><?= htmlspecialchars($cost['description'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($cost['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400"><?= htmlspecialchars($cost['cost_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-3 py-2 text-right text-gray-900 dark:text-gray-100"><?= number_format((float) $cost['amount'], 0, ',', ' ') ?> kr</td>
                        <td class="px-3 py-2 text-right">
                            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/costs/<?= (int) $cost['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort kostnad?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tasks section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white flex items-center justify-between">
            <span>Uppgifter</span>
            <div class="flex items-center gap-3">
                <span class="text-xs font-normal text-gray-500 dark:text-gray-400"><?= count($tasks) ?> uppgifter</span>
                <a href="/projects/<?= $project['id'] ?>/kanban" class="text-xs text-purple-600 dark:text-purple-400 hover:underline">Kanban-vy &rarr;</a>
            </div>
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

    <!-- C2: Stakeholders -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden" id="stakeholders">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white flex items-center justify-between">
            <span>Intressenter</span>
            <span class="text-xs font-normal text-gray-500 dark:text-gray-400"><?= count($stakeholders) ?> intressenter</span>
        </div>
        <?php if (!empty($stakeholders)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Namn</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Roll</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">E-post</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Telefon</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anteckningar</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($stakeholders as $sh): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($sh['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($sh['role'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($sh['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($sh['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($sh['notes'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/projects/<?= (int) $project['id'] ?>/stakeholders/<?= (int) $sh['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort intressent?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga intressenter registrerade ännu.</p>
        <?php endif; ?>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Lägg till intressent</p>
            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/stakeholders" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-36">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Namn</label>
                    <input type="text" name="name" placeholder="Fullständigt namn" required
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Roll</label>
                    <select name="role" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="Sponsor">Sponsor</option>
                        <option value="Kund">Kund</option>
                        <option value="Projektledare">Projektledare</option>
                        <option value="Teammedlem" selected>Teammedlem</option>
                        <option value="Extern konsult">Extern konsult</option>
                        <option value="Intressent">Intressent</option>
                        <option value="Beslutsfattare">Beslutsfattare</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">E-post</label>
                    <input type="email" name="email" placeholder="e-post"
                           class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Telefon</label>
                    <input type="text" name="phone" placeholder="telefon"
                           class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">+ Lägg till</button>
            </form>
        </div>
    </div>

    <!-- C3: Kopplade inköpsordrar -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden" id="purchases">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white flex items-center justify-between">
            <span>Kopplade inköpsordrar</span>
            <?php $poTotal = array_sum(array_column($linkedPOs, 'total_amount')); ?>
            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Totalt: <?= number_format($poTotal, 0, ',', ' ') ?> kr</span>
        </div>
        <?php if (!empty($linkedPOs)): ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Ordernr</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Leverantör</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Status</th>
                    <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 uppercase text-xs">Belopp</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs">Anteckningar</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($linkedPOs as $po): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                        <a href="/purchasing/orders/<?= $po['id'] ?>" class="hover:underline"><?= htmlspecialchars($po['order_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($po['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($po['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100"><?= number_format((float) $po['total_amount'], 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($po['link_notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/projects/<?= (int) $project['id'] ?>/purchase-orders/<?= (int) $po['link_id'] ?>/delete" class="inline" onsubmit="return confirm('Koppla bort inköpsordern?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer text-xs">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">Inga inköpsordrar kopplade ännu.</p>
        <?php endif; ?>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-3">Koppla inköpsorder</p>
            <form method="POST" action="/projects/<?= (int) $project['id'] ?>/purchase-orders" class="flex flex-wrap gap-3 items-end">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1 min-w-48">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Inköpsorder</label>
                    <select name="purchase_order_id" required class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">— Välj inköpsorder —</option>
                        <?php foreach ($allPOs as $po): ?>
                        <option value="<?= $po['id'] ?>"><?= htmlspecialchars(($po['order_number'] ?? 'PO-' . $po['id']) . ' – ' . ($po['supplier_name'] ?? '') . ' (' . number_format((float)$po['total_amount'], 0, ',', ' ') . ' kr)', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-1 min-w-32">
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Anteckningar</label>
                    <input type="text" name="notes" placeholder="Valfri anteckning"
                           class="w-full rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">Koppla</button>
            </form>
        </div>
    </div>

    <!-- Budget section -->
    <?php
        $totalBudgeted = array_sum(array_column($budget, 'budgeted'));
        $totalActual   = array_sum(array_column($budget, 'actual'));
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
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float) $line['budgeted'], 0, ',', ' ') ?> kr</td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400"><?= number_format((float) $line['actual'], 0, ',', ' ') ?> kr</td>
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
