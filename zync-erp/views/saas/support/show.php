<?php
$priorityColors = [
    'low'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'normal' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'high'   => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400',
    'urgent' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
];
$priorityLabels = ['low' => 'Låg', 'normal' => 'Normal', 'high' => 'Hög', 'urgent' => 'Kritisk'];
$statusColors = [
    'open'        => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'waiting'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
    'resolved'    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'closed'      => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-500',
];
$statusLabels = ['open' => 'Öppen', 'in_progress' => 'Pågår', 'waiting' => 'Väntar', 'resolved' => 'Löst', 'closed' => 'Stängd'];
$categoryLabels = ['bug' => 'Bugg', 'feature_request' => 'Funktionsönskemål', 'question' => 'Fråga', 'billing' => 'Fakturering', 'other' => 'Övrigt'];
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white"><?= htmlspecialchars((string) $ticket['subject'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars((string) $ticket['ticket_number'], ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars((string) ($ticket['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a href="/saas-admin/support" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Support</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Ticket details & comments -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Description -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
                <div class="flex items-center gap-3 mb-3 flex-wrap">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusColors[$ticket['status']] ?? '' ?>">
                        <?= htmlspecialchars($statusLabels[$ticket['status']] ?? $ticket['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityColors[$ticket['priority']] ?? '' ?>">
                        <?= htmlspecialchars($priorityLabels[$ticket['priority']] ?? $ticket['priority'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($categoryLabels[$ticket['category']] ?? $ticket['category'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars((string) ($ticket['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap"><?= htmlspecialchars((string) $ticket['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <!-- Comments -->
            <?php foreach ($ticket['comments'] ?? [] as $comment): ?>
                <div class="rounded-2xl <?= $comment['is_internal'] ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-white dark:bg-gray-800' ?> p-5 shadow-md">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($comment['username'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?></span>
                        <div class="flex items-center gap-2">
                            <?php if ($comment['is_internal']): ?>
                                <span class="text-xs text-yellow-600 dark:text-yellow-400">🔒 Intern</span>
                            <?php endif; ?>
                            <span class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars((string) $comment['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars((string) $comment['comment'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>

            <!-- Add comment -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Lägg till kommentar</h3>
                <form method="POST" action="/saas-admin/support/<?= (int) $ticket['id'] ?>/comment" class="space-y-3">
                    <?= \App\Core\Csrf::field() ?>
                    <textarea name="comment" rows="4" required
                              placeholder="Skriv din kommentar…"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Intern kommentar (ej synlig för kunden)
                        </label>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Skicka</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar: status actions -->
        <div class="space-y-4">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md space-y-3">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ändra status</h2>
                <?php foreach ($statusLabels as $st => $lbl): ?>
                    <?php if ($ticket['status'] !== $st): ?>
                        <form method="POST" action="/saas-admin/support/<?= (int) $ticket['id'] ?>/status">
                            <?= \App\Core\Csrf::field() ?>
                            <input type="hidden" name="status" value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-sm font-medium text-left transition-colors <?= $statusColors[$st] ?? '' ?> hover:opacity-80">
                                → <?= htmlspecialchars($lbl, ENT_QUOTES, 'UTF-8') ?>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Tenant link -->
            <?php if (!empty($ticket['tenant_id'])): ?>
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-md">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Kund</p>
                <a href="/saas-admin/tenants/<?= (int) $ticket['tenant_id'] ?>" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                    <?= htmlspecialchars((string) ($ticket['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> →
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
