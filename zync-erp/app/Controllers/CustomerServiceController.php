<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\CustomerServiceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomerServiceController extends Controller
{
    private CustomerServiceRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new CustomerServiceRepository();
    }

    /** GET /cs */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $allOpen = array_filter($this->repo->allTickets(), static fn($t) => $t['status'] === 'open');
        return $this->render($response, 'cs/dashboard', [
            'title'          => 'Kundtjänst – ZYNC ERP',
            'stats'          => $this->repo->stats(),
            'recent_tickets' => array_slice(array_values($allOpen), 0, 5),
        ]);
    }

    /** GET /cs/tickets */
    public function tickets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'cs/tickets/index', [
            'title'   => 'Alla ärenden – ZYNC ERP',
            'tickets' => $this->repo->allTickets(),
        ]);
    }

    /** GET /cs/tickets/my */
    public function myTickets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'cs/tickets/my', [
            'title'   => 'Mina ärenden – ZYNC ERP',
            'tickets' => $this->repo->myTickets((int) Auth::id()),
        ]);
    }

    /** GET /cs/tickets/create */
    public function createTicket(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'cs/tickets/create', [
            'title'     => 'Nytt ärende – ZYNC ERP',
            'customers' => $this->repo->allCustomers(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
            'old'       => [],
        ]);
    }

    /** POST /cs/tickets */
    public function storeTicket(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data   = $this->extractTicketData($request);
        $errors = $this->validateTicket($data);

        if (!empty($errors)) {
            return $this->render($response, 'cs/tickets/create', [
                'title'     => 'Nytt ärende – ZYNC ERP',
                'customers' => $this->repo->allCustomers(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
                'old'       => $data,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->repo->createTicket($data);
        Flash::set('success', 'Ärendet skapades.');
        return $this->redirect($response, '/cs/tickets/' . $id);
    }

    /** GET /cs/tickets/{id} */
    public function showTicket(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ticket = $this->repo->findTicket((int) $args['id']);
        if ($ticket === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'cs/tickets/show', [
            'title'    => htmlspecialchars($ticket['ticket_number'], ENT_QUOTES, 'UTF-8') . ' – ZYNC ERP',
            'ticket'   => $ticket,
            'comments' => $this->repo->ticketComments((int) $args['id']),
        ]);
    }

    /** GET /cs/tickets/{id}/edit */
    public function editTicket(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $ticket = $this->repo->findTicket((int) $args['id']);
        if ($ticket === null) {
            return $this->notFound($response);
        }

        return $this->render($response, 'cs/tickets/edit', [
            'title'     => 'Redigera ärende – ZYNC ERP',
            'ticket'    => $ticket,
            'customers' => $this->repo->allCustomers(),
            'users'     => $this->repo->allUsers(),
            'errors'    => [],
        ]);
    }

    /** POST /cs/tickets/{id} */
    public function updateTicket(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $ticket = $this->repo->findTicket($id);
        if ($ticket === null) {
            return $this->notFound($response);
        }

        $data   = $this->extractTicketData($request);
        $errors = $this->validateTicket($data);

        if (!empty($errors)) {
            return $this->render($response, 'cs/tickets/edit', [
                'title'     => 'Redigera ärende – ZYNC ERP',
                'ticket'    => array_merge($ticket, $data),
                'customers' => $this->repo->allCustomers(),
                'users'     => $this->repo->allUsers(),
                'errors'    => $errors,
            ]);
        }

        $this->repo->updateTicket($id, $data);
        Flash::set('success', 'Ärendet uppdaterades.');
        return $this->redirect($response, '/cs/tickets/' . $id);
    }

    /** POST /cs/tickets/{id}/status */
    public function updateTicketStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id     = (int) $args['id'];
        $body   = (array) $request->getParsedBody();
        $status = trim((string) ($body['status'] ?? ''));

        $allowed = ['open', 'in_progress', 'waiting_customer', 'waiting_internal', 'resolved', 'closed'];
        if (in_array($status, $allowed, true)) {
            $this->repo->updateTicketStatus($id, $status);
            Flash::set('success', 'Status uppdaterades.');
        }

        return $this->redirect($response, '/cs/tickets/' . $id);
    }

    /** POST /cs/tickets/{id}/comments */
    public function addComment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id      = (int) $args['id'];
        $body    = (array) $request->getParsedBody();
        $comment = trim((string) ($body['comment'] ?? ''));
        $isInternal = !empty($body['is_internal']);

        if ($comment !== '') {
            $this->repo->addComment($id, (int) Auth::id(), $comment, $isInternal);
            Flash::set('success', 'Kommentar tillagd.');
        }

        return $this->redirect($response, '/cs/tickets/' . $id);
    }

    /** POST /cs/tickets/{id}/delete */
    public function deleteTicket(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->repo->deleteTicket((int) $args['id']);
        Flash::set('success', 'Ärendet togs bort.');
        return $this->redirect($response, '/cs/tickets');
    }

    /** @return array<string, string> */
    private function extractTicketData(ServerRequestInterface $request): array
    {
        $body = (array) $request->getParsedBody();
        return [
            'title'          => trim((string) ($body['title']          ?? '')),
            'description'    => trim((string) ($body['description']    ?? '')),
            'customer_id'    => trim((string) ($body['customer_id']    ?? '')),
            'contact_person' => trim((string) ($body['contact_person'] ?? '')),
            'contact_email'  => trim((string) ($body['contact_email']  ?? '')),
            'contact_phone'  => trim((string) ($body['contact_phone']  ?? '')),
            'category'       => trim((string) ($body['category']       ?? 'inquiry')),
            'priority'       => trim((string) ($body['priority']       ?? 'normal')),
            'status'         => trim((string) ($body['status']         ?? 'open')),
            'assigned_to'    => trim((string) ($body['assigned_to']    ?? '')),
            'resolution'     => trim((string) ($body['resolution']     ?? '')),
        ];
    }

    /** @return array<string, string> */
    private function validateTicket(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') {
            $errors['title'] = 'Titel är obligatorisk.';
        }
        return $errors;
    }

}
