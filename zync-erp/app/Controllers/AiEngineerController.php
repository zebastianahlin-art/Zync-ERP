<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Models\AiEngineerRepository;
use App\Models\MachineRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AiEngineerController extends Controller
{
    private AiEngineerRepository $repo;
    private MachineRepository $machineRepo;

    public function __construct()
    {
        parent::__construct();
        $this->repo        = new AiEngineerRepository();
        $this->machineRepo = new MachineRepository();
    }

    // ─── GET /maintenance/ai ──────────────────────────────────────────────

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/ai/index', [
            'title'          => 'AI-ingenjör – ZYNC ERP',
            'stats'          => $this->repo->dashboardStats(),
            'topFaulty'      => $this->repo->topFaultyMachines(10),
            'trending'       => $this->repo->trendingFaults(),
            'mtbf'           => $this->repo->mtbfPerMachine(),
        ]);
    }

    // ─── GET /maintenance/ai/recommendations ──────────────────────────────

    public function recommendations(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/ai/recommendations', [
            'title'           => 'AI-rekommendationer – ZYNC ERP',
            'recommendations' => $this->repo->recommendations(),
        ]);
    }

    // ─── GET /maintenance/ai/machine/{id} ─────────────────────────────────

    public function machineHealth(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) ($args['id'] ?? 0);
        $health = $this->repo->machineHealth($id);

        if (!$health) {
            Flash::set('error', 'Maskinen hittades inte.');
            return $this->redirect($response, '/maintenance/ai');
        }

        return $this->render($response, 'maintenance/ai/machine-health', [
            'title'  => 'Hälsorapport: ' . htmlspecialchars($health['machine']['name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'health' => $health,
        ]);
    }
}
