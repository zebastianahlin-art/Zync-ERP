<?php

namespace App\Modules\Inventory\Controllers;

use App\Core\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        return $this->view('Inventory/index');
    }
}