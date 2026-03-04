<?php $asset = $asset ?? []; $departments = $departments ?? []; $accounts = $accounts ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera tillgång</h1>
        <a href="/finance/assets/<?= $asset['id'] ?>" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/finance/assets/<?= $asset['id'] ?>" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Namn <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($asset['name'] ?? '') ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anläggningsnummer <span class="text-red-500">*</span></label>
                    <input type="text" name="asset_number" value="<?= htmlspecialchars($asset['asset_number'] ?? '') ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
                    <textarea name="description" rows="2" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><?= htmlspecialchars($asset['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inköpsdatum <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" value="<?= $asset['purchase_date'] ?? '' ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inköpspris (SEK)</label>
                    <input type="number" name="purchase_price" step="0.01" value="<?= htmlspecialchars((string)($asset['purchase_price'] ?? '')) ?>" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nuvarande bokfört värde (SEK)</label>
                    <input type="number" name="current_value" step="0.01" value="<?= htmlspecialchars((string)($asset['current_value'] ?? '')) ?>" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avskrivningsmetod</label>
                    <select name="depreciation_method" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="linear" <?= ($asset['depreciation_method'] ?? '') === 'linear' ? 'selected' : '' ?>>Linjär</option>
                        <option value="declining" <?= ($asset['depreciation_method'] ?? '') === 'declining' ? 'selected' : '' ?>>Degressiv</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avskrivningstid (år)</label>
                    <input type="number" name="depreciation_years" value="<?= (int)($asset['depreciation_years'] ?? 5) ?>" min="1" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avdelning</label>
                    <select name="department_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Välj —</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($asset['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konto</label>
                    <select name="account_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Välj —</option>
                        <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id'] ?>" <?= ($asset['account_id'] ?? '') == $acc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($acc['account_number'] . ' — ' . $acc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="active" <?= ($asset['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="disposed" <?= ($asset['status'] ?? '') === 'disposed' ? 'selected' : '' ?>>Avyttrad</option>
                        <option value="written_off" <?= ($asset['status'] ?? '') === 'written_off' ? 'selected' : '' ?>>Utskriven</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded text-sm transition">Spara</button>
                <a href="/finance/assets/<?= $asset['id'] ?>" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-5 py-2 rounded text-sm transition text-gray-700 dark:text-gray-300">Avbryt</a>
            </div>
        </form>
    </div>
</div>
