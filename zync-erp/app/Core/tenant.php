<?php

namespace App\Core;

class Tenant
{
    protected static $tenantId = null;

    public static function set($id)
    {
        self::$tenantId = $id;
    }

    public static function get()
    {
        return self::$tenantId;
    }
}