<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PlaceholderController extends Controller
{
    /**
     * GET — renders a "coming soon" page for modules under development.
     *
     * Pass ?module=<name> or provide it via route attribute.
     */
    public function comingSoon(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $module = $args['module'] ?? ($request->getQueryParams()['module'] ?? 'Modulen');

        return $this->render($response, 'placeholder/coming-soon', [
            'title'  => htmlspecialchars($module, ENT_QUOTES, 'UTF-8') . ' – Kommer snart – ZYNC ERP',
            'module' => htmlspecialchars($module, ENT_QUOTES, 'UTF-8'),
        ]);
    }
}
