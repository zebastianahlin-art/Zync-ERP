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
        return $this->render($response, 'certificates/index', [
            'title'        => 'Certifikat – ZYNC ERP',
            'certificates' => $this->repo->all(),
            'success'      => Flash::get('success'),
        ]);
    }

    /** GET /certificates/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
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
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Certifikatet togs bort.');
        }

        return $this->redirect($response, '/certificates');
    }

    /** POST /certificates/{id}/renew */
    public function renew(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id   = (int) $args['id'];
        $cert = $this->repo->find($id);
        if ($cert === null) {
            return $this->notFound($response);
        }

        $body = (array) $request->getParsedBody();
        $data = [
            'issued_date' => trim((string) ($body['issued_date'] ?? date('Y-m-d'))),
            'expiry_date' => trim((string) ($body['expiry_date'] ?? '')),
            'notes'       => trim((string) ($body['notes'] ?? '')),
            'created_by'  => Auth::id(),
        ];

        try {
            $newId = $this->repo->renew($id, $data);
            Flash::set('success', 'Certifikatet förnyades.');
            return $this->redirect($response, '/certificates/' . $newId . '/edit');
        } catch (\Exception $e) {
            Flash::set('success', 'Kunde inte förnya certifikatet.');
            return $this->redirect($response, '/certificates');
        }
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

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $cert = $this->repo->find((int) $args['id']);
        if ($cert === null) {
            return $this->notFound($response);
        }
        return $this->render($response, 'certificates/show', [
            'title'       => 'Certifikat – ZYNC ERP',
            'certificate' => $cert,
        ]);
    }

    public function expiring(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $days = max(1, (int) ($params['days'] ?? 30));
        return $this->render($response, 'certificates/expiring', [
            'title'        => 'Certifikat som löper ut – ZYNC ERP',
            'certificates' => $this->repo->expiringCertificates($days),
            'days'         => $days,
        ]);
    }

    public function expired(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'certificates/expired', [
            'title'        => 'Utgångna certifikat – ZYNC ERP',
            'certificates' => $this->repo->expiredCertificates(),
        ]);
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

}
