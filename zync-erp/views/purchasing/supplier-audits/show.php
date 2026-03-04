<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Leverantörsaudit — <?= htmlspecialchars($audit['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <div class="flex items-center gap-2">
            <a href="/purchasing/supplier-audits"
               class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                ← Tillbaka
            </a>
            <a href="/purchasing/supplier-audits/<?= (int)$audit['id'] ?>/edit"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                Redigera
            </a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 p-4 text-red-800 dark:text-red-200">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Detaljer -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Auditinformation</h2>
        </div>
        <div class="p-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Leverantör</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                        <?= htmlspecialchars($audit['supplier_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Datum</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($audit['audit_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utförare</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($audit['auditor_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</dt>
                    <dd class="mt-1">
                        <?php
                        $statusMap = [
                            'planned'     => ['Planerad',  'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                            'in_progress' => ['Pågående',  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                            'completed'   => ['Slutförd',  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                        ];
                        $s = $audit['status'] ?? '';
                        [$label, $cls] = $statusMap[$s] ?? [$s, 'bg-gray-100 text-gray-700'];
                        ?>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $cls ?>">
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nästa revision</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                        <?= htmlspecialchars($audit['next_audit_date'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Poängkort -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Poängbedömning</h2>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <?php
                $scoreFields = [
                    'delivery_score'      => 'Leverans',
                    'quality_score'       => 'Kvalitet',
                    'price_score'         => 'Pris',
                    'communication_score' => 'Kommunikation',
                ];
                foreach ($scoreFields as $field => $fieldLabel):
                    $val = $audit[$field] ?? null;
                ?>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        <?= $fieldLabel ?>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?= $val !== null ? htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') : '—' ?>
                    </div>
                    <?php if ($val !== null): ?>
                        <div class="text-xs text-gray-400 dark:text-gray-500">av 5</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Totalpoäng -->
            <?php $overall = $audit['overall_score'] ?? null; ?>
            <div class="flex items-center justify-center">
                <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-xl px-8 py-4 text-center">
                    <div class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">
                        Totalpoäng
                    </div>
                    <div class="text-4xl font-bold text-indigo-700 dark:text-indigo-300">
                        <?= $overall !== null ? htmlspecialchars(number_format((float)$overall, 1), ENT_QUOTES, 'UTF-8') : '—' ?>
                    </div>
                    <?php if ($overall !== null): ?>
                        <div class="text-sm text-indigo-500 dark:text-indigo-400">av 5</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Noteringar -->
    <?php if (!empty($audit['notes'])): ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Noteringar</h2>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                <?= htmlspecialchars($audit['notes'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
    <?php endif; ?>
</div>
