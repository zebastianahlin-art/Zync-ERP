<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\CertificateRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CertificateController extends Controller
{
    private CertificateRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new CertificateRepository();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $p = $request->getQueryParams();
        $this->repo->refreshStatuses();

        return $this->render($response, 'certificates/index', [
            'title'        => 'Certifikat – ZYNC ERP',
            'certificates' => $this->repo->all(
                isset($p['employee']) ? (int) $p['employee'] : null,
                $p['status'] ?? null,
                $p['type'] ?? null
            ),
            'stats'     => $this->repo->stats(),
            'employees' => $this->repo->allEmployees(),
            'types'     => $this->repo->allTypes(),
            'filter'    => $p,
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'certificates/form', [
            'title'     => 'Nytt certifikat – ZYNC ERP',
            'cert'      => ['status' => 'active'],
            'employees' => $this->repo->allEmployees(),
            'types'     => $this->repo->allTypes(),
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
            return $this->render($response, 'certificates/form', [
                'title'     => 'Nytt certifikat – ZYNC ERP',
                'cert'      => $data,
                'employees' => $this->repo->allEmployees(),
                'types'     => $this->repo->allTypes(),
                'errors'    => $errors,
                'isNew'     => true,
            ]);
        }

        if ($file) {
            $data['file_path'] = $file['path'];
            $data['file_name'] = $file['name'];
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Certifikat har skapats.');
        return $this->redirect($response, '/certificates');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cert = $this->repo->find((int) $args['id']);
        if (!$cert) {
            Flash::set('error', 'Certifikat hittades inte.');
            return $this->redirect($response, '/certificates');
        }

        return $this->render($response, 'certificates/form', [
            'title'     => 'Redigera certifikat – ZYNC ERP',
            'cert'      => $cert,
            'employees' => $this->repo->allEmployees(),
            'types'     => $this->repo->allTypes(),
            'errors'    => [],
            'isNew'     => false,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $cert = $this->repo->find($id);
        if (!$cert) {
            Flash::set('error', 'Certifikat hittades inte.');
            return $this->redirect($response, '/certificates');
        }

        $data   = $this->parseBody($request);
        $file   = $this->handleUpload($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'certificates/form', [
                'title'     => 'Redigera certifikat – ZYNC ERP',
                'cert'      => array_merge($cert, $data),
                'employees' => $this->repo->allEmployees(),
                'types'     => $this->repo->allTypes(),
                'errors'    => $errors,
                'isNew'     => false,
            ]);
        }

        if ($file) {
            // Ta bort gammal fil
            if ($cert['file_path'] && file_exists(__DIR__ . '/../../public' . $cert['file_path'])) {
                unlink(__DIR__ . '/../../public' . $cert['file_path']);
            }
            $data['file_path'] = $file['path'];
            $data['file_name'] = $file['name'];
        } else {
            $data['file_path'] = $cert['file_path'];
            $data['file_name'] = $cert['file_name'];
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Certifikat har uppdaterats.');
        return $this->redirect($response, '/certificates');
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cert = $this->repo->find((int) $args['id']);
        if ($cert && $cert['file_path'] && file_exists(__DIR__ . '/../../public' . $cert['file_path'])) {
            unlink(__DIR__ . '/../../public' . $cert['file_path']);
        }
        $this->repo->delete((int) $args['id']);
        Flash::set('success', 'Certifikat har tagits bort.');
        return $this->redirect($response, '/certificates');
    }

    /** Visa fil. */
    public function download(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cert = $this->repo->find((int) $args['id']);
        if (!$cert || !$cert['file_path']) {
            Flash::set('error', 'Ingen fil hittades.');
            return $this->redirect($response, '/certificates');
        }

        $fullPath = __DIR__ . '/../../public' . $cert['file_path'];
        if (!file_exists($fullPath)) {
            Flash::set('error', 'Filen finns inte på servern.');
            return $this->redirect($response, '/certificates');
        }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        $response = $response
            ->withHeader('Content-Type', $mime)
            ->withHeader('Content-Disposition', 'inline; filename="' . $cert['file_name'] . '"')
            ->withHeader('Content-Length', (string) filesize($fullPath));
        $response->getBody()->write(file_get_contents($fullPath));
        return $response;
    }

    private function parseBody(ServerRequestInterface $request): array
    {
        $b = (array) $request->getParsedBody();
        return [
            'employee_id'        => (int) ($b['employee_id'] ?? 0),
            'name'               => trim((string) ($b['name'] ?? '')),
            'type'               => trim((string) ($b['type'] ?? '')),
            'issuer'             => trim((string) ($b['issuer'] ?? '')),
            'certificate_number' => trim((string) ($b['certificate_number'] ?? '')),
            'issued_date'        => trim((string) ($b['issued_date'] ?? '')),
            'expiry_date'        => trim((string) ($b['expiry_date'] ?? '')),
            'notes'              => trim((string) ($b['notes'] ?? '')),
            'status'             => trim((string) ($b['status'] ?? 'active')),
            'file_path'          => null,
            'file_name'          => null,
        ];
    }

    private function handleUpload(ServerRequestInterface $request): ?array
    {
        $files = $request->getUploadedFiles();
        $file  = $files['certificate_file'] ?? null;

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        $mime    = $file->getClientMediaType();
        if (!in_array($mime, $allowed)) {
            return null;
        }

        $ext      = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $safeName = uniqid('cert_', true) . '.' . strtolower($ext);
        $destDir  = __DIR__ . '/../../public/uploads/certificates';
        $file->moveTo($destDir . '/' . $safeName);

        return [
            'path' => '/uploads/certificates/' . $safeName,
            'name' => $file->getClientFilename(),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['employee_id'] < 1) $errors['employee_id'] = 'Välj en anställd.';
        if ($data['name'] === '')      $errors['name']        = 'Certifikatnamn är obligatoriskt.';
        return $errors;
    }
}
