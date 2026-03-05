<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Integrations\AiAdapter;
use App\Integrations\ImapAdapter;
use App\Integrations\IntegrationInterface;
use App\Integrations\OpenBankingAdapter;
use App\Integrations\PeppolAdapter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * IntegrationController — Administrera externa integrationer.
 *
 * Kräver administratörsrättigheter (role_level >= 8, sätts via RoleMiddleware).
 */
class IntegrationController extends Controller
{
    /** @var array<string, IntegrationInterface> */
    private array $adapters;

    public function __construct()
    {
        parent::__construct();
        $this->adapters = [
            'peppol'       => new PeppolAdapter(),
            'imap'         => new ImapAdapter(),
            'open_banking' => new OpenBankingAdapter(),
            'ai'           => new AiAdapter(),
        ];
    }

    /** GET /admin/integrations — Lista alla integrationer */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $integrations = [];
        foreach ($this->adapters as $slug => $adapter) {
            $integrations[] = [
                'slug'          => $slug,
                'name'          => $adapter->getName(),
                'is_configured' => $adapter->isConfigured(),
            ];
        }

        return $this->render($response, 'integrations/index', [
            'title'        => 'Integrationer – ZYNC ERP',
            'integrations' => $integrations,
            'breadcrumbs'  => [
                ['label' => 'Dashboard', 'url' => '/dashboard'],
                ['label' => 'Integrationer'],
            ],
        ]);
    }

    /** POST /admin/integrations/{slug}/test — Testa anslutning */
    public function test(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $slug = $args['slug'] ?? '';

        if (!isset($this->adapters[$slug])) {
            Flash::set('error', 'Integrationen hittades inte.');
            return $this->redirect($response, '/admin/integrations');
        }

        $adapter = $this->adapters[$slug];

        if (!$adapter->isConfigured()) {
            Flash::set('error', "Integrationen '{$adapter->getName()}' är inte konfigurerad.");
            return $this->redirect($response, '/admin/integrations');
        }

        try {
            $ok = $adapter->testConnection();
            if ($ok) {
                Flash::set('success', "Anslutningstest för '{$adapter->getName()}' lyckades.");
            } else {
                Flash::set('error', "Anslutningstest för '{$adapter->getName()}' misslyckades.");
            }
        } catch (\Throwable $e) {
            Flash::set('error', "Fel vid test av '{$adapter->getName()}': " . $e->getMessage());
        }

        return $this->redirect($response, '/admin/integrations');
    }
}
