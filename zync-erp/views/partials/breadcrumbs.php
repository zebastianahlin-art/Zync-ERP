<?php
/**
 * Breadcrumbs partial
 *
 * Usage: include with $breadcrumbs array:
 * [['label' => 'Dashboard', 'url' => '/dashboard'], ['label' => 'Aktuell sida']]
 *
 * @var array<int, array{label: string, url?: string}> $breadcrumbs
 */
if (empty($breadcrumbs)) {
    return;
}
?>
<nav aria-label="Brödsmulor" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php $isLast = ($i === count($breadcrumbs) - 1); ?>
            <?php if ($i > 0): ?>
                <li aria-hidden="true">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </li>
            <?php endif; ?>
            <li>
                <?php if (!$isLast && !empty($crumb['url'])): ?>
                    <a href="<?= htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8') ?>"
                       class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php else: ?>
                    <span class="text-gray-700 dark:text-gray-200 font-medium" aria-current="page">
                        <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
