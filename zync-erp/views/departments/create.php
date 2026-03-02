<div class="mx-auto max-w-2xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Skapa avdelning</h1>
        <a href="/departments" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/departments" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Namn <span class="text-red-500">*</span>
                </label>
                <input id="name" name="name" type="text" required
                       value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                <?php if (isset($errors['name'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kod <span class="text-red-500">*</span>
                </label>
                <input id="code" name="code" type="text" required maxlength="20"
                       value="<?= htmlspecialchars($old['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="T.ex. PROD, SALES, IT"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['code']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white uppercase">
                <?php if (isset($errors['code'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['code'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php else: ?>
                    <p class="mt-1 text-xs text-gray-400">Unik kort kod, max 20 tecken.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelningschef</label>
                <select id="manager_id" name="manager_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">– Ingen chef –</option>
                    <?php foreach ($managers as $m): ?>
                        <option value="<?= (int) $m['id'] ?>"
                            <?= (string) ($old['manager_id'] ?? '') === (string) $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['full_name'] ?? $m['username'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Överordnad avdelning</label>
                <select id="parent_id" name="parent_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">– Ingen (toppnivå) –</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= (int) $d['id'] ?>"
                            <?= (string) ($old['parent_id'] ?? '') === (string) $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Färg</label>
                <div class="mt-1 flex items-center gap-3">
                    <input id="color" name="color" type="color"
                           value="<?= htmlspecialchars($old['color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>"
                           class="h-10 w-14 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                    <span class="text-xs text-gray-400">Visas som färgmarkering i listor.</span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/departments"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    Avbryt
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Skapa avdelning
                </button>
            </div>
        </form>
    </div>
</div>
