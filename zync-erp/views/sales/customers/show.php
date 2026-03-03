<?php $c = $customer; ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="/sales/customers" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($c['name']) ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?= $c['customer_number'] ?> · <?= $c['org_number'] ?></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/sales/quotes/create?customer_id=<?= $c['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Ny offert</a>
            <a href="/sales/customers/<?= $c['id'] ?>/edit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Redigera</a>
        </div>
    </div>

    <!-- Kundinfo cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Kontaktuppgifter</h3>
            <div class="space-y-2 text-sm">
                <p><span class="text-gray-400">E-post:</span> <a href="mailto:<?= $c['email'] ?>" class="text-indigo-600 dark:text-indigo-400"><?= $c['email'] ?></a></p>
                <?php if ($c['invoice_email']): ?><p><span class="text-gray-400">Faktura:</span> <?= $c['invoice_email'] ?></p><?php endif; ?>
                <?php if ($c['phone']): ?><p><span class="text-gray-400">Telefon:</span> <?= $c['phone'] ?></p><?php endif; ?>
                <?php if ($c['website']): ?><p><span class="text-gray-400">Webb:</span> <a href="<?= $c['website'] ?>" target="_blank" class="text-indigo-600 dark:text-indigo-400"><?= $c['website'] ?></a></p><?php endif; ?>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Kommersiella villkor</h3>
            <div class="space-y-2 text-sm">
                <p><span class="text-gray-400">Betalning:</span> <span class="text-gray-900 dark:text-white"><?= $c['payment_terms'] ?></span></p>
                <p><span class="text-gray-400">Valuta:</span> <span class="text-gray-900 dark:text-white"><?= $c['currency'] ?></span></p>
                <p><span class="text-gray-400">Kreditgräns:</span> <span class="text-gray-900 dark:text-white"><?= number_format((float)$c['credit_limit'], 0, ',', ' ') ?> SEK</span></p>
                <p><span class="text-gray-400">Rabatt:</span> <span class="text-gray-900 dark:text-white"><?= $c['discount_percent'] ?>%</span></p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Adresser</h3>
            <div class="space-y-2 text-sm">
                <?php if ($c['address']): ?><p><span class="text-gray-400">Faktura:</span><br><span class="text-gray-900 dark:text-white whitespace-pre-line"><?= htmlspecialchars($c['address']) ?></span></p><?php endif; ?>
                <?php if ($c['delivery_address']): ?><p><span class="text-gray-400">Leverans:</span><br><span class="text-gray-900 dark:text-white whitespace-pre-line"><?= htmlspecialchars($c['delivery_address']) ?></span></p><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabs: Kontakter / Kundpriser / Offerter / Ordrar / Aktivitet -->
    <div x-data="{ tab: 'contacts' }">
        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
            <nav class="flex gap-4">
                <button @click="tab='contacts'" :class="tab==='contacts' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium">Kontakter (<?= count($contacts) ?>)</button>
                <button @click="tab='prices'" :class="tab==='prices' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium">Kundpriser (<?= count($customerPrices) ?>)</button>
                <button @click="tab='quotes'" :class="tab==='quotes' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium">Offerter (<?= count($quotes) ?>)</button>
                <button @click="tab='orders'" :class="tab==='orders' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium">Ordrar (<?= count($orders) ?>)</button>
                <button @click="tab='activity'" :class="tab==='activity' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="pb-3 border-b-2 text-sm font-medium">Aktivitet</button>
            </nav>
        </div>

        <!-- Kontakter -->
        <div x-show="tab==='contacts'" class="space-y-4">
            <form method="post" action="/sales/customers/<?= $c['id'] ?>/contacts" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Lägg till kontakt</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <input type="text" name="first_name" placeholder="Förnamn *" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="text" name="last_name" placeholder="Efternamn *" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="text" name="title" placeholder="Titel" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="email" name="email" placeholder="E-post" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="text" name="phone" placeholder="Telefon" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="text" name="mobile" placeholder="Mobil" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="text" name="department" placeholder="Avdelning" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400"><input type="checkbox" name="is_primary" value="1" class="rounded"> Primär</label>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">Lägg till</button>
                    </div>
                </div>
            </form>
            <?php if (!empty($contacts)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Namn</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titel</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-post</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Primär</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($contacts as $ct): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($ct['first_name'] . ' ' . $ct['last_name']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($ct['title'] ?? '') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($ct['email'] ?? '') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($ct['phone'] ?? '') ?> <?= $ct['mobile'] ? '/ ' . $ct['mobile'] : '' ?></td>
                            <td class="px-4 py-3 text-center"><?= $ct['is_primary'] ? '⭐' : '' ?></td>
                            <td class="px-4 py-3 text-right">
                                <form method="post" action="/sales/customers/<?= $c['id'] ?>/contacts/<?= $ct['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort kontakt?')">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Kundpriser -->
        <div x-show="tab==='prices'" class="space-y-4">
            <form method="post" action="/sales/customers/<?= $c['id'] ?>/prices" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <?= \App\Core\Csrf::field() ?>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Lägg till kundpris</h4>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <select name="article_id" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Välj artikel...</option>
                        <?php foreach ($articles ?? [] as $a): ?>
                            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['article_number'] . ' — ' . $a['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="unit_price" placeholder="Pris" step="0.01" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="number" name="min_quantity" placeholder="Min antal" value="1" step="0.001" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <input type="number" name="discount_percent" placeholder="Rabatt %" value="0" step="0.5" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">Lägg till</button>
                </div>
            </form>
            <?php if (!empty($customerPrices)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Artikel</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pris</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Min antal</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rabatt</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($customerPrices as $cp): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm"><span class="font-mono text-gray-400"><?= $cp['article_number'] ?></span> <?= htmlspecialchars($cp['article_name']) ?></td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$cp['unit_price'], 2, ',', ' ') ?></td>
                            <td class="px-4 py-3 text-sm text-right text-gray-500"><?= $cp['min_quantity'] ?></td>
                            <td class="px-4 py-3 text-sm text-right text-gray-500"><?= $cp['discount_percent'] ?>%</td>
                            <td class="px-4 py-3 text-right">
                                <form method="post" action="/sales/customers/<?= $c['id'] ?>/prices/<?= $cp['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort pris?')">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button class="text-red-500 hover:text-red-700 text-xs">Ta bort</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Offerter -->
        <div x-show="tab==='quotes'">
            <?php if (empty($quotes)): ?>
                <p class="text-sm text-gray-400 py-4">Inga offerter för denna kund</p>
            <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offertnr</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Belopp</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($quotes as $q): $sc = match($q['status']) { 'draft'=>'gray','sent'=>'blue','accepted'=>'green','rejected'=>'red','expired'=>'yellow',default=>'gray' }; $sl = match($q['status']) { 'draft'=>'Utkast','sent'=>'Skickad','accepted'=>'Accepterad','rejected'=>'Nekad','expired'=>'Utgången','revised'=>'Reviderad',default=>$q['status'] }; ?>
                        <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50" onclick="location.href='/sales/quotes/<?= $q['id'] ?>'">
                            <td class="px-4 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400"><?= $q['quote_number'] ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?= $q['quote_date'] ?></td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$q['total_amount'], 0, ',', ' ') ?> SEK</td>
                            <td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Ordrar -->
        <div x-show="tab==='orders'">
            <?php if (empty($orders)): ?>
                <p class="text-sm text-gray-400 py-4">Inga ordrar för denna kund</p>
            <?php else: ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordernr</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Belopp</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php foreach ($orders as $o): $sc = match($o['status']) { 'draft'=>'gray','confirmed'=>'blue','in_production'=>'yellow','delivered'=>'green','invoiced'=>'emerald','cancelled'=>'red',default=>'gray' }; $sl = match($o['status']) { 'draft'=>'Utkast','confirmed'=>'Bekräftad','in_production'=>'I produktion','partially_delivered'=>'Dellevererad','delivered'=>'Levererad','invoiced'=>'Fakturerad','cancelled'=>'Avbruten',default=>$o['status'] }; ?>
                        <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50" onclick="location.href='/sales/orders/<?= $o['id'] ?>'">
                            <td class="px-4 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400"><?= $o['order_number'] ?></td>
                            <td class="px-4 py-3 text-sm text-gray-500"><?= $o['order_date'] ?></td>
                            <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-white"><?= number_format((float)$o['total_amount'], 0, ',', ' ') ?> SEK</td>
                            <td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full bg-<?= $sc ?>-100 dark:bg-<?= $sc ?>-900/30 px-2 py-0.5 text-xs font-medium text-<?= $sc ?>-700 dark:text-<?= $sc ?>-400"><?= $sl ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Aktivitetslogg -->
        <div x-show="tab==='activity'">
            <?php if (empty($activities)): ?>
                <p class="text-sm text-gray-400 py-4">Ingen aktivitet loggad</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($activities as $a): ?>
                <div class="flex gap-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <?php $ic = match($a['activity_type']) { 'call'=>'📞','email'=>'✉️','meeting'=>'🤝','status_change'=>'🔄','quote_sent'=>'📤','order_confirmed'=>'✅', default=>'📝' }; ?>
                    <span class="text-lg"><?= $ic ?></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($a['subject']) ?></p>
                        <?php if ($a['description']): ?><p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($a['description']) ?></p><?php endif; ?>
                        <p class="text-xs text-gray-400 mt-1"><?= $a['activity_date'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
