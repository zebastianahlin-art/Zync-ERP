<?php
$categoryMap = [
    'complaint' => 'Klagomål',
    'inquiry'   => 'Förfrågan',
    'return'    => 'Retur',
    'warranty'  => 'Garanti',
    'support'   => 'Support',
    'other'     => 'Övrigt',
];
$priorityMap = [
    'low'    => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Låg'],
    'normal' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'Normal'],
    'high'   => ['bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400', 'Hög'],
    'urgent' => ['bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'Brådskande'],
];
$statusMap = [
    'open'             => ['bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'Öppet'],
    'in_progress'      => ['bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400', 'Pågående'],
    'waiting_customer' => ['bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'Väntar kund'],
    'waiting_internal' => ['bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400', 'Väntar internt'],
    'resolved'         => ['bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400', 'Löst'],
    'closed'           => ['bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', 'Stängt'],
];
$transitions = [
    'open'             => ['in_progress' => 'Starta', 'waiting_customer' => 'Väntar kund', 'waiting_internal' => 'Väntar internt', 'closed' => 'Stäng'],
    'in_progress'      => ['waiting_customer' => 'Väntar kund', 'waiting_internal' => 'Väntar internt', 'resolved' => 'Lös', 'closed' => 'Stäng'],
    'waiting_customer' => ['in_progress' => 'Återuppta', 'resolved' => 'Lös', 'closed' => 'Stäng'],
    'waiting_internal' => ['in_progress' => 'Återuppta', 'resolved' => 'Lös', 'closed' => 'Stäng'],
    'resolved'         => ['closed' => 'Stäng', 'open' => 'Återöppna'],
    'closed'           => ['open' => 'Återöppna'],
];
$p = $priorityMap[$ticket['priority']] ?? ['bg-gray-100 text-gray-600', $ticket['priority']];
$s = $statusMap[$ticket['status']] ?? ['bg-gray-100 text-gray-600', $ticket['status']];
$nextStatuses = $transitions[$ticket['status']] ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['ticket_number'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm"><?= htmlspecialchars($ticket['title'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="mt-2 flex gap-2">
                <span class="px-2 py-0.5 rounded text-xs <?= $s[0] ?>"><?= $s[1] ?></span>
                <span class="px-2 py-0.5 rounded text-xs <?= $p[0] ?>"><?= $p[1] ?></span>
                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400"><?= htmlspecialchars($categoryMap[$ticket['category']] ?? $ticket['category'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
        <a href="/cs/tickets/<?= $ticket['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
    </div>

    <!-- Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kund</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['customer_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tilldelad</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['assigned_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kontaktperson</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['contact_person'] ?? '—', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kontakt e-post / telefon</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                <?= htmlspecialchars($ticket['contact_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                <?= ($ticket['contact_phone'] ?? '') ? ' / ' . htmlspecialchars($ticket['contact_phone'], ENT_QUOTES, 'UTF-8') : '' ?>
                <?= (!$ticket['contact_email'] && !$ticket['contact_phone']) ? '—' : '' ?>
            </p>
        </div>
        <?php if (!empty($ticket['description'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Beskrivning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($ticket['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($ticket['resolution'])): ?>
        <div class="col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Lösning</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= nl2br(htmlspecialchars($ticket['resolution'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php endif; ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Skapad</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php if ($ticket['resolved_at']): ?>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Löst</p>
            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($ticket['resolved_at'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Status transitions -->
    <?php if (!empty($nextStatuses)): ?>
    <div class="flex flex-wrap gap-3">
        <?php foreach ($nextStatuses as $statusValue => $label): ?>
        <form method="POST" action="/cs/tickets/<?= $ticket['id'] ?>/status">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </button>
        </form>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Comments -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Kommentarer</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($comments as $c): ?>
            <div class="px-6 py-4 <?= $c['is_internal'] ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($c['user_name'] ?? 'System', ENT_QUOTES, 'UTF-8') ?></span>
                    <div class="flex items-center gap-2">
                        <?php if ($c['is_internal']): ?>
                        <span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Intern</span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400 dark:text-gray-500"><?= htmlspecialchars($c['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($c['comment'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <?php endforeach; ?>
            <?php if (empty($comments)): ?>
            <div class="px-6 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Inga kommentarer ännu</div>
            <?php endif; ?>
        </div>
        <!-- Add comment form -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
            <form method="POST" action="/cs/tickets/<?= $ticket['id'] ?>/comments" class="space-y-3">
                <?= \App\Core\Csrf::field() ?>
                <textarea name="comment" rows="3" placeholder="Skriv en kommentar…"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                        Intern kommentar
                    </label>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Lägg till</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer actions -->
    <div class="flex items-center gap-4">
        <a href="/cs/tickets" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Tillbaka till ärenden</a>
        <form method="POST" action="/cs/tickets/<?= $ticket['id'] ?>/delete" onsubmit="return confirm('Ta bort ärendet?')">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Ta bort</button>
        </form>
    </div>
</div>
