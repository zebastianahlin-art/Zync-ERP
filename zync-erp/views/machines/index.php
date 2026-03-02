<?php
$typeLabels = ['site'=>'Anläggning','area'=>'Område','line'=>'Linje','machine'=>'Maskin','sub_machine'=>'Delmaskin','component'=>'Komponent'];
$statusLabels = ['operational'=>['Drift','bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'],'degraded'=>['Nedsatt','bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'],'down'=>['Stillastående','bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],'decommissioned'=>['Avvecklad','bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400']];
$critColors = ['A'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400','B'=>'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400','C'=>'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'];

function renderTree(array $nodes, array $typeLabels, array $statusLabels, array $critColors, int $depth = 0): void {
    foreach ($nodes as $n):
        $sl = $statusLabels[$n['status']] ?? ['?','bg-gray-100 text-gray-500'];
        $cc = $critColors[$n['criticality']] ?? $critColors['B'];
?>
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
        <td class="px-4 py-3">
            <div class="flex items-center" style="padding-left:<?= $depth * 24 ?>px">
                <?php if (!empty($n['children'])): ?>
                    <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <?php else: ?>
                    <span class="w-5"></span>
                <?php endif; ?>
                <a href="/machines/<?= $n['id'] ?>" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline"><?= htmlspecialchars($n['name'], ENT_QUOTES, 'UTF-8') ?></a>
            </div>
        </td>
        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($n['code'], ENT_QUOTES, 'UTF-8') ?></td>
        <td class="px-4 py-3"><span class="text-xs text-gray-600 dark:text-gray-400"><?= $typeLabels[$n['type']] ?? $n['type'] ?></span></td>
        <td class="px-4 py-3"><span class="inline-flex rounded-full <?= $sl[1] ?> px-2 py-0.5 text-xs font-medium"><?= $sl[0] ?></span></td>
        <td class="px-4 py-3"><span class="inline-flex rounded-full <?= $cc ?> px-2 py-0.5 text-xs font-bold"><?= $n['criticality'] ?></span></td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs"><?= htmlspecialchars($n['location'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
<?php
        if (!empty($n['children'])) {
            renderTree($n['children'], $typeLabels, $statusLabels, $critColors, $depth + 1);
        }
    endforeach;
}
?>

<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Totalt</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">I drift</p>
            <p class="mt-1 text-2xl font-bold text-green-600"><?= $stats['operational'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Nedsatt</p>
            <p class="mt-1 text-2xl font-bold text-yellow-600"><?= $stats['degraded'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Stillastående</p>
            <p class="mt-1 text-2xl font-bold text-red-600"><?= $stats['down'] ?></p>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Avvecklade</p>
            <p class="mt-1 text-2xl font-bold text-gray-400"><?= $stats['decommissioned'] ?></p>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Maskiner & Utrustning</h1>
        <a href="/machines/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ny maskin
        </a>
    </div>

    <!-- Tree table -->
    <?php if (empty($tree)): ?>
        <div class="rounded-xl bg-white dark:bg-gray-800 p-10 text-center shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inga maskiner registrerade ännu.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Namn</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Kod</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Typ</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Krit.</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Plats</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php renderTree($tree, $typeLabels, $statusLabels, $critColors); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
