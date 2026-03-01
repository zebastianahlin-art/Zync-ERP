<div class="mx-auto max-w-2xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Redigera leverantör</h1>
        <a href="/suppliers" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">&larr; Tillbaka</a>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-md">
        <form method="POST" action="/suppliers/<?= $supplier->id ?>" class="space-y-5">
            <input type="hidden" name="_token" value="<?= \App\Core\Csrf::token() ?>">

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Namn <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required
                           value="<?= htmlspecialchars($supplier->name, ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['name'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="org_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Organisationsnummer <span class="text-red-500">*</span></label>
                    <input id="org_number" name="org_number" type="text" required
                           value="<?= htmlspecialchars($supplier->orgNumber, ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['org_number']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['org_number'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['org_number'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-postadress <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" required
                           value="<?= htmlspecialchars($supplier->email, ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['email']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                    <input id="phone" name="phone" type="text"
                           value="<?= htmlspecialchars($supplier->phone ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kontaktperson</label>
                    <input id="contact_person" name="contact_person" type="text"
                           value="<?= htmlspecialchars($supplier->contactPerson ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Webbplats</label>
                    <input id="website" name="website" type="url"
                           value="<?= htmlspecialchars($supplier->website ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adress</label>
                    <textarea id="address" name="address" rows="2"
                              class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($supplier->address ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postnummer</label>
                    <input id="postal_code" name="postal_code" type="text"
                           value="<?= htmlspecialchars($supplier->postalCode ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ort</label>
                    <input id="city" name="city" type="text"
                           value="<?= htmlspecialchars($supplier->city ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Land</label>
                    <input id="country" name="country" type="text"
                           value="<?= htmlspecialchars($supplier->country, ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"><?= htmlspecialchars($supplier->notes ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="sm:col-span-2 flex items-center space-x-2">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           <?= $supplier->isActive ? 'checked' : '' ?>
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktiv</label>
                </div>

            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="/suppliers" class="rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                    Spara ändringar
                </button>
            </div>

        </form>
    </div>

</div>
