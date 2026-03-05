<?php
/**
 * Pagination partial
 *
 * Usage: include with:
 * @var int $totalItems   Total number of items
 * @var int $currentPage  Current page (1-indexed)
 * @var int $perPage      Items per page
 * @var string $baseUrl   Base URL to append ?page=N to
 */
if (!isset($totalItems, $currentPage, $perPage, $baseUrl)) {
    return;
}

$totalPages = (int) ceil($totalItems / $perPage);

if ($totalPages <= 1) {
    return;
}

$prevPage = max(1, $currentPage - 1);
$nextPage = min($totalPages, $currentPage + 1);

$start = ($currentPage - 1) * $perPage + 1;
$end   = min($currentPage * $perPage, $totalItems);

function paginationUrl(string $baseUrl, int $page): string
{
    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    return htmlspecialchars($baseUrl . $sep . 'page=' . $page, ENT_QUOTES, 'UTF-8');
}
?>
<div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 text-sm text-gray-600 dark:text-gray-400">
    <p>
        Visar <span class="font-medium text-gray-900 dark:text-white"><?= $start ?>–<?= $end ?></span>
        av <span class="font-medium text-gray-900 dark:text-white"><?= $totalItems ?></span> poster
    </p>
    <nav aria-label="Paginering">
        <ul class="flex items-center gap-1">
            <!-- Föregående -->
            <li>
                <?php if ($currentPage > 1): ?>
                    <a href="<?= paginationUrl($baseUrl, $prevPage) ?>"
                       class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        &laquo; Föregående
                    </a>
                <?php else: ?>
                    <span class="px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                        &laquo; Föregående
                    </span>
                <?php endif; ?>
            </li>

            <!-- Sidnummer -->
            <?php
            $range = 2;
            $pages = [];
            for ($p = max(1, $currentPage - $range); $p <= min($totalPages, $currentPage + $range); $p++) {
                $pages[] = $p;
            }
            if (!in_array(1, $pages, true)) {
                echo '<li><a href="' . paginationUrl($baseUrl, 1) . '" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">1</a></li>';
                if ($pages[0] > 2) {
                    echo '<li><span class="px-2 py-1.5 text-gray-400">…</span></li>';
                }
            }
            foreach ($pages as $p):
            ?>
            <li>
                <?php if ($p === $currentPage): ?>
                    <span class="px-3 py-1.5 rounded-md bg-indigo-600 text-white font-medium" aria-current="page"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= paginationUrl($baseUrl, $p) ?>"
                       class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <?= $p ?>
                    </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
            <?php
            if (!in_array($totalPages, $pages, true)) {
                if (end($pages) < $totalPages - 1) {
                    echo '<li><span class="px-2 py-1.5 text-gray-400">…</span></li>';
                }
                echo '<li><a href="' . paginationUrl($baseUrl, $totalPages) . '" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">' . $totalPages . '</a></li>';
            }
            ?>

            <!-- Nästa -->
            <li>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= paginationUrl($baseUrl, $nextPage) ?>"
                       class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Nästa &raquo;
                    </a>
                <?php else: ?>
                    <span class="px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                        Nästa &raquo;
                    </span>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</div>
