<div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Redigera ärende – <?= htmlspecialchars($ticket['ticket_number'], ENT_QUOTES, 'UTF-8') ?></h1>

    <form method="POST" action="/cs/tickets/<?= $ticket['id'] ?>" class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <?= \App\Core\Csrf::field() ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titel <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="<?= htmlspecialchars($ticket['title'], ENT_QUOTES, 'UTF-8') ?>"
                class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <?php if (!empty($errors['title'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beskrivning</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($ticket['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kund</label>
                <select name="customer_id" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj kund —</option>
                    <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $ticket['customer_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tilldelad</label>
                <select name="assigned_to" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">— Välj användare —</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $ticket['assigned_to'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kontaktperson</label>
                <input type="text" name="contact_person" value="<?= htmlspecialchars($ticket['contact_person'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-post</label>
                <input type="email" name="contact_email" value="<?= htmlspecialchars($ticket['contact_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                <input type="text" name="contact_phone" value="<?= htmlspecialchars($ticket['contact_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                <select name="category" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['complaint' => 'Klagomål','inquiry' => 'Förfrågan','return' => 'Retur','warranty' => 'Garanti','support' => 'Support','other' => 'Övrigt'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $ticket['category'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritet</label>
                <select name="priority" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['low' => 'Låg','normal' => 'Normal','high' => 'Hög','urgent' => 'Brådskande'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $ticket['priority'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php foreach (['open' => 'Öppet','in_progress' => 'Pågående','waiting_customer' => 'Väntar kund','waiting_internal' => 'Väntar internt','resolved' => 'Löst','closed' => 'Stängt'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $ticket['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lösning</label>
            <textarea name="resolution" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($ticket['resolution'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Spara</button>
            <a href="/cs/tickets/<?= $ticket['id'] ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg transition">Avbryt</a>
        </div>
    </form>
</div>
