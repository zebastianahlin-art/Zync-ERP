<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Certifikat</h1>
        <div class="flex gap-2">
            <a href="/certificates/expiring" class="px-3 py-2 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300 text-sm font-medium rounded-lg transition">Utg&#229;r snart</a>
            <a href="/certificates/expired" class="px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-medium rounded-lg transition">Utg&#229;ngna</a>
            <a href="/certificates/create" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">+ Nytt certifikat</a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Personal</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Typ</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Utf&#228;rdat</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Utg&#229;r</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wide">P&#229;minnelse</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($certificates as $cert): ?>
                    <?php
                        $today = date('Y-m-d');
                        $expired   = !empty($cert['expiry_date']) && $cert['expiry_date'] < $today;
                        $exp60     = !$expired && !empty($cert['expiry_date']) && $cert['expiry_date'] < date('Y-m-d', strtotime('+60 days'));
                        $exp30     = !$expired && !empty($cert['expiry_date']) && $cert['expiry_date'] < date('Y-m-d', strtotime('+30 days'));
                        $daysLeft  = !empty($cert['expiry_date']) ? (int) round((strtotime($cert['expiry_date']) - strtotime($today)) / 86400) : null;
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 <?= $expired ? 'bg-red-50/40 dark:bg-red-900/10' : ($exp30 ? 'bg-yellow-50/40 dark:bg-yellow-900/10' : '') ?>">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($cert['employee_name'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($cert['certificate_type_name'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($cert['issued_date'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 font-medium <?= $expired ? 'text-red-600 dark:text-red-400' : ($exp30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-600 dark:text-gray-400') ?>">
                            <?= htmlspecialchars($cert['expiry_date'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($expired): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">&#10060; Utg&#229;nget</span>
                            <?php elseif ($exp30): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">&#9888;&#65039; Utg&#229;r snart</span>
                            <?php elseif ($exp60): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">&#8987; Utg&#229;r &lt;60d</span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">&#10003; Giltigt</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            <?php if ($daysLeft !== null): ?>
                                <?php if ($daysLeft < 0): ?>
                                    <span class="text-red-600 dark:text-red-400 font-medium"><?= abs($daysLeft) ?> dagar sedan</span>
                                <?php elseif ($daysLeft <= 60): ?>
                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium"><?= $daysLeft ?> dagar kvar</span>
                                <?php else: ?>
                                    <?= $daysLeft ?> dagar kvar
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="/certificates/<?= $cert['id'] ?>/edit" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mr-2">Redigera</a>
                            <?php if ($expired || $exp60): ?>
                            <form method="POST" action="/certificates/<?= (int)$cert['id'] ?>/renew" class="inline"
                                  onsubmit="return confirm('F&#246;rnya detta certifikat? Ett nytt certifikat skapas.')">
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="issued_date" value="<?= date('Y-m-d') ?>">
                                <button type="submit" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline mr-2">F&#246;rnya</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="/certificates/<?= $cert['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort certifikat?')">
                                <?= \App\Core\Csrf::field() ?>
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Ta bort</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($certificates)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Inga certifikat registrerade &#228;nnu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
