<?php $accounts = $accounts ?? []; $costCenters = $costCenters ?? []; ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ny verifikation</h1>
        <a href="/finance/journal" class="text-sm text-gray-500 hover:text-indigo-600">← Tillbaka</a>
    </div>
    <form method="POST" action="/finance/journal" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-6">
        <?= \App\Core\Csrf::field() ?>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">Datum *</label><input type="date" name="entry_date" value="<?= date('Y-m-d') ?>" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
            <div><label class="block text-sm font-medium mb-1">Serie</label><select name="voucher_series" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="A">A — Löpande</option><option value="B">B — Bank</option><option value="K">K — Kassa</option><option value="L">L — Leverantör</option><option value="S">S — Skatt</option></select></div>
            <div><label class="block text-sm font-medium mb-1">Typ</label><select name="source_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"><option value="manual">Manuell</option><option value="invoice_out">Kundfaktura</option><option value="invoice_in">Lev.faktura</option><option value="payment">Betalning</option><option value="salary">Lön</option><option value="depreciation">Avskrivning</option></select></div>
        </div>
        <div><label class="block text-sm font-medium mb-1">Beskrivning *</label><input type="text" name="description" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></div>
        <div><label class="block text-sm font-medium mb-1">Anteckning</label><textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea></div>

        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Konteringsrader</h3>
            <div class="space-y-2" id="journal-lines">
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                    <div><select name="lines[<?= $i ?>][account_id]" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">Konto...</option><?php foreach ($accounts as $acc): ?><option value="<?= $acc['id'] ?>"><?= htmlspecialchars($acc['account_number'] . ' ' . $acc['name']) ?></option><?php endforeach; ?></select></div>
                    <div><select name="lines[<?= $i ?>][cost_center_id]" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"><option value="">KS...</option><?php foreach ($costCenters as $cc): ?><option value="<?= $cc['id'] ?>"><?= htmlspecialchars($cc['code'] . ' ' . $cc['name']) ?></option><?php endforeach; ?></select></div>
                    <div><input type="text" name="lines[<?= $i ?>][description]" placeholder="Beskrivning" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                    <div><input type="number" name="lines[<?= $i ?>][debit]" step="0.01" placeholder="Debet" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                    <div><input type="number" name="lines[<?= $i ?>][credit]" step="0.01" placeholder="Kredit" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition">Skapa verifikation</button>
        </div>
    </form>
</div>
