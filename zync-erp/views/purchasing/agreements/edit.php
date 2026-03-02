<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="/purchasing/agreements/<?= $agreement['id'] ?>" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera avtal</h1>
    </div>

    <form method="POST" action="/purchasing/agreements/<?= $agreement['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel *</label>
            <input type="text" name="title" required value="<?= htmlspecialchars($agreement['title']) ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leverantör *</label>
                <select name="supplier_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (int)$agreement['supplier_id'] === (int)$s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avtalstyp</label>
                <select name="agreement_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach (['standard'=>'Standard','framework'=>'Ramavtal','project'=>'Projektavtal','service'=>'Serviceavtal','AB04'=>'AB04','ABT06'=>'ABT06','NL10'=>'NL10','NL17'=>'NL17','ABA99'=>'ABA99','other'=>'Övrigt'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $agreement['agreement_type'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Startdatum *</label>
                <input type="date" name="start_date" required value="<?= $agreement['start_date'] ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slutdatum</label>
                <input type="date" name="end_date" value="<?= $agreement['end_date'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach (['draft'=>'Utkast','active'=>'Aktivt','expired'=>'Utgånget','terminated'=>'Avslutat'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $agreement['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Avtalsvärde</label>
                <input type="number" name="value" step="0.01" min="0" value="<?= $agreement['value'] ?? '' ?>" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ansvarig</label>
                <select name="responsible_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— Välj ansvarig —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= (int)($agreement['responsible_id'] ?? 0) === (int)$u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($agreement['description'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Villkor / Klausuler</label>
            <textarea name="terms" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($agreement['terms'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition">Spara</button>
            <a href="/purchasing/agreements/<?= $agreement['id'] ?>" class="px-6 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">Avbryt</a>
        </div>
    </form>

    <form method="POST" action="/purchasing/agreements/<?= $agreement['id'] ?>/delete" onsubmit="return confirm('Vill du verkligen radera detta avtal?')" class="text-right">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Radera avtal</button>
    </form>
</div>
