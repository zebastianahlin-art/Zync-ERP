<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Flash;
use PHPUnit\Framework\TestCase;

class FlashTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testSetStoresMessageInSession(): void
    {
        Flash::set('success', 'Allt gick bra!');
        $this->assertArrayHasKey('_flash_success', $_SESSION);
        $this->assertSame('Allt gick bra!', $_SESSION['_flash_success']);
    }

    public function testGetReturnsMessageAndClearsIt(): void
    {
        Flash::set('error', 'Något gick fel.');
        $message = Flash::get('error');

        $this->assertSame('Något gick fel.', $message);
        $this->assertArrayNotHasKey('_flash_error', $_SESSION);
    }

    public function testGetReturnsNullForMissingKey(): void
    {
        $this->assertNull(Flash::get('nonexistent'));
    }

    public function testGetClearsAfterFirstRead(): void
    {
        Flash::set('info', 'Information');
        Flash::get('info');
        $this->assertNull(Flash::get('info'));
    }

    public function testMultipleKeysAreIndependent(): void
    {
        Flash::set('success', 'OK');
        Flash::set('error', 'Fel');

        $this->assertSame('OK', Flash::get('success'));
        $this->assertNull(Flash::get('success'));
        $this->assertSame('Fel', Flash::get('error'));
    }

    public function testSetOverwritesExistingValue(): void
    {
        Flash::set('success', 'Första');
        Flash::set('success', 'Andra');
        $this->assertSame('Andra', Flash::get('success'));
    }

    public function testEmptyStringIsValidMessage(): void
    {
        Flash::set('warning', '');
        $this->assertSame('', Flash::get('warning'));
    }
}
