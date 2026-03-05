<?php

declare(strict_types=1);

/**
 * @var array<int, array{slug: string, name: string, is_configured: bool}> $integrations
 * @var string $title
 * @var array<int, array{label: string, url?: string}> $breadcrumbs
 */
?>
<?php include dirname(__DIR__) . '/partials/breadcrumbs.php'; ?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Externa integrationer</h1>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($integrations as $integration): ?>
        <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-6 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    <?= htmlspecialchars($integration['name'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <?php if ($integration['is_configured']): ?>
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Konfigurerad
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-0.5 text-xs font-medium text-yellow-700 dark:text-yellow-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-400"></span>
                        Ej konfigurerad
                    </span>
                <?php endif; ?>
            </div>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                <?= match($integration['slug']) {
                    'peppol'       => 'Skicka och ta emot e-fakturor via Peppol-nätverket.',
                    'imap'         => 'Hämta och bearbeta inkommande fakturor via e-post.',
                    'open_banking' => 'Hämta banktransaktioner och matcha mot fakturor.',
                    'ai'           => 'AI-driven analys av underhållsmönster och rapportgenerering.',
                    default        => 'Extern integration.',
                } ?>
            </p>

            <div class="mt-auto">
                <form method="POST" action="/admin/integrations/<?= htmlspecialchars($integration['slug'], ENT_QUOTES, 'UTF-8') ?>/test">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit"
                            <?= !$integration['is_configured'] ? 'disabled' : '' ?>
                            class="w-full rounded-lg border border-indigo-600 dark:border-indigo-500 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        Testa anslutning
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-8 rounded-xl border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 text-sm text-yellow-700 dark:text-yellow-400">
    <strong>Obs!</strong> Integrationerna konfigureras via miljövariabler i <code>.env</code>.
    Kontakta systemadministratören för att aktivera en integration.
</div>
