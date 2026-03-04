<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\CertificateRepository;
use App\Models\EmployeeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CertificateController extends Controller
{
    private CertificateRepository $repo;
    private EmployeeRepository $empRepo;

    public function __construct()
    {
        parent::__construct();
        $this->repo    = new CertificateRepository();
        $this->empRepo = new EmployeeRepository();
    }

    /** GET /certificates */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'certificates/index', [
            'title'        => 'Certifikat – ZYNC ERP',
            'certificates' => $this->repo->all(),
            'success'      => Flash::get('success'),
        ]);
    }

    /** GET /certificates/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'certificates/create', [
            'title'     => 'Nytt certifikat – ZYNC ERP',
            'employees' => $this->empRepo->all(),
            'types'     => $this->repo->allTypes(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /certificates */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'certificates/create', [
                'title'     => 'Nytt certifikat – ZYNC ERP',
                'employees' => $this->empRepo->all(),
                'types'     => $this->repo->allTypes(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $this->repo->create($data);
        Flash::set('success', 'Certifikatet skapades.');
        return $this->redirect($response, '/certificates');
    }

    /** GET /certificates/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $cert = $this->repo->find((int) $args['id']);
        if ($cert === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'certificates/edit', [
            'title'       => 'Redigera certifikat – ZYNC ERP',
            'certificate' => $cert,
            'employees'   => $this->empRepo->all(),
            'types'       => $this->repo->allTypes(),
            'errors'      => [],
        ]);
    }

    /** POST /certificates/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id   = (int) $args['id'];
        $cert = $this->repo->find($id);
        if ($cert === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'certificates/edit', [
                'title'       => 'Redigera certifikat – ZYNC ERP',
                'certificate' => $cert,
                'employees'   => $this->empRepo->all(),
                'types'       => $this->repo->allTypes(),
                'errors'      => $errors,
            ]);
        }

        $this->repo->update($id, $data);
        Flash::set('success', 'Certifikatet uppdaterades.');
        return $this->redirect($response, '/certificates');
    }

    /** POST /certificates/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Certifikatet togs bort.');
        }

        return $this->redirect($response, '/certificates');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'employee_id'         => trim((string) ($body['employee_id'] ?? '')),
            'certificate_type_id' => trim((string) ($body['certificate_type_id'] ?? '')),
            'issued_date'         => trim((string) ($body['issued_date'] ?? '')),
            'expiry_date'         => trim((string) ($body['expiry_date'] ?? '')),
            'file_path'           => trim((string) ($body['file_path'] ?? '')),
            'notes'               => trim((string) ($body['notes'] ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validate(array $data): array
    {
        $errors = [];
        if ($data['employee_id'] === '') {
            $errors['employee_id'] = 'Personal är obligatoriskt.';
        }
        if ($data['certificate_type_id'] === '') {
            $errors['certificate_type_id'] = 'Certifikattyp är obligatorisk.';
        }
        if ($data['issued_date'] === '') {
            $errors['issued_date'] = 'Utfärdandedatum är obligatoriskt.';
        }
        return $errors;
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Certifikat hittades inte</h1>');
        return $response->withStatus(404);
    }
}
