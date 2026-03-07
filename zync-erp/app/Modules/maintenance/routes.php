<?php

use Modules\Maintenance\Controllers\MaintenanceDashboardController;
use Modules\Maintenance\Controllers\PreventiveMaintenanceController;
use Modules\Maintenance\Controllers\WorkOrderController;

return [
    ['GET',  '/maintenance',                             [MaintenanceDashboardController::class, 'index']],

    ['GET',  '/maintenance/work-orders',                 [WorkOrderController::class, 'index']],
    ['GET',  '/maintenance/work-orders/create',          [WorkOrderController::class, 'create']],
    ['POST', '/maintenance/work-orders',                 [WorkOrderController::class, 'store']],
    ['GET',  '/maintenance/work-orders/show',            [WorkOrderController::class, 'show']],
    ['POST', '/maintenance/work-orders/status',          [WorkOrderController::class, 'updateStatus']],
    ['POST', '/maintenance/work-orders/add-log',         [WorkOrderController::class, 'addLog']],

    ['GET',  '/maintenance/preventive',                  [PreventiveMaintenanceController::class, 'index']],
    ['GET',  '/maintenance/preventive/create',           [PreventiveMaintenanceController::class, 'create']],
    ['POST', '/maintenance/preventive',                  [PreventiveMaintenanceController::class, 'store']],
    ['GET',  '/maintenance/preventive/show',             [PreventiveMaintenanceController::class, 'show']],
    ['POST', '/maintenance/preventive/run-due',          [PreventiveMaintenanceController::class, 'runDueSchedules']],
    ['POST', '/maintenance/preventive/complete-run',     [PreventiveMaintenanceController::class, 'completeRun']],
];
