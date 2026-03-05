<?php
$e = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$planDefaults = [
    'starter'      => ['Starter', '999 kr/mån – Underhåll & Drift', 10],
    'professional' => ['Professional', '2 499 kr/mån – HR, Ekonomi, Projekt', 50],
    'enterprise'   => ['Enterprise', '4 999 kr/mån – Komplett ERP', 999],
];
?>
<div class="space-y-6" x-data="{ step: 1 }">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <a href="/saas-admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">SaaS Admin</a>
        <span>/</span>
        <a href="/saas-admin/tenants" class="hover:text-indigo-600 dark:hover:text-indigo-400">Kunder</a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">Ny kund (Provisioning)</span>
    </nav>

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Skapa ny kund</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Följ stegen för att konfigurera och aktivera en ny SaaS-kund</p>
        </div>
    </div>

    <!-- Step indicator -->
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 text-sm" :class="step >= 1 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400'">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                  :class="step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">1</span>
            Företagsinfo
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600"></div>
        <div class="flex items-center gap-2 text-sm" :class="step >= 2 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400'">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                  :class="step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">2</span>
            Plan & Moduler
        </div>
        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-600"></div>
        <div class="flex items-center gap-2 text-sm" :class="step >= 3 ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-400'">
            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                  :class="step >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">3</span>
            Bekräfta
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
        <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">Rätta felen nedan och försök igen:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $field => $msg): ?>
            <li class="text-sm text-red-600 dark:text-red-400"><?= $e($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="/saas-admin/tenants/provision" x-ref="provisionForm">
        <?= \App\Core\Csrf::field() ?>

        <!-- Step 1: Company Info -->
        <div x-show="step === 1" class="space-y-6">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Steg 1 — Företagsinformation</h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Företagsnamn *</label>
                        <input type="text" name="company_name" id="company_name" value="<?= $e((string) ($old['company_name'] ?? '')) ?>"
                               x-on:input="$refs.subdomain.value = $event.target.value.toLowerCase().replace(/[åä]/g,'a').replace(/[ö]/g,'o').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'')"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= !empty($errors['company_name']) ? 'border-red-500' : '' ?>">
                        <?php if (!empty($errors['company_name'])): ?>
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= $e($errors['company_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subdomain (auto-genereras)</label>
                        <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                            <input type="text" name="subdomain" id="subdomain" x-ref="subdomain" value="<?= $e((string) ($old['subdomain'] ?? '')) ?>"
                                   class="flex-1 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none">
                            <span class="flex items-center bg-gray-100 dark:bg-gray-600 px-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">.zync-erp.se</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kontaktperson</label>
                        <input type="text" name="contact_name" value="<?= $e((string) ($old['contact_name'] ?? '')) ?>"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-postadress *</label>
                        <input type="email" name="contact_email" value="<?= $e((string) ($old['contact_email'] ?? '')) ?>"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 <?= !empty($errors['contact_email']) ? 'border-red-500' : '' ?>">
                        <?php if (!empty($errors['contact_email'])): ?>
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400"><?= $e($errors['contact_email']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                        <input type="tel" name="contact_phone" value="<?= $e((string) ($old['contact_phone'] ?? '')) ?>"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Org.nr</label>
                        <input type="text" name="org_number" value="<?= $e((string) ($old['org_number'] ?? '')) ?>"
                               placeholder="556123-4567"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adress</label>
                    <input type="text" name="address" value="<?= $e((string) ($old['address'] ?? '')) ?>"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anteckningar</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= $e((string) ($old['notes'] ?? '')) ?></textarea>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="step = 2" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Nästa: Plan & Moduler →
                </button>
            </div>
        </div>

        <!-- Step 2: Plan & Modules -->
        <div x-show="step === 2" class="space-y-6">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3">Steg 2 — Plan & Status</h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Abonnemangsplan</label>
                        <div class="space-y-2">
                            <?php foreach ($plans as $plan): ?>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:border-indigo-400 dark:hover:border-indigo-500 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-900/20 transition-colors">
                                <input type="radio" name="plan" value="<?= $e((string) $plan['slug']) ?>"
                                       <?= (($old['plan'] ?? 'starter') === $plan['slug']) ? 'checked' : '' ?>
                                       class="mt-0.5 h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= $e((string) $plan['name']) ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= number_format((float) $plan['price_monthly'], 0, ',', ' ') ?> kr/mån &middot; max <?= (int) $plan['max_users'] >= 999 ? '∞' : (int) $plan['max_users'] ?> användare</p>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="trial" <?= (($old['status'] ?? 'trial') === 'trial') ? 'selected' : '' ?>>Trial (30 dagar)</option>
                                <option value="active" <?= (($old['status'] ?? '') === 'active') ? 'selected' : '' ?>>Aktiv</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Trial ger automatiskt 30 dagars prövotid.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max användare</label>
                            <input type="number" name="max_users" value="<?= $e((string) ($old['max_users'] ?? '10')) ?>" min="1"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <span class="font-semibold">ℹ️ Auto-provisioning:</span>
                        Moduler som ingår i vald plan aktiveras automatiskt. Du kan justera moduler manuellt efteråt.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" @click="step = 1" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Tillbaka</button>
                <button type="button" @click="step = 3" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Nästa: Bekräfta →
                </button>
            </div>
        </div>

        <!-- Step 3: Confirm -->
        <div x-show="step === 3" class="space-y-6">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-md">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">Steg 3 — Bekräfta</h2>

                <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <p>Kontrollera uppgifterna och klicka på <strong class="text-gray-900 dark:text-white">Skapa kund</strong> för att slutföra.</p>

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-4 space-y-1.5 text-xs">
                        <p><span class="font-medium text-gray-900 dark:text-white">Vad händer nu:</span></p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                            <li>Kunden registreras i databasen</li>
                            <li>Moduler kopplade till vald plan aktiveras automatiskt</li>
                            <li>Händelse loggas i kundens historik</li>
                            <li>Välkomstmeddelande (placeholder) markeras som skickat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="button" @click="step = 2" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">← Tillbaka</button>
                <button type="submit" class="rounded-lg bg-green-600 px-8 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    ✓ Skapa kund
                </button>
            </div>
        </div>

    </form>
</div>
