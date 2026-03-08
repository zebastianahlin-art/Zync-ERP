<?php

declare(strict_types=1);

use Modules\Maintenance\Controllers\MaintenanceDashboardController;
use Modules\Maintenance\Controllers\PreventiveMaintenanceController;
use Modules\Maintenance\Controllers\WorkOrderController;

return function ($app): void {
    // Dashboard
    $app->get('/maintenance', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->index();
        return $response;
    });

    $app->get('/maintenance/dashboard', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->index();
        return $response;
    });

    // Work orders
    $app->get('/maintenance/work-orders', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->index();
        return $response;
    });

    $app->get('/maintenance/work-orders/create', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->create();
        return $response;
    });

    $app->post('/maintenance/work-orders/store', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->store();
        return $response;
    });

    $app->get('/maintenance/work-orders/show', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->show();
        return $response;
    });

    $app->post('/maintenance/work-orders/update-status', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->updateStatus();
        return $response;
    });

    $app->post('/maintenance/work-orders/add-log', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->addLog();
        return $response;
    });

    // Materials
    $app->post('/maintenance/work-orders/add-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->addMaterial();
        return $response;
    });

    $app->post('/maintenance/work-orders/update-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->updateMaterial();
        return $response;
    });

    $app->post('/maintenance/work-orders/delete-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->deleteMaterial();
        return $response;
    });

    $app->post('/maintenance/work-orders/reserve-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->reserveMaterial();
        return $response;
    });

    $app->post('/maintenance/work-orders/issue-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->issueMaterial();
        return $response;
    });

    $app->post('/maintenance/work-orders/return-material', function ($request, $response) {
        $controller = $this->get(WorkOrderController::class);
        $controller->returnMaterial();
        return $response;
    });

    // Preventive maintenance
    $app->get('/maintenance/preventive', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->index();
        return $response;
    });

    $app->get('/maintenance/preventive/show', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->show();
        return $response;
    });

    $app->get('/maintenance/preventive/create', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->create();
        return $response;
    });

    $app->post('/maintenance/preventive/store', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->store();
        return $response;
    });

    $app->post('/maintenance/preventive/update', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->update();
        return $response;
    });

    $app->post('/maintenance/preventive/delete', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->delete();
        return $response;
    });

    $app->post('/maintenance/preventive/run-now', function ($request, $response) {
        $controller = $this->get(PreventiveMaintenanceController::class);
        $controller->runNow();
        return $response;
    });
};
