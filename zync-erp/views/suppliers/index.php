<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Leverantörer</h1>
        <a href="/suppliers/create"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            + Ny leverantör
        </a>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-md">
        <?php if (empty($suppliers)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                Inga leverantörer ännu. <a href="/suppliers/create" class="text-indigo-600 dark:text-indigo-400 hover:underline">Lägg till den första.</a>
            </p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Namn</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Orgnr</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">E-post</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Telefon</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Kontaktperson</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Aktiv</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                <?= htmlspecialchars($supplier->name, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <?= htmlspecialchars($supplier->orgNumber, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <?= htmlspecialchars($supplier->email, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <?= $supplier->phone !== null ? htmlspecialchars($supplier->phone, ENT_QUOTES, 'UTF-8') : '–' ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <?= $supplier->contactPerson !== null ? htmlspecialchars($supplier->contactPerson, ENT_QUOTES, 'UTF-8') : '–' ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($supplier->isActive): ?>
                                    <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/40 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">Ja</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400">Nej</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/suppliers/<?= $supplier->id ?>/edit"
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">Redigera</a>
                                <form method="POST" action="/suppliers/<?= $supplier->id ?>/delete"
                                      class="inline"
                                      onsubmit="return confirm('Ta bort denna leverantör?')">
                                    <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">
                                    <button type="submit"
                                            class="text-red-600 dark:text-red-400 hover:underline bg-transparent border-0 p-0 cursor-pointer">
                                        Ta bort
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
