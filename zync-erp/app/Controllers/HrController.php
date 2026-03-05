<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\HrRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HrController extends Controller
{
    private HrRepository $repo;
    public function __construct()
    {
        parent::__construct();
        $this->repo = new HrRepository();
    }

    public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/dashboard', [
            'title'          => 'HR Dashboard – ZYNC ERP',
            'stats'          => $this->repo->stats(),
            'upcomingEvents' => $this->repo->upcomingEvents(),
            'success'        => Flash::get('success'),
        ]);
    }
}
