<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\ObjectNavigatorRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ObjectNavigatorController extends Controller
{
    private ObjectNavigatorRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ObjectNavigatorRepository();
    }

    // ─── GET /objects ─────────────────────────────────────────────────────

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $query  = trim($params['q'] ?? '');
        $type   = trim($params['type'] ?? '');

        $results  = ($query !== '' || $type !== '') ? $this->repo->search($query, $type) : [];
        $typeCounts = $this->repo->countByType();

        return $this->render($response, 'objects/index', [
            'title'      => 'ObjektNavigator – ZYNC ERP',
            'query'      => $query,
            'type'       => $type,
            'results'    => $results,
            'typeCounts' => $typeCounts,
        ]);
    }

    // ─── GET /objects/tree ────────────────────────────────────────────────

    public function tree(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $roots = $this->repo->tree();

        // Group by type for the tree display
        $byType = [];
        foreach ($roots as $obj) {
            $byType[$obj['object_type']][] = $obj;
        }

        return $this->render($response, 'objects/tree', [
            'title'  => 'Objektträd – ZYNC ERP',
            'byType' => $byType,
        ]);
    }

    // ─── GET /objects/search (AJAX JSON) ─────────────────────────────────

    public function search(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $query  = trim($params['q'] ?? '');
        $type   = trim($params['type'] ?? '');

        if (strlen($query) < 2 && $type === '') {
            return $this->json($response, []);
        }

        $results = $this->repo->search($query, $type);
        return $this->json($response, $results);
    }

    // ─── GET /objects/{type}/{id}/children (AJAX JSON) ────────────────────

    public function children(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $type = $args['type'] ?? '';
        $id   = (int) ($args['id'] ?? 0);

        $children = $this->repo->children($type, $id);
        return $this->json($response, $children);
    }

    // ─── GET /objects/{type}/{id} ─────────────────────────────────────────

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $type = $args['type'] ?? '';
        $id   = (int) ($args['id'] ?? 0);

        $object = $this->repo->findByTypeAndId($type, $id);
        if (!$object) {
            Flash::set('error', 'Objektet hittades inte.');
            return $this->redirect($response, '/objects');
        }

        $children = $this->repo->children($type, $id);

        return $this->render($response, 'objects/show', [
            'title'    => htmlspecialchars($object['display_name'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'object'   => $object,
            'children' => $children,
        ]);
    }

    // ─── POST /objects/sync (admin) ───────────────────────────────────────

    public function sync(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->repo->sync();
        Flash::set('success', 'Objektregistret har synkroniserats.');
        return $this->redirect($response, '/objects');
    }
}
