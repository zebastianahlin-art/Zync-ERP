<div class="mx-auto max-w-2xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Redigera användare</h1>
        <a href="/admin/users" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/admin/users/<?= (int) $user['id'] ?>" class="space-y-5">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Användarnamn <span class="text-red-500">*</span>
                </label>
                <input id="username" name="username" type="text" required
                       value="<?= htmlspecialchars((string) ($user['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['username']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                <?php if (isset($errors['username'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['username'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    E-postadress <span class="text-red-500">*</span>
                </label>
                <input id="email" name="email" type="email" required
                       value="<?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lösenord</label>
                <input id="password" name="password" type="password" minlength="8"
                       class="mt-1 block w-full rounded-lg border <?= isset($errors['password']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                <?php if (isset($errors['password'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php else: ?>
                    <p class="mt-1 text-xs text-gray-400">Lämna tomt för att behålla nuvarande lösenord.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Roll <span class="text-red-500">*</span>
                </label>
                <select id="role_id" name="role_id" required
                        class="mt-1 block w-full rounded-lg border <?= isset($errors['role_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">– Välj roll –</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= (int) $role['id'] ?>"
                            <?= (string) ($user['role_id'] ?? '') === (string) $role['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?>
                            (nivå <?= (int) $role['level'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['role_id'])): ?>
                    <p class="mt-1 text-xs text-red-600"><?= htmlspecialchars($errors['role_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avdelning</label>
                <select id="department_id" name="department_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">– Ingen avdelning –</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= (int) $dept['id'] ?>"
                            <?= (string) ($user['department_id'] ?? '') === (string) $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/admin/users"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Avbryt
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Spara ändringar
                </button>
            </div>

        </form>
    </div>

</div>
