<?php
$statusLabels = ['draft' => 'Utkast', 'sent' => 'Skickad', 'paid' => 'Betald', 'overdue' => 'Förfallen', 'cancelled' => 'Avbruten'];
$statusColors = [
    'draft'     => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
    'sent'      => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400',
    'paid'      => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400',
    'overdue'   => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
    'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-400',
];
?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Faktura <?= htmlspecialchars((string) $invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="flex gap-2">
            <a href="/saas-admin/invoices" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Fakturor</a>
            <a href="/saas-admin/invoices/<?= (int) $invoice['id'] ?>/edit" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">Redigera</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Details -->
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detaljer</h2>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusColors[$invoice['status']] ?? '' ?>">
                    <?= htmlspecialchars($statusLabels[$invoice['status']] ?? $invoice['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Kund</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($invoice['company_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Kontakt</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) ($invoice['contact_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?><br>
                        <span class="text-xs text-gray-500"><?= htmlspecialchars((string) ($invoice['contact_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Period</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $invoice['period_start'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars((string) $invoice['period_end'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Förfallodatum</dt>
                    <dd class="text-gray-900 dark:text-gray-100"><?= htmlspecialchars((string) $invoice['due_date'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php if (!empty($invoice['paid_at'])): ?>
                <div>
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Betald</dt>
                    <dd class="text-green-700 dark:text-green-400 font-medium"><?= htmlspecialchars((string) $invoice['paid_at'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
            </dl>

            <!-- Amounts -->
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Belopp (ex. moms)</span>
                    <span class="text-gray-900 dark:text-gray-100"><?= number_format((float) $invoice['amount'], 2, ',', ' ') ?> kr</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Moms</span>
                    <span class="text-gray-900 dark:text-gray-100"><?= number_format((float) $invoice['vat'], 2, ',', ' ') ?> kr</span>
                </div>
                <div class="flex justify-between font-semibold text-base border-t border-gray-200 dark:border-gray-700 pt-2">
                    <span class="text-gray-900 dark:text-gray-100">Totalt</span>
                    <span class="text-indigo-600 dark:text-indigo-400"><?= number_format((float) $invoice['total'], 2, ',', ' ') ?> kr</span>
                </div>
            </div>

            <?php if (!empty($invoice['notes'])): ?>
            <div class="mt-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Anteckningar</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"><?= htmlspecialchars((string) $invoice['notes'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Status actions -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-3">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Ändra status</h2>
            <?php foreach (['sent' => 'Markera som skickad', 'paid' => 'Markera som betald', 'overdue' => 'Markera som förfallen', 'cancelled' => 'Avbryt faktura'] as $st => $label): ?>
                <?php if ($invoice['status'] !== $st): ?>
                    <form method="POST" action="/saas-admin/invoices/<?= (int) $invoice['id'] ?>/status">
                        <?= \App\Core\Csrf::field() ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-sm font-medium text-left transition-colors <?= $statusColors[$st] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' ?> hover:opacity-80">
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </button>
                    </form>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

</div>
