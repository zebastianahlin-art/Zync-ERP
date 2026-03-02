<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center gap-4">
        <a href="/inventory" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Lagerrörelse</h1>
    </div>

    <form method="POST" action="/inventory/move" class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6 space-y-5">

        <!-- Typ -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Typ av rörelse *</label>
            <div class="flex flex-wrap gap-3">
                <?php foreach ([['in','Inleverans','bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 ring-green-300'],['out','Uttag','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 ring-red-300'],['adjust','Justering','bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 ring-yellow-300']] as [$val,$lbl,$cls]): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="<?= $val ?>" class="peer hidden" <?= ($data['type'] ?? '') === $val ? 'checked' : '' ?>>
                        <span class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium ring-1 ring-gray-300 dark:ring-gray-600 peer-checked:<?= $cls ?> peer-checked:ring-2 transition-colors"><?= $lbl ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($errors['type'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['type'] ?></p><?php endif; ?>
        </div>

        <!-- Artikel -->
        <div>
            <label for="article_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Artikel *</label>
            <select name="article_id" id="article_id" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">— Välj artikel —</option>
                <?php foreach ($articles as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= (int)($data['article_id'] ?? 0) === (int)$a['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['article_number'] . ' — ' . $a['name'], ENT_QUOTES, 'UTF-8') ?> (<?= $a['unit'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['article_id'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['article_id'] ?></p><?php endif; ?>
        </div>

        <!-- Lagerplats -->
        <div>
            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lagerplats *</label>
            <select name="warehouse_id" id="warehouse_id" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">— Välj lagerplats —</option>
                <?php foreach ($warehouses as $w): ?>
                    <option value="<?= $w['id'] ?>" <?= (int)($data['warehouse_id'] ?? 0) === (int)$w['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($w['code'] . ' — ' . $w['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['warehouse_id'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['warehouse_id'] ?></p><?php endif; ?>
        </div>

        <!-- Antal -->
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Antal *</label>
            <input type="number" name="quantity" id="quantity" step="0.001" min="0.001" required
                   value="<?= htmlspecialchars((string)($data['quantity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <?php if (!empty($errors['quantity'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['quantity'] ?></p><?php endif; ?>
        </div>

        <!-- Notering -->
        <div>
            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notering</label>
            <textarea name="note" id="note" rows="2"
                      class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($data['note'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="/inventory" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">Registrera</button>
        </div>
    </form>
</div>
