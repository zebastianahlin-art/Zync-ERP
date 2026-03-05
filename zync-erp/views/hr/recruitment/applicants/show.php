<?php if (!empty($success)): ?>
<div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-green-800 dark:text-green-300 text-sm">
    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
</div>
<?php endif; ?>

<?php
$pipelineSteps = ['new' => 'Ny', 'screening' => 'Granskning', 'interview' => 'Intervju', 'offer' => 'Erbjudande', 'hired' => 'Anst&#228;lld', 'rejected' => 'Avvisad'];
$currentStep = $applicant['pipeline_step'] ?? $applicant['status'] ?? 'new';
$stepColors = [
    'new'       => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
    'screening' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
    'interview' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
    'offer'     => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
    'hired'     => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
    'rejected'  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
];
$rating = (int) ($applicant['rating'] ?? 0);
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="/hr/recruitment/positions/<?= (int)$applicant['position_id'] ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600">&larr; <?= htmlspecialchars($applicant['position_title'] ?? 'Tj&#228;nst', ENT_QUOTES, 'UTF-8') ?></a>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars(($applicant['first_name'] ?? '') . ' ' . ($applicant['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $stepColors[$currentStep] ?? $stepColors['new'] ?>">
                    <?= $pipelineSteps[$currentStep] ?? htmlspecialchars($currentStep, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php if ($rating > 0): ?>
                <span class="text-yellow-500">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= $rating ? '&#9733;' : '&#9734;' ?>
                    <?php endfor; ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($currentStep !== 'hired' && $currentStep !== 'rejected'): ?>
            <a href="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/convert"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition"
               onclick="return confirm('Konvertera s&#246;kande till anst&#228;lld?')">
                &#8594; Konvertera till anst&#228;lld
            </a>
            <?php endif; ?>
            <a href="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/edit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Redigera</a>
            <form method="POST" action="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/delete" class="inline" onsubmit="return confirm('Ta bort s&#246;kande?')">
                <?= \App\Core\Csrf::field() ?>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Ta bort</button>
            </form>
        </div>
    </div>

    <!-- Pipeline visualization -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Pipeline</h3>
        <div class="flex items-center gap-1">
            <?php $stepKeys = array_keys($pipelineSteps); ?>
            <?php foreach ($pipelineSteps as $step => $label): ?>
            <?php if ($step === 'rejected') continue; // Show separately ?>
            <?php
                $isDone    = array_search($step, $stepKeys) <= array_search($currentStep, $stepKeys) && $currentStep !== 'rejected';
                $isCurrent = $step === $currentStep;
            ?>
            <div class="flex-1 text-center">
                <div class="h-2 rounded-full <?= $isDone ? 'bg-indigo-500' : 'bg-gray-200 dark:bg-gray-700' ?> transition-all"></div>
                <span class="text-xs mt-1 block <?= $isCurrent ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' ?>">
                    <?= $label ?>
                </span>
            </div>
            <?php if ($step !== 'hired'): ?>
            <div class="w-2 h-2 rounded-full <?= $isDone ? 'bg-indigo-400' : 'bg-gray-200 dark:bg-gray-700' ?>"></div>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($currentStep === 'rejected'): ?>
            <div class="ml-4 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Avvisad</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tj&#228;nst</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['position_title'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">E-post</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['email'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Telefon</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['phone'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ans&#246;kningsdatum</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($applicant['applied_at'] ?? '&#8212;', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php if (!empty($applicant['cv_url'])): ?>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">CV</dt>
            <dd class="mt-0.5 text-sm">
                <a href="<?= htmlspecialchars($applicant['cv_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                    &#128196; &#214;ppna CV
                </a>
            </dd>
        </div>
        <?php endif; ?>
        <?php if (!empty($applicant['salary_expectation'])): ?>
        <div>
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">L&#246;neF&#246;rv&#228;ntning</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white"><?= number_format((float)$applicant['salary_expectation'], 0, ',', ' ') ?> kr/m&#229;n</dd>
        </div>
        <?php endif; ?>
        <?php if (!empty($applicant['notes'])): ?>
        <div class="md:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Anteckningar</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($applicant['notes'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
        <?php if (!empty($applicant['cover_letter'])): ?>
        <div class="md:col-span-2">
            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Personligt brev</dt>
            <dd class="mt-0.5 text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($applicant['cover_letter'], ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pipeline & Rating update -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Uppdatera pipeline &amp; betyg</h3>
        <form method="POST" action="/hr/recruitment/applicants/<?= (int)$applicant['id'] ?>/status" class="flex flex-wrap items-end gap-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Pipeline-steg</label>
                <select id="status" name="status"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                    <?php foreach ($pipelineSteps as $step => $label): ?>
                    <option value="<?= htmlspecialchars($step, ENT_QUOTES, 'UTF-8') ?>" <?= $currentStep === $step ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Uppdatera</button>
        </form>
    </div>
</div>
