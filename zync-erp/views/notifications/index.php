<?php

declare(strict_types=1);

/**
 * @var array<int, array<string, mixed>> $notifications
 * @var string $title
 * @var array<int, array{label: string, url?: string}> $breadcrumbs
 */
?>
<?php include dirname(__DIR__) . '/partials/breadcrumbs.php'; ?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifikationer</h1>
    <?php if (!empty($notifications)): ?>
    <form method="POST" action="/notifications/read-all">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors">
            Markera alla som lästa
        </button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
    <div class="text-center py-16 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p class="text-sm">Inga notifikationer ännu.</p>
    </div>
<?php else: ?>
    <div class="space-y-2">
        <?php foreach ($notifications as $notif): ?>
            <div class="flex items-start gap-4 rounded-xl border p-4 transition-colors
                <?= $notif['is_read'] ? 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' : 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700' ?>">
                <div class="mt-0.5 flex-shrink-0">
                    <?php if (!$notif['is_read']): ?>
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                    <?php else: ?>
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        <?= htmlspecialchars($notif['title'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php if (!empty($notif['message'])): ?>
                        <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($notif['message'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        <?= htmlspecialchars($notif['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        · <?= htmlspecialchars($notif['type'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <?php if (!$notif['is_read']): ?>
                <form method="POST" action="/notifications/<?= (int) $notif['id'] ?>/read">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit"
                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline whitespace-nowrap">
                        Markera som läst
                    </button>
                </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
