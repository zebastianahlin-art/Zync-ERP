<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\SupplierRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SupplierController extends Controller
{
    private SupplierRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new SupplierRepository();
    }

    /** GET /suppliers */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'suppliers/index', [
            'title'     => 'Leverantörer – ZYNC ERP',
            'suppliers' => $this->repo->all(),
            'success'   => Flash::get('success'),
        ]);
    }

    /** GET /suppliers/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'suppliers/create', [
            'title'  => 'Ny leverantör – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /suppliers */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'suppliers/create', [
                'title'  => 'Ny leverantör – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $this->repo->create($data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'suppliers/create', [
                'title'  => 'Ny leverantör – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        Flash::set('success', 'Leverantören har skapats.');
        return $this->redirect($response, '/suppliers');
    }

    /** GET /suppliers/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $supplier = $this->repo->find((int) $args['id']);
        if ($supplier === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'suppliers/edit', [
            'title'    => 'Redigera leverantör – ZYNC ERP',
            'supplier' => $supplier,
            'errors'   => [],
        ]);
    }

    /** POST /suppliers/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id       = (int) $args['id'];
        $supplier = $this->repo->find($id);
        if ($supplier === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'suppliers/edit', [
                'title'    => 'Redigera leverantör – ZYNC ERP',
                'supplier' => $supplier,
                'errors'   => $errors,
            ]);
        }

        try {
            $this->repo->update($id, $data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'suppliers/edit', [
                'title'    => 'Redigera leverantör – ZYNC ERP',
                'supplier' => $supplier,
                'errors'   => $errors,
            ]);
        }

        Flash::set('success', 'Leverantören har uppdaterats.');
        return $this->redirect($response, '/suppliers');
    }

    /** POST /suppliers/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Leverantören har tagits bort.');
        }

        return $this->redirect($response, '/suppliers');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'           => trim((string) ($body['name'] ?? '')),
            'org_number'     => trim((string) ($body['org_number'] ?? '')),
            'email'          => trim((string) ($body['email'] ?? '')),
            'phone'          => trim((string) ($body['phone'] ?? '')),
            'address'        => trim((string) ($body['address'] ?? '')),
            'city'           => trim((string) ($body['city'] ?? '')),
            'postal_code'    => trim((string) ($body['postal_code'] ?? '')),
            'country'        => trim((string) ($body['country'] ?? 'Sverige')),
            'contact_person' => trim((string) ($body['contact_person'] ?? '')),
            'website'        => trim((string) ($body['website'] ?? '')),
            'notes'          => trim((string) ($body['notes'] ?? '')),
            'is_active'      => isset($body['is_active']) ? '1' : '0',
        ];
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function validate(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        } elseif (mb_strlen($data['name']) > 255) {
            $errors['name'] = 'Namn får inte vara längre än 255 tecken.';
        }

        if ($data['org_number'] === '') {
            $errors['org_number'] = 'Organisationsnummer är obligatoriskt.';
        } elseif ($this->repo->orgNumberExists($data['org_number'], $excludeId)) {
            $errors['org_number'] = 'Det här organisationsnumret är redan registrerat.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'E-postadress är obligatorisk.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-postadressen är ogiltig.';
        } elseif ($this->repo->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'Den här e-postadressen används redan.';
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function handleUniqueViolation(\PDOException $e, array $errors): array
    {
        $message = $e->getMessage();
        if (str_contains($message, 'idx_suppliers_email') || str_contains($message, "'email'")) {
            $errors['email'] = 'Den här e-postadressen används redan.';
        } elseif (str_contains($message, 'idx_suppliers_org_number') || str_contains($message, "'org_number'")) {
            $errors['org_number'] = 'Det här organisationsnumret är redan registrerat.';
        } else {
            $errors['general'] = 'En dubblettpost hittades. Kontrollera dina uppgifter.';
        }
        return $errors;
    }

    private function notFound(ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write('<h1>404 – Leverantören hittades inte</h1>');
        return $response->withStatus(404);
    }
}
