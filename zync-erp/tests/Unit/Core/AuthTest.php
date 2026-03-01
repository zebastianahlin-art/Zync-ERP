<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Auth;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        // Start a session so session_regenerate_id() works inside Auth::login()
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Reset session state before each test
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testCheckReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse(Auth::check());
    }

    public function testCheckReturnsTrueAfterLogin(): void
    {
        Auth::login(42);
        $this->assertTrue(Auth::check());
    }

    public function testIdReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(Auth::id());
    }

    public function testIdReturnsUserIdAfterLogin(): void
    {
        Auth::login(99);
        $this->assertSame(99, Auth::id());
    }

    public function testLoginStoresUserId(): void
    {
        Auth::login(7);
        $this->assertSame(7, Auth::id());
        $this->assertTrue(Auth::check());
    }

    public function testLogoutClearsSession(): void
    {
        Auth::login(5);
        $this->assertTrue(Auth::check());

        Auth::logout();
        $this->assertFalse(Auth::check());
        $this->assertNull(Auth::id());
    }

    public function testUserReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(Auth::user());
    }
}
