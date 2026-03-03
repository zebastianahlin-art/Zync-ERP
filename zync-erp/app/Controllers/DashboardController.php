<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends Controller
{
    /** GET /dashboard – protected dashboard page. */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($guard = $this->requireAuth($response)) {
            return $guard;
        }

        $db = Database::pdo();

        // KPI counts
        $customers = (int) $db->query("SELECT COUNT(*) FROM customers WHERE is_deleted = 0")->fetchColumn();
        $suppliers = (int) $db->query("SELECT COUNT(*) FROM suppliers WHERE is_deleted = 0")->fetchColumn();
        $articles  = (int) $db->query("SELECT COUNT(*) FROM articles WHERE is_deleted = 0")->fetchColumn();
        $users     = (int) $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();

        // Recent customers
        $recentCustomers = $db->query(
            "SELECT id, customer_number, name, email, status, created_at
             FROM customers WHERE is_deleted = 0
             ORDER BY created_at DESC LIMIT 5"
        )->fetchAll();

        // Recent suppliers
        $recentSuppliers = $db->query(
            "SELECT id, org_number, name, email, created_at
             FROM suppliers WHERE is_deleted = 0
             ORDER BY created_at DESC LIMIT 5"
        )->fetchAll();

        // Recent activity
        $recentActivity = $db->query(
            "SELECT sa.*, c.name AS customer_name
             FROM sales_activities sa
             LEFT JOIN customers c ON c.id = sa.customer_id
             ORDER BY sa.activity_date DESC LIMIT 10"
        )->fetchAll();

        return $this->render($response, 'dashboard/index', [
            'title'           => 'Dashboard – ZYNC ERP',
            'userId'          => Auth::id(),
            'currentUser'     => Auth::user(),
            'customers'       => $customers,
            'suppliers'       => $suppliers,
            'articles'        => $articles,
            'users'           => $users,
            'recentCustomers' => $recentCustomers,
            'recentSuppliers' => $recentSuppliers,
            'recentActivity'  => $recentActivity,
        ]);
    }
}
