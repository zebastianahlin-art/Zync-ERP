<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/equipment/<?= $equipment['id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700">← Tillbaka</a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Dokument – <?= htmlspecialchars($equipment['name']) ?></h1>
        </div>
    </div>
    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Titel</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Filtyp</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Uppladdad</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Åtgärder</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php foreach ($documents as $doc): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($doc['title']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($doc['file_type'] ?? '–') ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($doc['uploaded_at']) ?></td>
                    <td class="px-6 py-4 text-right text-sm">
                        <form method="post" action="/equipment/<?= $equipment['id'] ?>/documents/<?= $doc['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort?')">
                            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Ta bort</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($documents)): ?><tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga dokument.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
