<div class="mx-auto max-w-2xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Redigera avdelning</h1>
        <a href="/departments" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/departments/<?= (int) $department['id'] ?>" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Namn <span class="text-red-500">*</span>
                </label>
                <input id="name" name="name" type="text" required
                       value="<?= htmlspecialchars($department['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
                       value="<?= htmlspecialchars($department['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['code']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white uppercase">
                <?php if (isset($errors['code'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['code'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelningschef</label>
                <select id="manager_id" name="manager_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">– Ingen chef –</option>
                    <?php foreach ($managers as $m): ?>
                        <option value="<?= (int) $m['id'] ?>"
                            <?= (string) ($department['manager_id'] ?? '') === (string) $m['id'] ? 'selected' : '' ?>>
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
                            <?= (string) ($department['parent_id'] ?? '') === (string) $d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Färg</label>
                <div class="mt-1 flex items-center gap-3">
                    <input id="color" name="color" type="color"
                           value="<?= htmlspecialchars($department['color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>"
                           class="h-10 w-14 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/departments"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    Avbryt
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Spara ändringar
                </button>
            </div>
        </form>
    </div>

    <!-- Members -->
    <?php if (!empty($members)): ?>
    <div class="mt-8 rounded-2xl bg-white dark:bg-gray-800 shadow-md ring-1 ring-gray-200 dark:ring-gray-700">
        <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Medarbetare (<?= count($members) ?>)</h2>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php foreach ($members as $m): ?>
                <li class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($m['full_name'] ?? $m['username'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($m['role_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            · <?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                    <a href="/admin/users/<?= (int) $m['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Delete -->
    <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6">
        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Radera avdelning</h3>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">Denna åtgärd kan inte ångras.</p>
        <form method="POST" action="/departments/<?= (int) $department['id'] ?>/delete" class="mt-3"
              onsubmit="return confirm('Är du säker på att du vill ta bort denna avdelning?');">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors">
                Radera
            </button>
        </form>
    </div>
</div>
