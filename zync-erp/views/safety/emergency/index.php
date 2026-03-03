<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Krishantering</h1>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Contacts -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Nödkontakter</h2>
                <a href="/safety/emergency/contacts/create" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">+ Ny kontakt</a>
            </div>
            <?php if (empty($contacts)): ?>
                <p class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga kontakter ännu.</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($contacts as $c): ?>
                        <li class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($c['role'] ?? '', ENT_QUOTES, 'UTF-8') ?> <?= !empty($c['phone']) ? '· ' . htmlspecialchars($c['phone'], ENT_QUOTES, 'UTF-8') : '' ?></p>
                            </div>
                            <a href="/safety/emergency/contacts/<?= (int) $c['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700">
                    <a href="/safety/emergency/contacts" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla kontakter &rarr;</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Procedures -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Nödprocedurer</h2>
                <a href="/safety/emergency/procedures/create" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition-colors">+ Ny procedur</a>
            </div>
            <?php if (empty($procedures)): ?>
                <p class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Inga procedurer ännu.</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php foreach ($procedures as $p): ?>
                        <li class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($p['category'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <a href="/safety/emergency/procedures/<?= (int) $p['id'] ?>" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Visa</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700">
                    <a href="/safety/emergency/procedures" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Visa alla procedurer &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
