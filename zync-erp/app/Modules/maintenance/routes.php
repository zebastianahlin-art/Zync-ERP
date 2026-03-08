<?php

use Modules\Maintenance\Controllers\MaintenanceDashboardController;
use Modules\Maintenance\Controllers\PreventiveMaintenanceController;
use Modules\Maintenance\Controllers\WorkOrderController;

return [
    // Dashboard
    ['GET',  '/maintenance', [MaintenanceDashboardController::class, 'index']],
    ['GET',  '/maintenance/dashboard', [MaintenanceDashboardController::class, 'index']],

    // Work orders
    ['GET',  '/maintenance/work-orders', [WorkOrderController::class, 'index']],
    ['GET',  '/maintenance/work-orders/create', [WorkOrderController::class, 'create']],
    ['POST', '/maintenance/work-orders/store', [WorkOrderController::class, 'store']],
    ['GET',  '/maintenance/work-orders/show', [WorkOrderController::class, 'show']],
    ['POST', '/maintenance/work-orders/update-status', [WorkOrderController::class, 'updateStatus']],
    ['POST', '/maintenance/work-orders/add-log', [WorkOrderController::class, 'addLog']],

    // Work order materials
    ['POST', '/maintenance/work-orders/add-material', [WorkOrderController::class, 'addMaterial']],
    ['POST', '/maintenance/work-orders/update-material', [WorkOrderController::class, 'updateMaterial']],
    ['POST', '/maintenance/work-orders/delete-material', [WorkOrderController::class, 'deleteMaterial']],

    // New material inventory actions
    ['POST', '/maintenance/work-orders/reserve-material', [WorkOrderController::class, 'reserveMaterial']],
    ['POST', '/maintenance/work-orders/issue-material', [WorkOrderController::class, 'issueMaterial']],
    ['POST', '/maintenance/work-orders/return-material', [WorkOrderController::class, 'returnMaterial']],

    // Preventive maintenance
    ['GET',  '/maintenance/preventive', [PreventiveMaintenanceController::class, 'index']],
    ['GET',  '/maintenance/preventive/create', [PreventiveMaintenanceController::class, 'create']],
    ['POST', '/maintenance/preventive/store', [PreventiveMaintenanceController::class, 'store']],
    ['GET',  '/maintenance/preventive/show', [PreventiveMaintenanceController::class, 'show']],
    ['POST', '/maintenance/preventive/update', [PreventiveMaintenanceController::class, 'update']],
    ['POST', '/maintenance/preventive/delete', [PreventiveMaintenanceController::class, 'delete']],
    ['POST', '/maintenance/preventive/run-now', [PreventiveMaintenanceController::class, 'runNow']],
];
