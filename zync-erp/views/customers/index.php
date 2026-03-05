<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Kunder</h1>
        <a href="/customers/create"
           class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700 transition-colors">
            + Ny kund
        </a>
    </div>

    <?php if ($success !== null): ?>
        <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl bg-white shadow-md">
        <?php if (empty($customers)): ?>
            <p class="px-6 py-10 text-center text-sm text-gray-500">Inga kunder ännu. <a href="/customers/create" class="text-indigo-600 hover:underline">Lägg till den första.</a></p>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide text-xs">Namn</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide text-xs">Org.nummer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide text-xs">E-post</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide text-xs">Telefon</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wide text-xs">Åtgärder</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach ($customers as $customer): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= htmlspecialchars($customer->name, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($customer->orgNumber, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($customer->email, ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= $customer->phone !== null ? htmlspecialchars($customer->phone, ENT_QUOTES, 'UTF-8') : '–' ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="/customers/<?= $customer->id ?>/edit"
                                   class="text-indigo-600 hover:underline">Redigera</a>
                                <form method="POST" action="/customers/<?= $customer->id ?>/delete"
                                      class="inline"
                                      onsubmit="return confirm('Ta bort denna kund?')">
                                    <button type="submit"
                                            class="text-red-600 hover:underline bg-transparent border-0 p-0 cursor-pointer">
                                        Ta bort
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
