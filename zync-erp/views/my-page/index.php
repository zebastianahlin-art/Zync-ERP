<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Min Sida</h1>
        <a href="/my-page/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera profil</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                <?= htmlspecialchars(mb_strtoupper(mb_substr($user['full_name'] ?? $user['username'] ?? 'U', 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <?= htmlspecialchars($user['full_name'] ?? $user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($user['role_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700">

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Användarnamn</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($user['username'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">E-post</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($user['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Telefon</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($user['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Avdelning</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($user['department_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Konto skapat</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars(substr($user['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">2FA</dt>
                <dd class="mt-1 text-sm">
                    <?php if ($user['totp_enabled'] ?? false): ?>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Aktiverat</span>
                    <?php else: ?>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">Ej aktiverat</span>
                        <a href="/2fa/setup" class="ml-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Aktivera</a>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>
    </div>
</div>
