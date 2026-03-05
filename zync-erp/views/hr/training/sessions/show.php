<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/training/sessions" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Kurstillfällen</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($session['course_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <?= htmlspecialchars($session['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($session['location'])): ?>&bull; <?= htmlspecialchars($session['location'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/hr/training/sessions/<?= (int)$session['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/training/sessions/<?= (int)$session['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort kurstillfället?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Startdatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($session['start_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Slutdatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($session['end_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tränare</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($session['trainer'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Max deltagare</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars((string)($session['max_participants'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Registrerade</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= (int)($session['participant_count'] ?? 0) ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($session['status'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
    </div>

    <!-- Deltagare -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">Deltagare</h3>
        </div>

        <!-- Registrera deltagare -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700">
            <form method="POST" action="/hr/training/sessions/<?= (int)$session['id'] ?>/participants" class="flex items-end gap-3">
                <?= \App\Core\Csrf::field() ?>
                <div class="flex-1">
                    <label for="employee_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Lägg till deltagare</label>
                    <select id="employee_id" name="employee_id"
                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">— Välj anställd —</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= (int)$emp['id'] ?>"><?= htmlspecialchars($emp['last_name'] . ', ' . $emp['first_name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Registrera</button>
            </form>
        </div>

        <?php if (empty($participants)): ?>
        <p class="px-4 py-6 text-center text-gray-400 dark:text-gray-500 text-sm">Inga deltagare registrerade</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Anställd</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($participants as $p): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                        <?= htmlspecialchars(($p['last_name'] ?? '') . ', ' . ($p['first_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($p['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <?php if (($p['status'] ?? '') !== 'completed'): ?>
                        <form method="POST" action="/hr/training/sessions/<?= (int)$session['id'] ?>/participants/<?= (int)$p['id'] ?>/complete" class="inline">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-xs text-green-600 dark:text-green-400 hover:underline mr-2">Markera klar</button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" action="/hr/training/sessions/<?= (int)$session['id'] ?>/participants/<?= (int)$p['id'] ?>/remove" class="inline" onsubmit="return confirm('Ta bort deltagare?')">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
