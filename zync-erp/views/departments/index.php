<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Avdelningar</h1>
        <a href="/departments/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny avdelning
        </a>
    </div>

    <?php if (empty($departments)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga avdelningar ännu.
                <a href="/departments/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Skapa den första.</a>
            </p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($departments as $dept): ?>
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Color bar -->
                    <div class="h-2" style="background-color: <?= htmlspecialchars($dept['color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>;"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($dept['name'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <span class="inline-block mt-1 rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                                    <?= htmlspecialchars($dept['code'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <a href="/departments/<?= (int) $dept['id'] ?>/edit"
                                   class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Chef: <?= htmlspecialchars($dept['manager_full_name'] ?? $dept['manager_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php if ($dept['parent_name']): ?>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                Under: <?= htmlspecialchars($dept['parent_name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <?= (int) $dept['user_count'] ?> medarbetare
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
