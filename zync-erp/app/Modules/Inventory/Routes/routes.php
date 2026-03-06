<?php

declare(strict_types=1);

use Slim\App;
use App\Modules\Inventory\Controllers\InventoryController;

return function (App $app): void {
    $app->get('/inventory', [InventoryController::class, 'index']);
};