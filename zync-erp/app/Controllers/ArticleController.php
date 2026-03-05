<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Models\ArticleRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ArticleController extends Controller
{
    private ArticleRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ArticleRepository();
    }

    /** GET /articles */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'articles/index', [
            'title'    => 'Artiklar – ZYNC ERP',
            'articles' => $this->repo->all(),
            'success'  => Flash::get('success'),
        ]);
    }

    /** GET /articles/create */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'articles/create', [
            'title'     => 'Ny artikel – ZYNC ERP',
            'errors'    => [],
            'old'       => [],
            'suppliers' => $this->repo->allSuppliers(),
        ]);
    }

    /** POST /articles */
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractData($request);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $this->render($response, 'articles/create', [
                'title'     => 'Ny artikel – ZYNC ERP',
                'errors'    => $errors,
                'old'       => $data,
                'suppliers' => $this->repo->allSuppliers(),
            ]);
        }

        try {
            $this->repo->create($data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'articles/create', [
                'title'     => 'Ny artikel – ZYNC ERP',
                'errors'    => $errors,
                'old'       => $data,
                'suppliers' => $this->repo->allSuppliers(),
            ]);
        }

        Flash::set('success', 'Artikeln har skapats.');
        return $this->redirect($response, '/articles');
    }

    /** GET /articles/{id}/edit */
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $article = $this->repo->find((int) $args['id']);
        if ($article === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'articles/edit', [
            'title'     => 'Redigera artikel – ZYNC ERP',
            'article'   => $article,
            'errors'    => [],
            'suppliers' => $this->repo->allSuppliers(),
        ]);
    }

    /** POST /articles/{id} */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $article = $this->repo->find($id);
        if ($article === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractData($request);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            return $this->render($response, 'articles/edit', [
                'title'     => 'Redigera artikel – ZYNC ERP',
                'article'   => $article,
                'errors'    => $errors,
                'suppliers' => $this->repo->allSuppliers(),
            ]);
        }

        try {
            $this->repo->update($id, $data);
        } catch (\PDOException $e) {
            $errors = $this->handleUniqueViolation($e, $errors);
            return $this->render($response, 'articles/edit', [
                'title'     => 'Redigera artikel – ZYNC ERP',
                'article'   => $article,
                'errors'    => $errors,
                'suppliers' => $this->repo->allSuppliers(),
            ]);
        }

        Flash::set('success', 'Artikeln har uppdaterats.');
        return $this->redirect($response, '/articles');
    }

    /** POST /articles/{id}/delete */
    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];
        if ($this->repo->find($id) !== null) {
            $this->repo->delete($id);
            Flash::set('success', 'Artikeln har tagits bort.');
        }

        return $this->redirect($response, '/articles');
    }

    /** @return array<string, string> */
    private function extractData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'article_number' => trim((string) ($body['article_number'] ?? '')),
            'name'           => trim((string) ($body['name'] ?? '')),
            'description'    => trim((string) ($body['description'] ?? '')),
            'unit'           => trim((string) ($body['unit'] ?? '')),
            'purchase_price' => trim((string) ($body['purchase_price'] ?? '')),
            'selling_price'  => trim((string) ($body['selling_price'] ?? '')),
            'vat_rate'       => trim((string) ($body['vat_rate'] ?? '')),
            'category'       => trim((string) ($body['category'] ?? '')),
            'supplier_id'    => trim((string) ($body['supplier_id'] ?? '')),
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

        if ($data['article_number'] === '') {
            $errors['article_number'] = 'Artikelnummer är obligatoriskt.';
        } elseif ($this->repo->articleNumberExists($data['article_number'], $excludeId)) {
            $errors['article_number'] = 'Det här artikelnumret används redan.';
        }

        if ($data['name'] === '') {
            $errors['name'] = 'Namn är obligatoriskt.';
        } elseif (mb_strlen($data['name']) > 255) {
            $errors['name'] = 'Namn får inte vara längre än 255 tecken.';
        }

        $validUnits = ['st', 'kg', 'm', 'm²', 'm³', 'l', 'tim', 'paket'];
        if ($data['unit'] === '') {
            $errors['unit'] = 'Enhet är obligatorisk.';
        } elseif (!in_array($data['unit'], $validUnits, true)) {
            $errors['unit'] = 'Ogiltig enhet.';
        }

        if ($data['selling_price'] === '') {
            $errors['selling_price'] = 'Försäljningspris är obligatoriskt.';
        } elseif (!is_numeric($data['selling_price']) || (float) $data['selling_price'] < 0) {
            $errors['selling_price'] = 'Försäljningspris måste vara ett tal större än eller lika med 0.';
        }

        if ($data['purchase_price'] !== '' && (!is_numeric($data['purchase_price']) || (float) $data['purchase_price'] < 0)) {
            $errors['purchase_price'] = 'Inköpspris måste vara ett tal större än eller lika med 0.';
        }

        $validVatRates = ['25.00', '12.00', '6.00', '0.00'];
        if ($data['vat_rate'] === '') {
            $errors['vat_rate'] = 'Momssats är obligatorisk.';
        } elseif (!in_array(number_format((float) $data['vat_rate'], 2, '.', ''), $validVatRates, true)) {
            $errors['vat_rate'] = 'Ogiltig momssats.';
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
        if (str_contains($message, 'idx_articles_number') || str_contains($message, "'article_number'")) {
            $errors['article_number'] = 'Det här artikelnumret används redan.';
        } else {
            $errors['general'] = 'En dubblettpost hittades. Kontrollera dina uppgifter.';
        }
        return $errors;
    }

}
