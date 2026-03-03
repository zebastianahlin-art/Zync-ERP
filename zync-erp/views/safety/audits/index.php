<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Audits</h1>
        <a href="/safety/audits/create" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">+ Ny audit</a>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($audits)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Inga audits ännu. <a href="/safety/audits/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Titel</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Planerat datum</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Ansvarig</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Mall</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($audits as $a): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <a href="/safety/audits/<?= (int) $a['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400"><?= htmlspecialchars($a['title'], ENT_QUOTES, 'UTF-8') ?></a>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['scheduled_date'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/40 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400"><?= htmlspecialchars($a['status'] ?? '–', ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['assigned_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?= htmlspecialchars($a['template_name'] ?? '–', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/safety/audits/<?= (int) $a['id'] ?>/edit" class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                                <form method="POST" action="/safety/audits/<?= (int) $a['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort denna audit?')">
                                    <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
