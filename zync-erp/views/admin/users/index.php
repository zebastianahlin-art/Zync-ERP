<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Användare</h1>
        <a href="/admin/users/create"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            + Ny användare
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($users)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                Inga användare hittades.
                <a href="/admin/users/create" class="text-indigo-600 hover:underline">Skapa den första.</a>
            </p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Användarnamn</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">E-post</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Roll</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Avdelning</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Aktiv</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400"><?= (int) $user['id'] ?></td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars((string) $user['username'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars((string) ($user['role_name'] ?? '–'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars((string) ($user['department_name'] ?? '–'), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($user['is_active']): ?>
                                    <span class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Ja</span>
                                <?php else: ?>
                                    <span class="inline-block rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Nej</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="/admin/users/<?= (int) $user['id'] ?>/edit"
                                   class="text-indigo-600 hover:underline">Redigera</a>
                                <form method="POST" action="/admin/users/<?= (int) $user['id'] ?>/toggle" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                    <button type="submit"
                                            class="<?= $user['is_active'] ? 'text-red-600' : 'text-green-600' ?> hover:underline bg-transparent border-0 p-0 cursor-pointer">
                                        <?= $user['is_active'] ? 'Inaktivera' : 'Aktivera' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
