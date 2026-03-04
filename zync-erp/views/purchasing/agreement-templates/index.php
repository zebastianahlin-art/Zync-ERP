<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Avtalsmallar</h1>
        <a href="/purchasing/agreement-templates/create"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ny mall
        </a>
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

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Namn</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leverantör</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Betalningsvillkor</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Leveransvillkor</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($templates)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Inga avtalsmallar registrerade ännu</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($template['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($template['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($template['default_payment_terms'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    <?= htmlspecialchars($template['default_delivery_terms'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php $active = !empty($template['is_active']); ?>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        <?= $active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' ?>">
                                        <?= $active ? 'Aktiv' : 'Inaktiv' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/purchasing/agreement-templates/<?= (int)$template['id'] ?>"
                                           class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                            Visa
                                        </a>
                                        <a href="/purchasing/agreement-templates/<?= (int)$template['id'] ?>/edit"
                                           class="rounded px-2 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                                            Redigera
                                        </a>
                                        <form method="POST"
                                              action="/purchasing/agreement-templates/<?= (int)$template['id'] ?>/delete"
                                              onsubmit="return confirm('Är du säker på att du vill ta bort denna mall?')">
                                            <?= \App\Core\Csrf::field() ?>
                                            <button type="submit"
                                                    class="rounded px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                                Ta bort
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
