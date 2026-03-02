<?php $account = $account ?? []; ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera konto <?= htmlspecialchars($account['account_number'] ?? '') ?></h1>
        <a href="/finance/accounts" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/accounts/<?= $account['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Kontonummer *</label><input type="text" name="account_number" value="<?= htmlspecialchars($account['account_number'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Namn *</label><input type="text" name="name" value="<?= htmlspecialchars($account['name'] ?? '') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Klass *</label><select name="account_class" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?php foreach(['1'=>'Tillgångar','2'=>'Skulder','3'=>'Intäkter','4'=>'Material','5'=>'Lokalkostnader','6'=>'Övriga externa','7'=>'Personal','8'=>'Finansiellt'] as $k=>$v): ?><option value="<?= $k ?>" <?= ($account['account_class'] ?? '') == $k ? 'selected' : '' ?>><?= $k ?> — <?= $v ?></option><?php endforeach; ?></select></div>
            <div><label class="block text-sm font-medium mb-1">Kontogrupp</label><input type="text" name="account_group" value="<?= htmlspecialchars($account['account_group'] ?? '') ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Momskod</label><select name="vat_code" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="">Ingen</option><option value="MP1" <?= ($account['vat_code'] ?? '') === 'MP1' ? 'selected' : '' ?>>MP1 — 25%</option><option value="MP2" <?= ($account['vat_code'] ?? '') === 'MP2' ? 'selected' : '' ?>>MP2 — 12%</option><option value="MP3" <?= ($account['vat_code'] ?? '') === 'MP3' ? 'selected' : '' ?>>MP3 — 6%</option></select></div>
            <div class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_active" value="1" <?= ($account['is_active'] ?? 1) ? 'checked' : '' ?> class="rounded border-gray-300"><label class="text-sm">Aktiv</label></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Beskrivning</label><textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($account['description'] ?? '') ?></textarea></div>
        <div class="flex justify-end pt-4"><button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Spara</button></div>
    </form>
</div>
