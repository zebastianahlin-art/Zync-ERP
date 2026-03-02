<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\EquipmentRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EquipmentController extends Controller
{
    private EquipmentRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new EquipmentRepository();
    }

    /* ─── Lista ─── */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        return $this->render($response, 'equipment/index', [
            'title'       => 'Utrustning – ZYNC ERP',
            'equipment'   => $this->repo->all($p['type'] ?? null, $p['status'] ?? null, isset($p['department']) ? (int) $p['department'] : null),
            'stats'       => $this->repo->stats(),
            'departments' => $this->repo->allDepartments(),
            'filter'      => $p,
        ]);
    }

    /* ─── Trädvy ─── */
    public function tree(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'equipment/tree', [
            'title' => 'Utrustningsträd – ZYNC ERP',
            'tree'  => $this->repo->tree(),
        ]);
    }

    /* ─── Detalj ─── */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $eq = $this->repo->find((int) $args['id']);
        if (!$eq) {
            Flash::set('error', 'Utrustning hittades inte.');
            return $this->redirect($response, '/equipment');
        }
        return $this->render($response, 'equipment/show', [
            'title'      => $eq['name'] . ' – ZYNC ERP',
            'eq'         => $eq,
            'children'   => $this->repo->children((int) $eq['id']),
            'documents'  => $this->repo->documents((int) $eq['id']),
            'spareParts' => $this->repo->spareParts((int) $eq['id']),
        ]);
    }

    /* ─── Skapa ─── */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        $type = $p['type'] ?? 'machine';
        return $this->render($response, 'equipment/form', [
            'title'       => 'Ny utrustning – ZYNC ERP',
            'eq'          => ['type' => $type, 'status' => 'operational', 'criticality' => 'B', 'equipment_number' => $this->repo->nextNumber($type)],
            'parents'     => $this->repo->allParentOptions(),
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'isNew'       => true,
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'equipment/form', [
                'title'       => 'Ny utrustning – ZYNC ERP',
                'eq'          => $data,
                'parents'     => $this->repo->allParentOptions(),
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
                'isNew'       => true,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->create($data);
        Flash::set('success', 'Utrustning har skapats.');
        return $this->redirect($response, '/equipment/' . $id);
    }

    /* ─── Redigera ─── */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $eq = $this->repo->find((int) $args['id']);
        if (!$eq) {
            Flash::set('error', 'Utrustning hittades inte.');
            return $this->redirect($response, '/equipment');
        }
        return $this->render($response, 'equipment/form', [
            'title'       => 'Redigera ' . $eq['name'] . ' – ZYNC ERP',
            'eq'          => $eq,
            'parents'     => $this->repo->allParentOptions((int) $eq['id']),
            'departments' => $this->repo->allDepartments(),
            'errors'      => [],
            'isNew'       => false,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        $eq = $this->repo->find($id);
        if (!$eq) {
            Flash::set('error', 'Utrustning hittades inte.');
            return $this->redirect($response, '/equipment');
        }

        $data   = $this->parseBody($request);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'equipment/form', [
                'title'       => 'Redigera ' . $eq['name'] . ' – ZYNC ERP',
                'eq'          => array_merge($eq, $data),
                'parents'     => $this->repo->allParentOptions($id),
                'departments' => $this->repo->allDepartments(),
                'errors'      => $errors,
                'isNew'       => false,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Utrustning har uppdaterats.');
        return $this->redirect($response, '/equipment/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Utrustning har tagits bort.');
        return $this->redirect($response, '/equipment');
    }

    /* ─── Dokument ─── */
    public function uploadDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $eqId  = (int) $args['id'];
        $body  = (array) $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $file  = $files['document_file'] ?? null;

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            Flash::set('error', 'Ingen fil vald.');
            return $this->redirect($response, '/equipment/' . $eqId);
        }

        $ext      = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $safeName = uniqid('eqdoc_', true) . '.' . strtolower($ext);
        $destDir  = __DIR__ . '/../../public/uploads/equipment';
        $file->moveTo($destDir . '/' . $safeName);

        $this->repo->addDocument([
            'equipment_id' => $eqId,
            'name'         => trim($body['doc_name'] ?? $file->getClientFilename()),
            'type'         => $body['doc_type'] ?? 'other',
            'file_path'    => '/uploads/equipment/' . $safeName,
            'file_name'    => $file->getClientFilename(),
            'file_size'    => $file->getSize(),
            'uploaded_by'  => Auth::id(),
        ]);

        Flash::set('success', 'Dokument har laddats upp.');
        return $this->redirect($response, '/equipment/' . $eqId);
    }

    public function downloadDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $doc = $this->repo->findDocument((int) $args['docId']);
        if (!$doc) {
            Flash::set('error', 'Dokument hittades inte.');
            return $this->redirect($response, '/equipment');
        }
        $fullPath = __DIR__ . '/../../public' . $doc['file_path'];
        if (!file_exists($fullPath)) {
            Flash::set('error', 'Filen finns inte.');
            return $this->redirect($response, '/equipment/' . $args['id']);
        }
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        $response = $response
            ->withHeader('Content-Type', $mime)
            ->withHeader('Content-Disposition', 'inline; filename="' . $doc['file_name'] . '"')
            ->withHeader('Content-Length', (string) filesize($fullPath));
        $response->getBody()->write(file_get_contents($fullPath));
        return $response;
    }

    public function deleteDocument(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteDocument((int) $args['docId']);
        Flash::set('success', 'Dokument har tagits bort.');
        return $this->redirect($response, '/equipment/' . $args['id']);
    }

    /* ─── Reservdelar ─── */
    public function addSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $eqId = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->addSparePart($eqId, (int) $body['article_id'], (float) ($body['quantity_needed'] ?? 1), $body['notes'] ?? null);
        Flash::set('success', 'Reservdel har kopplats.');
        return $this->redirect($response, '/equipment/' . $eqId);
    }

    public function removeSparePart(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->removeSparePart((int) $args['spareId']);
        Flash::set('success', 'Reservdelskoppling har tagits bort.');
        return $this->redirect($response, '/equipment/' . $args['id']);
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'parent_id'        => (int) ($b['parent_id'] ?? 0) ?: null,
            'equipment_number' => trim((string) ($b['equipment_number'] ?? '')),
            'name'             => trim((string) ($b['name'] ?? '')),
            'type'             => trim((string) ($b['type'] ?? 'machine')),
            'description'      => trim((string) ($b['description'] ?? '')),
            'location'         => trim((string) ($b['location'] ?? '')),
            'manufacturer'     => trim((string) ($b['manufacturer'] ?? '')),
            'model'            => trim((string) ($b['model'] ?? '')),
            'serial_number'    => trim((string) ($b['serial_number'] ?? '')),
            'year_installed'   => trim((string) ($b['year_installed'] ?? '')),
            'status'           => trim((string) ($b['status'] ?? 'operational')),
            'criticality'      => trim((string) ($b['criticality'] ?? 'B')),
            'department_id'    => (int) ($b['department_id'] ?? 0) ?: null,
            'notes'            => trim((string) ($b['notes'] ?? '')),
        ];
    }

    private function validate(array $data, ?int $excludeId = null): array
    {
        $errors = [];
        if ($data['equipment_number'] === '') $errors['equipment_number'] = 'Utrustningsnummer krävs.';
        if ($data['name'] === '')             $errors['name']             = 'Namn krävs.';
        return $errors;
    }
}
