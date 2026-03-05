<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\TenantContext;
use PHPUnit\Framework\TestCase;

class TenantContextTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset singleton state between tests
        TenantContext::getInstance()->clearTenant();
    }

    protected function tearDown(): void
    {
        TenantContext::getInstance()->clearTenant();
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $a = TenantContext::getInstance();
        $b = TenantContext::getInstance();
        $this->assertSame($a, $b);
    }

    public function testHasTenantReturnsFalseInitially(): void
    {
        $this->assertFalse(TenantContext::getInstance()->hasTenant());
    }

    public function testHasTenantReturnsTrueAfterSet(): void
    {
        TenantContext::getInstance()->setTenant(['id' => 1, 'company_name' => 'Test AB']);
        $this->assertTrue(TenantContext::getInstance()->hasTenant());
    }

    public function testGetReturnsFieldValue(): void
    {
        TenantContext::getInstance()->setTenant(['id' => 42, 'company_name' => 'ACME AB', 'plan' => 'enterprise']);
        $ctx = TenantContext::getInstance();

        $this->assertSame(42, $ctx->get('id'));
        $this->assertSame('ACME AB', $ctx->get('company_name'));
        $this->assertSame('enterprise', $ctx->get('plan'));
    }

    public function testGetReturnsNullForMissingField(): void
    {
        TenantContext::getInstance()->setTenant(['id' => 1]);
        $this->assertNull(TenantContext::getInstance()->get('nonexistent_field'));
    }

    public function testGetReturnsNullWhenNoTenant(): void
    {
        $this->assertNull(TenantContext::getInstance()->get('id'));
    }

    public function testGetTenantReturnsFullArray(): void
    {
        $data = ['id' => 5, 'company_name' => 'Bolaget AB', 'modules' => ['hr', 'finance']];
        TenantContext::getInstance()->setTenant($data);
        $this->assertSame($data, TenantContext::getInstance()->getTenant());
    }

    public function testGetTenantReturnsNullWhenNotSet(): void
    {
        $this->assertNull(TenantContext::getInstance()->getTenant());
    }

    public function testClearTenantResetsState(): void
    {
        TenantContext::getInstance()->setTenant(['id' => 1]);
        TenantContext::getInstance()->clearTenant();
        $this->assertFalse(TenantContext::getInstance()->hasTenant());
        $this->assertNull(TenantContext::getInstance()->getTenant());
    }

    public function testIsModuleEnabledReturnsTrueWithStringModules(): void
    {
        TenantContext::getInstance()->setTenant([
            'id'      => 1,
            'modules' => ['hr', 'finance', 'maintenance'],
        ]);

        $this->assertTrue(TenantContext::getInstance()->isModuleEnabled('hr'));
        $this->assertTrue(TenantContext::getInstance()->isModuleEnabled('finance'));
        $this->assertFalse(TenantContext::getInstance()->isModuleEnabled('saas'));
    }

    public function testIsModuleEnabledReturnsTrueWithArrayModules(): void
    {
        TenantContext::getInstance()->setTenant([
            'id'      => 1,
            'modules' => [
                ['module_slug' => 'hr', 'is_active' => 1],
                ['module_slug' => 'finance', 'is_active' => 0],
                ['module_slug' => 'maintenance', 'is_active' => 1],
            ],
        ]);

        $ctx = TenantContext::getInstance();
        $this->assertTrue($ctx->isModuleEnabled('hr'));
        $this->assertFalse($ctx->isModuleEnabled('finance'));
        $this->assertTrue($ctx->isModuleEnabled('maintenance'));
        $this->assertFalse($ctx->isModuleEnabled('saas'));
    }

    public function testIsModuleEnabledReturnsTrueWhenNoTenant(): void
    {
        // Without tenant context (single-tenant mode), all modules are enabled
        $this->assertTrue(TenantContext::getInstance()->isModuleEnabled('any_module'));
    }

    public function testIsModuleEnabledReturnsFalseWithEmptyModules(): void
    {
        TenantContext::getInstance()->setTenant(['id' => 1, 'modules' => []]);
        $this->assertFalse(TenantContext::getInstance()->isModuleEnabled('hr'));
    }
}
