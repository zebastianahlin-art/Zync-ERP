<?php $budget = $budget ?? []; $accounts = $accounts ?? []; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera budgetrad</h1>
        <a href="/finance/budgets?year=<?= (int)($budget['fiscal_year'] ?? date('Y')) ?>" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="/finance/budgets/<?= $budget['id'] ?>" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konto <span class="text-red-500">*</span></label>
                    <select name="account_id" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">— Välj konto —</option>
                        <?php foreach ($accounts as $acc): ?>
                        <option value="<?= $acc['id'] ?>" <?= ($budget['account_id'] ?? '') == $acc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($acc['account_number'] . ' — ' . $acc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Räkenskapsår <span class="text-red-500">*</span></label>
                    <input type="number" name="fiscal_year" value="<?= (int)($budget['fiscal_year'] ?? date('Y')) ?>" min="2000" max="2100" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Månad <span class="text-red-500">*</span></label>
                    <select name="month" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <?php $monthNames = ['1'=>'Januari','2'=>'Februari','3'=>'Mars','4'=>'April','5'=>'Maj','6'=>'Juni','7'=>'Juli','8'=>'Augusti','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'December']; ?>
                        <?php foreach ($monthNames as $num => $name): ?>
                        <option value="<?= $num ?>" <?= ((string)(int)($budget['month'] ?? 1)) === $num ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budgeterat belopp (SEK) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars((string)($budget['amount'] ?? '0')) ?>" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded text-sm transition">Spara</button>
                <a href="/finance/budgets?year=<?= (int)($budget['fiscal_year'] ?? date('Y')) ?>" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-5 py-2 rounded text-sm transition text-gray-700 dark:text-gray-300">Avbryt</a>
            </div>
        </form>
    </div>
</div>
