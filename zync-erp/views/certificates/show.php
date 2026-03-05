<?php
$expiry = $certificate['expiry_date'] ?? null;
$now = date('Y-m-d');
$in30 = date('Y-m-d', strtotime('+30 days'));
if (!$expiry) {
    $badge = ['text' => 'Okänt', 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'];
} elseif ($expiry < $now) {
    $badge = ['text' => 'Utgånget', 'class' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'];
} elseif ($expiry <= $in30) {
    $badge = ['text' => 'Utgår snart', 'class' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'];
} else {
    $badge = ['text' => 'Giltigt', 'class' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'];
}
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/certificates" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; Tillbaka till certifikat</a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars($certificate['certificate_type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <?= htmlspecialchars($certificate['employee_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $badge['class'] ?>">
                <?= htmlspecialchars($badge['text'], ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a href="/certificates/<?= (int)$certificate['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/certificates/<?= (int)$certificate['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort certifikatet?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Certifikattyp</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($certificate['certificate_type_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anställd</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($certificate['employee_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utfärdandedatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($certificate['issued_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Utgångsdatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($certificate['expiry_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php if (!empty($certificate['notes'])): ?>
        <div class="md:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($certificate['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
    </div>
</div>
