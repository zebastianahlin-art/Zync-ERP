<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\CustomerRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomerController extends Controller
{
    private CustomerRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new CustomerRepository();
    }

    /** GET /customers */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'customers/index', [
            'title'     => 'Kunder – ZYNC ERP',
            'customers' => $this->repo->all(),
            'success'   => Flash::get('success'),
        ]);
    }

    /** GET /customers/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        return $this->render($response, 'customers/create', [
            'title'  => 'Ny kund – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /customers */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'customers/create', [
                'title'  => 'Ny kund – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $this->repo->create($data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'customers/create', [
                'title'  => 'Ny kund – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        Flash::set('success', 'Kunden har skapats.');
        return $this->redirect($response, '/customers');
    }

    /** GET /customers/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $customer = $this->repo->find((int) $args['id']);
        if ($customer === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'customers/edit', [
            'title'    => 'Redigera kund – ZYNC ERP',
            'customer' => $customer,
            'errors'   => [],
        ]);
    }

    /** POST /customers/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id       = (int) $args['id'];
        $customer = $this->repo->find($id);
        if ($customer === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'customers/edit', [
                'title'    => 'Redigera kund – ZYNC ERP',
                'customer' => $customer,
                'errors'   => $errors,
            ]);
        }

        try {
            $this->repo->update($id, $data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'customers/edit', [
                'title'    => 'Redigera kund – ZYNC ERP',
                'customer' => $customer,
                'errors'   => $errors,
            ]);
        }

        Flash::set('success', 'Kunden har uppdaterats.');
        return $this->redirect($response, '/customers');
    }

    /** POST /customers/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!Auth::check()) {
            return $this->redirect($response, '/login');
        }

        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Kunden har tagits bort.');
        }

        return $this->redirect($response, '/customers');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'name'       => trim((string) ($body['name'] ?? '')),
            'org_number' => trim((string) ($body['org_number'] ?? '')),
            'email'      => trim((string) ($body['email'] ?? '')),
            'phone'      => trim((string) ($body['phone'] ?? '')),
            'address'    => trim((string) ($body['address'] ?? '')),
        ];
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        }
        if ($data['org_number'] === '') {
            $errors['org_number'] = 'Organisationsnummer är obligatoriskt.';
        }
        if ($data['email'] === '') {
            $errors['email'] = 'E-postadress är obligatorisk.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ogiltig e-postadress.';
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
        if (str_contains($message, 'customers_email_unique') || str_contains($message, "'email'")) {
            $errors['email'] = 'E-postadressen används redan.';
        } elseif (str_contains($message, 'customers_org_number_unique') || str_contains($message, "'org_number'")) {
            $errors['org_number'] = 'Organisationsnumret är redan registrerat.';
        } else {
            $errors['general'] = 'En dubblettpost hittades. Kontrollera dina uppgifter.';
        }
        return $errors;
    }

}
