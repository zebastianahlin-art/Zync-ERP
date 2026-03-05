<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    private string $tmpDir;
    private View $view;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/zync-erp-view-test-' . uniqid();
        mkdir($this->tmpDir, 0755, true);
        mkdir($this->tmpDir . '/layouts', 0755, true);
        mkdir($this->tmpDir . '/partials', 0755, true);

        // Skapa en enkel layout
        file_put_contents(
            $this->tmpDir . '/layouts/test.php',
            '<html><body><?= $content ?></body></html>'
        );

        // Skapa en enkel vy
        file_put_contents(
            $this->tmpDir . '/hello.php',
            '<p>Hello, <?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>!</p>'
        );

        // Skapa en vy utan layout
        file_put_contents(
            $this->tmpDir . '/simple.php',
            '<span><?= $value ?></span>'
        );

        $this->view = new View($this->tmpDir);
    }

    protected function tearDown(): void
    {
        // Rensa temporärt katalog
        array_map('unlink', glob($this->tmpDir . '/layouts/*') ?: []);
        array_map('unlink', glob($this->tmpDir . '/partials/*') ?: []);
        array_map('unlink', glob($this->tmpDir . '/*.php') ?: []);
        rmdir($this->tmpDir . '/layouts');
        rmdir($this->tmpDir . '/partials');
        rmdir($this->tmpDir);
    }

    public function testRenderReturnsHtmlString(): void
    {
        $output = $this->view->render('hello', ['name' => 'World'], 'test');
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function testRenderInjectsVariables(): void
    {
        $output = $this->view->render('hello', ['name' => 'ZYNC'], 'test');
        $this->assertStringContainsString('Hello, ZYNC!', $output);
    }

    public function testRenderWrapsInLayout(): void
    {
        $output = $this->view->render('hello', ['name' => 'Test'], 'test');
        $this->assertStringContainsString('<html>', $output);
        $this->assertStringContainsString('<body>', $output);
        $this->assertStringContainsString('Hello, Test!', $output);
    }

    public function testRenderWithoutLayoutReturnsViewOnly(): void
    {
        $output = $this->view->render('simple', ['value' => 'direktvärde'], null);
        $this->assertSame('<span>direktvärde</span>', $output);
        $this->assertStringNotContainsString('<html>', $output);
    }

    public function testRenderEscapesSpecialCharacters(): void
    {
        $output = $this->view->render('hello', ['name' => '<script>alert("xss")</script>'], 'test');
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    public function testRenderThrowsForMissingView(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/View not found/');
        $this->view->render('nonexistent_view', []);
    }

    public function testRenderThrowsForMissingLayout(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Layout not found/');
        $this->view->render('simple', ['value' => 'test'], 'nonexistent_layout');
    }

    public function testRenderWithDotNotation(): void
    {
        mkdir($this->tmpDir . '/sub', 0755, true);
        file_put_contents($this->tmpDir . '/sub/page.php', '<div>Sub-vy</div>');

        $output = $this->view->render('sub.page', [], null);
        $this->assertSame('<div>Sub-vy</div>', $output);

        unlink($this->tmpDir . '/sub/page.php');
        rmdir($this->tmpDir . '/sub');
    }

    public function testDataIsNotLeakedBetweenViews(): void
    {
        $output1 = $this->view->render('simple', ['value' => 'värde-1'], null);
        $output2 = $this->view->render('simple', ['value' => 'värde-2'], null);

        $this->assertSame('<span>värde-1</span>', $output1);
        $this->assertSame('<span>värde-2</span>', $output2);
    }
}
