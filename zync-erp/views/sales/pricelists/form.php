<?php $pl = $priceList ?? $old ?? []; $isEdit = !empty($priceList); ?>
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="/sales/pricelists" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $title ?></h1>
    </div>

    <form method="post" action="<?= $isEdit ? "/sales/pricelists/{$priceList['id']}" : '/sales/pricelists' ?>" class="space-y-6">
        <?= \App\Core\Csrf::field() ?>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kod *</label>
                    <input type="text" name="code" value="<?= htmlspecialchars($pl['code'] ?? '') ?>" required maxlength="20" placeholder="T.ex. VIP, DIST" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono uppercase">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($pl['name'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valuta</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php foreach (['SEK','EUR','USD','NOK','DKK','GBP'] as $cur): ?>
                            <option value="<?= $cur ?>" <?= ($pl['currency'] ?? 'SEK') === $cur ? 'selected' : '' ?>><?= $cur ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giltig från</label>
                    <input type="date" name="valid_from" value="<?= $pl['valid_from'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Giltig till</label>
                    <input type="date" name="valid_to" value="<?= $pl['valid_to'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div class="flex items-end gap-6">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_default" value="1" <?= ($pl['is_default'] ?? 0) ? 'checked' : '' ?> class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                        Standardlista
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_active" value="1" <?= ($pl['is_active'] ?? 1) ? 'checked' : '' ?> class="rounded border-gray-300 dark:border-gray-600 text-indigo-600">
                        Aktiv
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/sales/pricelists" class="rounded-lg bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"><?= $isEdit ? 'Spara ändringar' : 'Skapa prislista' ?></button>
        </div>
    </form>
</div>
