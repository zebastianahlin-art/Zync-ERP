<?php

namespace App\Core;

class TenantResolver
{
    public static function resolve()
    {
        $host = $_SERVER['HTTP_HOST'];

        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT id FROM tenants WHERE domain = ?");
        $stmt->execute([$host]);

        $tenant = $stmt->fetch();

        if (!$tenant) {
            die("Tenant not found");
        }

        Tenant::set($tenant['id']);
    }
}