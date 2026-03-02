<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\FaultReportRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FaultReportController extends Controller
{
    private FaultReportRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new FaultReportRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        return $this->render($response, 'maintenance/faults/index', [
            'title'     => 'Felanmälan – ZYNC ERP',
            'reports'   => $this->repo->all($p['status'] ?? null, $p['priority'] ?? null, isset($p['equipment']) ? (int) $p['equipment'] : null),
            'stats'     => $this->repo->stats(),
            'equipment' => $this->repo->allEquipment(),
            'filter'    => $p,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'maintenance/faults/form', [
            'title'     => 'Ny felanmälan – ZYNC ERP',
            'report'    => ['priority' => 'medium', 'fault_type' => 'other', 'status' => 'reported', 'report_number' => $this->repo->nextNumber()],
            'equipment' => $this->repo->allEquipment(),
            'errors'    => [],
            'isNew'     => true,
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->parseBody($request);
        $file   = $this->handleUpload($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'maintenance/faults/form', [
                'title'     => 'Ny felanmälan – ZYNC ERP',
                'report'    => $data,
                'equipment' => $this->repo->allEquipment(),
                'errors'    => $errors,
                'isNew'     => true,
            ]);
        }

        if ($file) {
            $data['image_path'] = $file['path'];
            $data['image_name'] = $file['name'];
        }
        $data['reported_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Felanmälan har skapats.');
        return $this->redirect($response, '/maintenance/faults');
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $report = $this->repo->find((int) $args['id']);
        if (!$report) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }
        return $this->render($response, 'maintenance/faults/show', [
            'title'  => $report['report_number'] . ' – ZYNC ERP',
            'report' => $report,
        ]);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $report = $this->repo->find((int) $args['id']);
        if (!$report) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }
        return $this->render($response, 'maintenance/faults/form', [
            'title'     => 'Redigera felanmälan – ZYNC ERP',
            'report'    => $report,
            'equipment' => $this->repo->allEquipment(),
            'errors'    => [],
            'isNew'     => false,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $report = $this->repo->find($id);
        if (!$report) {
            Flash::set('error', 'Felanmälan hittades inte.');
            return $this->redirect($response, '/maintenance/faults');
        }

        $data   = $this->parseBody($request);
        $file   = $this->handleUpload($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'maintenance/faults/form', [
                'title'     => 'Redigera felanmälan – ZYNC ERP',
                'report'    => array_merge($report, $data),
                'equipment' => $this->repo->allEquipment(),
                'errors'    => $errors,
                'isNew'     => false,
            ]);
        }

        if ($file) {
            if ($report['image_path'] && file_exists(__DIR__ . '/../../public' . $report['image_path'])) {
                unlink(__DIR__ . '/../../public' . $report['image_path']);
            }
            $data['image_path'] = $file['path'];
            $data['image_name'] = $file['name'];
        } else {
            $data['image_path'] = $report['image_path'];
            $data['image_name'] = $report['image_name'];
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Felanmälan har uppdaterats.');
        return $this->redirect($response, '/maintenance/faults/' . $id);
    }

    public function updateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $this->repo->updateStatus($id, $body['status'], Auth::id());
        Flash::set('success', 'Status uppdaterad.');
        return $this->redirect($response, '/maintenance/faults/' . $id);
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Felanmälan har tagits bort.');
        return $this->redirect($response, '/maintenance/faults');
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'report_number' => trim((string) ($b['report_number'] ?? '')),
            'equipment_id'  => (int) ($b['equipment_id'] ?? 0),
            'title'         => trim((string) ($b['title'] ?? '')),
            'description'   => trim((string) ($b['description'] ?? '')),
            'fault_type'    => trim((string) ($b['fault_type'] ?? 'other')),
            'priority'      => trim((string) ($b['priority'] ?? 'medium')),
            'status'        => trim((string) ($b['status'] ?? 'reported')),
            'notes'         => trim((string) ($b['notes'] ?? '')),
            'image_path'    => null,
            'image_name'    => null,
        ];
    }

    private function handleUpload(ServerRequestInterface $request): ?array
    {
        $files = $request->getUploadedFiles();
        $file  = $files['fault_image'] ?? null;
        if (!$file || $file->getError() !== UPLOAD_ERR_OK) return null;
        $ext      = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $safeName = uniqid('fault_', true) . '.' . strtolower($ext);
        $file->moveTo(__DIR__ . '/../../public/uploads/faults/' . $safeName);
        return ['path' => '/uploads/faults/' . $safeName, 'name' => $file->getClientFilename()];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['equipment_id'] < 1) $errors['equipment_id'] = 'Välj utrustning.';
        if ($data['title'] === '')      $errors['title']        = 'Rubrik krävs.';
        if ($data['description'] === '') $errors['description'] = 'Beskrivning krävs.';
        return $errors;
    }
}
