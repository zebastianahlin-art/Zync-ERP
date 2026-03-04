<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            <?= htmlspecialchars($template['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <div class="flex items-center gap-2">
            <a href="/purchasing/agreement-templates"
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                ← Tillbaka
            </a>
            <a href="/purchasing/agreement-templates/<?= (int)$template['id'] ?>/edit"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Redigera
            </a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Detaljer -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mallinformation</h2>
        </div>
        <div class="p-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Leverantör</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($template['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Betalningsvillkor</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($template['default_payment_terms'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Leveransvillkor</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($template['default_delivery_terms'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</dt>
                    <dd class="mt-1">
                        <?php $active = !empty($template['is_active']); ?>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                            <?= $active
                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' ?>">
                            <?= $active ? 'Aktiv' : 'Inaktiv' ?>
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Beskrivning -->
    <?php if (!empty($template['description'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Beskrivning</h2>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                <?= htmlspecialchars($template['description'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Standardvillkor -->
    <?php if (!empty($template['default_terms'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Standardvillkor / Generella villkor</h2>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                <?= htmlspecialchars($template['default_terms'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Skapa avtal från mall -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Skapa avtal</h2>
        </div>
        <div class="p-5 flex items-center gap-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Använd denna mall som utgångspunkt för ett nytt avtal.
            </p>
            <a href="/purchasing/agreements/create?template_id=<?= (int)$template['id'] ?>"
               class="flex-shrink-0 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition">
                Skapa avtal från mall
            </a>
        </div>
    </div>
</div>
