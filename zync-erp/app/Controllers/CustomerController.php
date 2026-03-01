<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Models\CustomerRepository;

class CustomerController extends Controller
{
    private CustomerRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new CustomerRepository();
    }

    /** GET /customers */
    public function index(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        return $this->render('customers/index', [
            'title'     => 'Customers – ZYNC ERP',
            'customers' => $this->repo->all(),
            'success'   => Flash::get('success'),
        ]);
    }

    /** GET /customers/create */
    public function create(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        return $this->render('customers/create', [
            'title'  => 'New Customer – ZYNC ERP',
            'errors' => [],
            'old'    => [],
        ]);
    }

    /** POST /customers */
    public function store(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render('customers/create', [
                'title'  => 'New Customer – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        try {
            $this->repo->create($data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render('customers/create', [
                'title'  => 'New Customer – ZYNC ERP',
                'errors' => $errors,
                'old'    => $data,
            ]);
        }

        Flash::set('success', 'Customer created successfully.');
        return $this->redirect('/customers');
    }

    /** GET /customers/{id}/edit */
    public function edit(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $customer = $this->repo->find((int) $request->params['id']);
        if ($customer === null) {
            return $this->notFound();
        }

        return $this->render('customers/edit', [
            'title'    => 'Edit Customer – ZYNC ERP',
            'customer' => $customer,
            'errors'   => [],
        ]);
    }

    /** POST /customers/{id} */
    public function update(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $id       = (int) $request->params['id'];
        $customer = $this->repo->find($id);
        if ($customer === null) {
            return $this->notFound();
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render('customers/edit', [
                'title'    => 'Edit Customer – ZYNC ERP',
                'customer' => $customer,
                'errors'   => $errors,
            ]);
        }

        try {
            $this->repo->update($id, $data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render('customers/edit', [
                'title'    => 'Edit Customer – ZYNC ERP',
                'customer' => $customer,
                'errors'   => $errors,
            ]);
        }

        Flash::set('success', 'Customer updated successfully.');
        return $this->redirect('/customers');
    }

    /** POST /customers/{id}/delete */
    public function destroy(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect('/login');
        }

        $id = (int) $request->params['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Customer deleted.');
        }

        return $this->redirect('/customers');
    }

    /** @return array<string, string> */
    private function extractData(Request $request): array
    {
        return [
            'name'       => trim((string) $request->input('name', '')),
            'org_number' => trim((string) $request->input('org_number', '')),
            'email'      => trim((string) $request->input('email', '')),
            'phone'      => trim((string) $request->input('phone', '')),
            'address'    => trim((string) $request->input('address', '')),
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
            $errors['name'] = 'Name is required.';
        }
        if ($data['org_number'] === '') {
            $errors['org_number'] = 'Organisation number is required.';
        }
        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email must be a valid email address.';
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
            $errors['email'] = 'This email address is already in use.';
        } elseif (str_contains($message, 'customers_org_number_unique') || str_contains($message, "'org_number'")) {
            $errors['org_number'] = 'This organisation number is already registered.';
        } else {
            $errors['general'] = 'A duplicate entry was detected. Please check your input.';
        }
        return $errors;
    }

    private function notFound(): Response
    {
        return $this->response->html('<h1>404 – Customer Not Found</h1>', 404);
    }
}
