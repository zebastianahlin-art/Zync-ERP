<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\MachineRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MachineController extends Controller
{
    private MachineRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new MachineRepository();
    }

    /** GET /machines */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'machines/index', [
            'title' => 'Maskiner & Utrustning – ZYNC ERP',
            'tree'  => $this->repo->tree(),
            'stats' => $this->repo->stats(),
        ]);
    }

    /** GET /machines/{id} */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $machine = $this->repo->find((int) $args['id']);
        if (!$machine) {
            Flash::set('error', 'Maskinen hittades inte.');
            return $this->redirect($response, '/machines');
        }

        return $this->render($response, 'machines/show', [
            'title'      => $machine['name'] . ' – ZYNC ERP',
            'machine'    => $machine,
            'children'   => $this->repo->children((int) $args['id']),
            'spareParts' => $this->repo->spareParts((int) $args['id']),
            'documents'  => $this->repo->documents((int) $args['id']),
        ]);
    }

    /** GET /machines/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $q = $request->getQueryParams();
        return $this->render($response, 'machines/form', [
            'title'    => 'Ny maskin – ZYNC ERP',
            'machine'  => ['parent_id' => $q['parent'] ?? '', 'type' => 'machine', 'status' => 'operational', 'criticality' => 'B', 'is_active' => 1],
            'parents'  => $this->repo->parentOptions(),
            'errors'   => [],
            'isEdit'   => false,
        ]);
    }

    /** POST /machines */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseMachine($request);
        $errors = $this->validateMachine($data);

        if (!empty($errors)) {
            return $this->render($response, 'machines/form', [
                'title' => 'Ny maskin – ZYNC ERP', 'machine' => $data,
                'parents' => $this->repo->parentOptions(), 'errors' => $errors, 'isEdit' => false,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->create($data);
        Flash::set('success', 'Maskin/utrustning skapad.');
        return $this->redirect($response, '/machines/' . $id);
    }

    /** GET /machines/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $machine = $this->repo->find((int) $args['id']);
        if (!$machine) {
            Flash::set('error', 'Hittades inte.');
            return $this->redirect($response, '/machines');
        }

        return $this->render($response, 'machines/form', [
            'title'   => 'Redigera ' . $machine['name'] . ' – ZYNC ERP',
            'machine' => $machine,
            'parents' => $this->repo->parentOptions((int) $args['id']),
            'errors'  => [],
            'isEdit'  => true,
        ]);
    }

    /** POST /machines/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $data   = $this->parseMachine($request);
        $errors = $this->validateMachine($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'machines/form', [
                'title' => 'Redigera – ZYNC ERP', 'machine' => array_merge(['id' => $id], $data),
                'parents' => $this->repo->parentOptions($id), 'errors' => $errors, 'isEdit' => true,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Maskin/utrustning uppdaterad.');
        return $this->redirect($response, '/machines/' . $id);
    }

    /** POST /machines/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Maskin/utrustning borttagen.');
        return $this->redirect($response, '/machines');
    }

    /** POST /machines/{id}/spare-parts */
    public function addSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $b  = (array) $request->getParsedBody();
        $articleId = (int) ($b['article_id'] ?? 0);
        $qty       = (float) ($b['quantity'] ?? 1);
        $note      = trim((string) ($b['note'] ?? ''));

        if ($articleId < 1) {
            Flash::set('error', 'Välj en artikel.');
        } else {
            $this->repo->addSparePart($id, $articleId, $qty, $note ?: null);
            Flash::set('success', 'Reservdel kopplad.');
        }
        return $this->redirect($response, '/machines/' . $id);
    }

    /** POST /machines/{id}/spare-parts/{spId}/delete */
    public function removeSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->removeSparePart((int) $args['spId']);
        Flash::set('success', 'Reservdelskoppling borttagen.');
        return $this->redirect($response, '/machines/' . $args['id']);
    }

    /** POST /machines/{id}/documents */
    public function uploadDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $b  = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $title = trim((string) ($b['title'] ?? ''));
        $type  = $b['doc_type'] ?? 'other';

        if ($title === '') {
            Flash::set('error', 'Ange en titel för dokumentet.');
            return $this->redirect($response, '/machines/' . $id);
        }

        if (!isset($files['file']) || $files['file']->getError() !== UPLOAD_ERR_OK) {
            Flash::set('error', 'Ingen fil vald eller uppladdningsfel.');
            return $this->redirect($response, '/machines/' . $id);
        }

        $file = $files['file'];
        $ext  = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $name = 'machine_' . $id . '_' . time() . '.' . $ext;
        $dir  = dirname(__DIR__, 2) . '/storage/uploads/machines';
        if (!is_dir($dir)) mkdir($dir, 0775, true);
        $file->moveTo($dir . '/' . $name);

        $this->repo->addDocument($id, [
            'title'       => $title,
            'type'        => $type,
            'file_path'   => 'storage/uploads/machines/' . $name,
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getClientMediaType(),
            'uploaded_by' => Auth::id(),
        ]);

        Flash::set('success', 'Dokument uppladdat.');
        return $this->redirect($response, '/machines/' . $id);
    }

    /** POST /machines/{id}/documents/{docId}/delete */
    public function removeDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $path = $this->repo->removeDocument((int) $args['docId']);
        if ($path) {
            $full = dirname(__DIR__, 2) . '/' . $path;
            if (file_exists($full)) unlink($full);
        }
        Flash::set('success', 'Dokument borttaget.');
        return $this->redirect($response, '/machines/' . $args['id']);
    }

    /* ── Helpers ── */

    private function parseMachine(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'parent_id'     => ($b['parent_id'] ?? '') !== '' ? (int) $b['parent_id'] : null,
            'name'          => trim((string) ($b['name'] ?? '')),
            'code'          => strtoupper(trim((string) ($b['code'] ?? ''))),
            'type'          => $b['type'] ?? 'machine',
            'description'   => trim((string) ($b['description'] ?? '')),
            'manufacturer'  => trim((string) ($b['manufacturer'] ?? '')),
            'model'         => trim((string) ($b['model'] ?? '')),
            'serial_number' => trim((string) ($b['serial_number'] ?? '')),
            'year_installed' => ($b['year_installed'] ?? '') !== '' ? (int) $b['year_installed'] : null,
            'location'      => trim((string) ($b['location'] ?? '')),
            'status'        => $b['status'] ?? 'operational',
            'criticality'   => $b['criticality'] ?? 'B',
            'is_active'     => isset($b['is_active']) ? 1 : 0,
        ];
    }

    private function validateMachine(array $d, ?int $excludeId = null): array
    {
        $errors = [];
        if ($d['name'] === '') $errors['name'] = 'Namn är obligatoriskt.';
        if ($d['code'] === '') $errors['code'] = 'Kod är obligatoriskt.';
        elseif ($this->repo->codeExists($d['code'], $excludeId)) $errors['code'] = 'Koden används redan.';
        if (!in_array($d['type'], ['site','area','line','machine','sub_machine','component'])) $errors['type'] = 'Ogiltig typ.';
        if (!in_array($d['status'], ['operational','degraded','down','decommissioned'])) $errors['status'] = 'Ogiltig status.';
        if (!in_array($d['criticality'], ['A','B','C'])) $errors['criticality'] = 'Ogiltig kritikalitet.';
        return $errors;
    }
}
