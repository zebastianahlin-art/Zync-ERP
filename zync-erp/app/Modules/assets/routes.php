<?php

use Modules\Assets\Controllers\AssetNodeController;

return [
    ['GET',  '/assets',                   [AssetNodeController::class, 'index']],
    ['GET',  '/assets/create',            [AssetNodeController::class, 'create']],
    ['POST', '/assets',                   [AssetNodeController::class, 'store']],
    ['GET',  '/assets/edit',              [AssetNodeController::class, 'edit']],
    ['POST', '/assets/update',            [AssetNodeController::class, 'update']],
    ['POST', '/assets/archive',           [AssetNodeController::class, 'archive']],
];
