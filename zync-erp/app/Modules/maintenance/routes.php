<?php

use Modules\Maintenance\Controllers\WorkOrderController;

return [
    ['GET',  '/maintenance/work-orders',                 [WorkOrderController::class, 'index']],
    ['GET',  '/maintenance/work-orders/create',          [WorkOrderController::class, 'create']],
    ['POST', '/maintenance/work-orders',                 [WorkOrderController::class, 'store']],
    ['GET',  '/maintenance/work-orders/show',            [WorkOrderController::class, 'show']],
    ['POST', '/maintenance/work-orders/status',          [WorkOrderController::class, 'updateStatus']],
    ['POST', '/maintenance/work-orders/add-log',         [WorkOrderController::class, 'addLog']],
];
