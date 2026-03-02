<?php
$c = $cert;
$action  = $isNew ? '/certificates' : '/certificates/' . (int) $c['id'];
$heading = $isNew ? 'Nytt certifikat' : 'Redigera certifikat';
$statusOptions = ['active' => 'Giltig', 'expiring' => 'Utgår snart', 'expired' => 'Utgången', 'revoked' => 'Återkallad'];
?>
<div class="mx-auto max-w-3xl">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= $heading ?></h1>
        <a href="/certificates" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">&larr; Tillbaka</a>
    </div>

    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-8">
        <?= \App\Core\Csrf::field() ?>

        <!-- Certifikatinfo -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Certifikatuppgifter</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anställd <span class="text-red-500">*</span></label>
                    <select id="employee_id" name="employee_id" required
                            class="mt-1 block w-full rounded-lg border <?= isset($errors['employee_id']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                        <option value="">– Välj anställd –</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= (int) $emp['id'] ?>" <?= (int) ($c['employee_id'] ?? 0) === (int) $emp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['full_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($emp['employee_number'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['employee_id'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['employee_id'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Typ</label>
                    <select id="type" name="type" onchange="if(this.value) document.getElementById('name').value = this.value;"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                        <option value="">– Välj eller skriv fritt –</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-months="<?= (int) ($t['validity_months'] ?? 0) ?>"
                                    <?= ($c['type'] ?? '') === $t['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int) $t['validity_months'] ?> mån)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Certifikatnamn <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required
                           value="<?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border <?= isset($errors['name']) ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' ?> px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <?php if (isset($errors['name'])): ?><p class="mt-1 text-xs text-red-600"><?= $errors['name'] ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="certificate_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Certifikatnummer</label>
                    <input id="certificate_number" name="certificate_number" type="text"
                           value="<?= htmlspecialchars($c['certificate_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="issuer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utfärdare</label>
                    <input id="issuer" name="issuer" type="text"
                           value="<?= htmlspecialchars($c['issuer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                        <?php foreach ($statusOptions as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($c['status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Datum -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Giltighetsdatum</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="issued_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utfärdandedatum</label>
                    <input id="issued_date" name="issued_date" type="date"
                           value="<?= htmlspecialchars($c['issued_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utgångsdatum</label>
                    <input id="expiry_date" name="expiry_date" type="date"
                           value="<?= htmlspecialchars($c['expiry_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Fil -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">Bifoga certifikat</h2>
            <?php if (!$isNew && ($c['file_name'] ?? null)): ?>
                <div class="mb-3 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>📎</span>
                    <a href="/certificates/<?= (int) $c['id'] ?>/download" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                        <?= htmlspecialchars($c['file_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <span class="text-xs text-gray-400">(ladda upp ny för att ersätta)</span>
                </div>
            <?php endif; ?>
            <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png,.webp"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
            <p class="mt-1 text-xs text-gray-400">PDF, JPG, PNG eller WebP. Max 10 MB.</p>
        </div>

        <!-- Anteckningar -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anteckningar</label>
            <textarea id="notes" name="notes" rows="3"
                      class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($c['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="/certificates" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Avbryt</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
                <?= $isNew ? 'Skapa certifikat' : 'Spara ändringar' ?>
            </button>
        </div>
    </form>

    <?php if (!$isNew): ?>
    <div class="mt-6 rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6">
        <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Radera certifikat</h3>
        <p class="mt-1 text-xs text-red-600 dark:text-red-400">Denna åtgärd kan inte ångras.</p>
        <form method="POST" action="/certificates/<?= (int) $c['id'] ?>/delete" class="mt-3"
              onsubmit="return confirm('Är du säker?');">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 transition-colors">Radera</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
// Auto-beräkna utgångsdatum från typ
document.getElementById('type')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const months = parseInt(opt.dataset.months || 0);
    const issuedInput = document.getElementById('issued_date');
    const expiryInput = document.getElementById('expiry_date');
    if (months > 0 && issuedInput.value) {
        const d = new Date(issuedInput.value);
        d.setMonth(d.getMonth() + months);
        expiryInput.value = d.toISOString().split('T')[0];
    }
});
document.getElementById('issued_date')?.addEventListener('change', function() {
    const typeSelect = document.getElementById('type');
    const opt = typeSelect.options[typeSelect.selectedIndex];
    const months = parseInt(opt?.dataset?.months || 0);
    if (months > 0 && this.value) {
        const d = new Date(this.value);
        d.setMonth(d.getMonth() + months);
        document.getElementById('expiry_date').value = d.toISOString().split('T')[0];
    }
});
</script>
